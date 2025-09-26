<?php
// PropEx/api/get_all_properties.php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../includes/function.php';

global $conn;

try {
    // The stored procedure only accepts a LIMIT parameter, not an offset.
    $limit = $_GET['limit'] ?? 12;
    if ($limit > 50) {
        $limit = 50; // Prevent excessively large requests
    }

    $sql = "CALL sp_get_random_listings_for_users(?)";
    $stmt = $conn->prepare($sql);
    
    // Bind only the p_limit parameter as defined in the stored procedure.
    $stmt->bind_param("i", $limit);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $listings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

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
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Listings fetched successfully.', 'data' => ['listings' => $listings]]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

if ($conn && $conn->ping()) {
    $conn->close();
}
?>