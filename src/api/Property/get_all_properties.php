<?php
// PropEx/api/get_all_properties.php
// Disable error display to prevent HTML output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Start output buffering to catch any errors
if (ob_get_level() == 0) {
    ob_start();
}

// Set headers first before any output
header('Content-Type: application/json; charset=utf-8', true);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

ini_set('session.cookie_path', '/');
session_start();

// Fix path to config.php - from UserPanel/src/api/Property/ to UserPanel/
// Normalize path separators for cross-platform compatibility
$config_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config.php';
$config_path = realpath($config_path);

if (!$config_path || !file_exists($config_path)) {
    ob_end_clean();
    http_response_code(500);
    $expected_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config.php';
    echo json_encode(['success' => false, 'message' => 'Config file not found. Expected at: ' . $expected_path . ' (resolved: ' . ($config_path ?: 'not found') . ')']);
    exit();
}

// Suppress any output from config.php
ob_start();
require_once $config_path;
$config_output = ob_get_clean();

// If config.php output anything, it's an error
if (!empty($config_output) && !empty(trim($config_output))) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Config file error: Output detected']);
    exit();
}

// Check if function.php exists, if not skip it
$function_file = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'function.php';
$function_file = realpath($function_file);
if ($function_file && file_exists($function_file)) {
    ob_start();
    require_once $function_file;
    $function_output = ob_get_clean();
    if (!empty($function_output) && !empty(trim($function_output))) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Function file error: Output detected']);
        exit();
    }
}

// Use the connection from config.php
if (!isset($conn) || !$conn || $conn->connect_error) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . ($conn ? $conn->connect_error : 'Connection not set')]);
    exit();
}

ob_end_clean();

try {
    // The stored procedure only accepts a LIMIT parameter, not an offset.
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
    if ($limit > 50) {
        $limit = 50; // Prevent excessively large requests
    }

    $sql = "SELECT 
                l.listing_id, l.property_id, l.owner_user_id, l.percentage_available, 
                l.percentage_original, l.price_total, l.status, l.created_at,
                p.property_name, p.property_type, p.description, p.location, 
                p.estimated_value, p.property_image, p.verification_status
            FROM listings l
            JOIN properties p ON p.property_id = l.property_id
            WHERE l.status IN ('Active', 'Partially_Fulfilled')
            ORDER BY RAND()
            LIMIT ?";
    
    if (!$stmt = $conn->prepare($sql)) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->bind_param("i", $limit)) {
        throw new Exception("Bind param failed: " . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Get result failed: " . $stmt->error);
    }
    
    $listings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // If no listings, return empty array
    if (!is_array($listings)) {
        $listings = [];
    }

    // Loop through the listings and trim the decimal values
    foreach ($listings as &$listing) {
        // Check if price_total is a valid number and remove trailing .0
        if (isset($listing['price_total'])) {
            $value = floatval($listing['price_total']);
            // If the value has no decimal part, cast it to an integer
            if ($value == intval($value)) {
                $listing['price_total'] = intval($value);
            } else {
                $listing['price_total'] = $value;
            }
        }
        
        // Do the same for estimated_value
        if (isset($listing['estimated_value'])) {
            $value = floatval($listing['estimated_value']);
            if ($value == intval($value)) {
                $listing['estimated_value'] = intval($value);
            } else {
                $listing['estimated_value'] = $value;
            }
        }

        // Also, trim the percentage_available and percentage_original
        if (isset($listing['percentage_available'])) {
            $value = floatval($listing['percentage_available']);
            if ($value == intval($value)) {
                $listing['percentage_available'] = intval($value);
            } else {
                // Keep the decimal if it's not .0000
                $listing['percentage_available'] = round($value, 4);
            }
        }
        
        if (isset($listing['percentage_original'])) {
            $value = floatval($listing['percentage_original']);
            if ($value == intval($value)) {
                $listing['percentage_original'] = intval($value);
            } else {
                // Keep the decimal if it's not .0000
                $listing['percentage_original'] = round($value, 4);
            }
        }
    }
    
    // Ensure no output before JSON
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Set headers again to be sure
    header('Content-Type: application/json; charset=utf-8', true);
    http_response_code(200);
    
    $response = ['success' => true, 'message' => 'Listings fetched successfully.', 'data' => ['listings' => $listings]];
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'error_details' => $e->getFile() . ':' . $e->getLine()
    ]);
    exit();
} catch (Error $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'PHP Error: ' . $e->getMessage(),
        'error_details' => $e->getFile() . ':' . $e->getLine()
    ]);
    exit();
}

// Don't close the connection as it's shared from config.php
// if ($conn && $conn->ping()) {
//     $conn->close();
// }
?>