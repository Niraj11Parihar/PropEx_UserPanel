<?php
// PropEx/api/get_user_properties.php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../includes/function.php';

global $conn;

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

try {
    $sql = "SELECT p.*, upo.ownership_percentage 
            FROM properties p
            JOIN user_property_ownership upo ON p.property_id = upo.property_id
            WHERE upo.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'User properties fetched successfully.', 'data' => ['properties' => $properties]]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>