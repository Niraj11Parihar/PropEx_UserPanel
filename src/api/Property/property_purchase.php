<?php
// PropEx/UserPanel/src/api/Property/property_purchase.php

// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['buyer_user_id']) || !isset($input['listing_id']) || !isset($input['percentage_to_buy'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit();
}

$buyer_user_id = intval($input['buyer_user_id']);
$listing_id = intval($input['listing_id']);
$percentage_to_buy = floatval($input['percentage_to_buy']);

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $buyer_user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit();
}

// Connect to database
require_once __DIR__ . '/../../../config.php';
global $conn;

try {
    // Call the purchase stored procedure
    $sql = "CALL sp_purchase_from_listing(?, ?, ?, @p_exchange_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iid", $buyer_user_id, $listing_id, $percentage_to_buy);
    $stmt->execute();
    $stmt->close();
    
    // Get the exchange ID
    $exchange_result = $conn->query("SELECT @p_exchange_id as exchange_id");
    $exchange_row = $exchange_result->fetch_assoc();
    $exchange_id = $exchange_row['exchange_id'];
    
    if (!$exchange_id) {
        throw new Exception("Purchase failed - no exchange ID generated");
    }
    
    // Simulate payment processing
    $payment_success = simulatePayment($buyer_user_id, $listing_id, $exchange_id);
    
    if (!$payment_success) {
        throw new Exception("Payment processing failed");
    }
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Purchase completed successfully!',
        'data' => ['exchange_id' => $exchange_id]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Function to simulate payment processing
function simulatePayment($buyer_id, $listing_id, $exchange_id) {
    global $conn;
    
    // Get seller ID from listing
    $seller_sql = "SELECT owner_user_id FROM listings WHERE listing_id = ?";
    $seller_stmt = $conn->prepare($seller_sql);
    $seller_stmt->bind_param("i", $listing_id);
    $seller_stmt->execute();
    $seller_result = $seller_stmt->get_result();
    $seller_row = $seller_result->fetch_assoc();
    $seller_id = $seller_row['owner_user_id'];
    
    // Get amount from exchange details
    $amount_sql = "SELECT price_transferred FROM exchange_details WHERE exchange_id = ?";
    $amount_stmt = $conn->prepare($amount_sql);
    $amount_stmt->bind_param("i", $exchange_id);
    $amount_stmt->execute();
    $amount_result = $amount_stmt->get_result();
    $amount_row = $amount_result->fetch_assoc();
    $amount = $amount_row['price_transferred'];
    
    // Record the payment in the database
    $payment_sql = "INSERT INTO payments (exchange_id, buyer_id, seller_id, amount, status, payment_method) 
                    VALUES (?, ?, ?, ?, 'completed', 'mock_payment')";
    
    $stmt = $conn->prepare($payment_sql);
    $stmt->bind_param("iiid", $exchange_id, $buyer_id, $seller_id, $amount);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}