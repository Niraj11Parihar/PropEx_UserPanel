<?php
// PropEx/api/create_listing.php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// ✅ Ensure a graceful shutdown with JSON
function handleFatalError() {
    $error = error_get_last();
    if ($error !== null && $error['type'] === E_ERROR) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Fatal server error.'
        ]);
        exit();
    }
}
register_shutdown_function('handleFatalError');

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../includes/function.php';

global $conn;

// 1. Authentication and Method Check
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please log in.'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed.'
    ]);
    exit();
}

// 2. Input Validation & Sanitization
$property_id        = intval($_POST['property_id'] ?? 0);
$percentage_to_list = floatval($_POST['percentage_to_list'] ?? 0);
// ✅ Correctly handle the optional price. Pass NULL if not provided.
$price_total        = $_POST['price_total'] === '' || !isset($_POST['price_total']) ? NULL : floatval($_POST['price_total']);


if ($property_id <= 0 || $percentage_to_list <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing or invalid required fields.'
    ]);
    exit();
}

// 3. Database Transaction with Enhanced Error Handling
try {
    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    $sql = "CALL sp_create_listing(?, ?, ?, ?, @new_listing_id)";
    if (!$stmt = $conn->prepare($sql)) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // ✅ Adjust binding types: 'i' for user_id, 'i' for property_id, 'd' for percentage, 'd' for price
    if (!$stmt->bind_param("iidd", 
        $user_id,
        $property_id,
        $percentage_to_list,
        $price_total
    )) {
        throw new Exception("Parameter binding failed: " . $stmt->error);
    }
    
    // ✅ Use a flag to check for success to handle potential stored procedure errors
    $success = $stmt->execute();
    if ($success) {
        $stmt->close();
        
        // Retrieve new listing id
        $result = $conn->query("SELECT @new_listing_id AS listing_id");
        if (!$result) {
            throw new Exception("Failed to fetch new listing ID: " . $conn->error);
        }
        $row = $result->fetch_assoc();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Listing created successfully.',
            'data'    => [
                'listing_id' => $row['listing_id'] ?? null
            ]
        ]);
    } else {
        // ✅ Better error handling for stored procedure errors (e.g., SIGNAL)
        $errorCode = $conn->errno;
        $errorMsg = $conn->error;

        // Check for specific error codes for better user messages
        if ($errorCode === 1644) { // MySQL error code for SIGNAL SQLSTATE '45000'
            $message = "Listing failed: " . $errorMsg;
        } else {
            $message = "Stored procedure execution failed: " . $errorMsg;
        }

        http_response_code(400); // Use 400 for client-side errors (invalid input)
        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

if ($conn && $conn->ping()) {
    $conn->close();
}