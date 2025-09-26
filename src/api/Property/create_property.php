<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure a graceful shutdown and return a JSON error
function handleFatalError() {
    $error = error_get_last();
    if ($error !== null && $error['type'] === E_ERROR) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Fatal server error.']);
    }
}
register_shutdown_function('handleFatalError');

session_start();

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../includes/function.php';

global $conn;

// 1. Authentication and Method Check
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
    exit();
}

// 2. File Upload and Validation
$property_image = null;
if (isset($_FILES['property_image']) && $_FILES['property_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../../../../AdminPanel/public/uploads/properties/';
    
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server failed to create image upload directory.']);
            exit();
        }
    }

    $file_name = time() . '_' . basename($_FILES['property_image']['name']);
    $target_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['property_image']['tmp_name'], $target_path)) {
        $property_image = '/uploads/properties/' . $file_name; // Relative path for the database
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file. Check folder permissions.']);
        exit();
    }
} else {
    // This is a good place to add a check for the required file
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Image upload failed. A property image is required.']);
    exit();
}

// 3. Input Validation and Sanitization
$property_name = trim($_POST['property_name'] ?? '');
// Ensure property_type is not longer than the database column (VARCHAR(50))
$property_type = substr(trim($_POST['property_type'] ?? 'Other'), 0, 50);
$description = trim($_POST['description'] ?? '');
$location = trim($_POST['location'] ?? '');
$estimated_value = floatval($_POST['estimated_value'] ?? 0);

if (empty($property_name) || empty($location) || $estimated_value <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid required fields.']);
    exit();
}

// 4. Database Transaction with Enhanced Error Handling
try {
    // Check if the database connection is valid
    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    $sql = "CALL sp_create_property(?, ?, ?, ?, ?, ?, ?, @new_property_id)";
    
    // Check if the prepare statement succeeds
    if (!$stmt = $conn->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    // Use 's' for the DECIMAL type to avoid precision issues
    if (!$stmt->bind_param("issssss", 
        $user_id,
        $property_name,
        $property_type,
        $description,
        $location,
        $estimated_value,
        $property_image
    )) {
        throw new Exception("Binding parameters failed: " . $stmt->error);
    }

    if ($stmt->execute()) {
        $stmt->close();
        
        // Get the newly created property ID
        $result = $conn->query("SELECT @new_property_id AS property_id");
        if (!$result) {
            throw new Exception("Failed to retrieve new property ID: " . $conn->error);
        }
        $row = $result->fetch_assoc();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Property listed successfully.',
            'property_id' => $row['property_id'] ?? null,
            'property_image' => $property_image
        ]);

    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to execute stored procedure: ' . $stmt->error]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
