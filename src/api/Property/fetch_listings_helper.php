<?php
// PropEx/UserPanel/src/api/Property/fetch_listings_helper.php
// Helper function to fetch listings - can be included in templates

function fetchListingsData($conn, $limit = 12) {
    try {
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
            if (isset($listing['price_total'])) {
                $value = floatval($listing['price_total']);
                if ($value == intval($value)) $listing['price_total'] = intval($value);
                else $listing['price_total'] = $value;
            }
            if (isset($listing['estimated_value'])) {
                $value = floatval($listing['estimated_value']);
                if ($value == intval($value)) $listing['estimated_value'] = intval($value);
                else $listing['estimated_value'] = $value;
            }
            if (isset($listing['percentage_available'])) {
                $value = floatval($listing['percentage_available']);
                if ($value == intval($value)) $listing['percentage_available'] = intval($value);
                else $listing['percentage_available'] = round($value, 4);
            }
        }
        
        return $listings;

    } catch (Exception $e) {
        error_log("Error fetching listings: " . $e->getMessage());
        return [];
    }
}
?>
