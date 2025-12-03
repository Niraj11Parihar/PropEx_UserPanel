<?php
// UserPanel/src/api/User/user_data.php
// Set headers first before any output
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

// Start output buffering to catch any errors
ob_start();

ini_set('session.cookie_path', '/');
ini_set('display_errors', 0); // Don't display errors, return JSON instead
error_reporting(E_ALL);

session_start();

// Fix path to config.php - from UserPanel/src/api/User/ to UserPanel/
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

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit();
}

// Helper function to mask sensitive numbers
function mask_number($number, $visibleDigits = 4)
{
    $len = strlen($number);
    if ($len <= $visibleDigits) return $number;
    return str_repeat('*', $len - $visibleDigits) . substr($number, -$visibleDigits);
}

// Helper function to handle file uploads
function handleFileUpload($file, $user_id, $type)
{
    // Simplified function to return path or false with error message in the return
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error uploading file: ' . $file['name']];
    }
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => "Invalid file type for " . $file['name'] . ". Only JPEG, PNG, WebP, and PDF files are allowed."];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => "File too large: " . $file['name'] . ". Maximum size is 5MB."];
    }
    $uploadDir = __DIR__ . '/../../../../AdminPanel/public/uploads/properties/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = $type . '_' . $user_id . '_' . time() . '.' . $file_extension;
    $file_path = 'uploads/properties/' . $unique_filename;
    $absolute_path = $uploadDir . $unique_filename;

    if (move_uploaded_file($file['tmp_name'], $absolute_path)) {
        return ['success' => true, 'path' => $file_path];
    } else {
        return ['success' => false, 'message' => "Failed to move uploaded file: " . $file['name']];
    }
}

// --- Main Logic based on Request Method ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST requests for updates
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
        exit();
    }
    
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $address = trim($_POST['address'] ?? '');

        try {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ?, address = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $full_name, $phone_number, $address, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating profile.']);
            }
            $stmt->close();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred while updating your profile.']);
        }
    } else if (isset($_POST['update_verification'])) {
        $aadhar_number = trim($_POST['aadhaar_hash'] ?? '');
        $pan_number = trim($_POST['pan_number'] ?? '');
        $aadhaar_doc_url = null;
        $pan_doc_url = null;

        if (!preg_match('/^\d{12}$/', $aadhar_number)) {
            echo json_encode(['success' => false, 'message' => "Invalid Aadhaar number. It must be 12 digits."]);
            exit();
        }
        if (!preg_match('/^[A-Z]{5}\d{4}[A-Z]{1}$/', $pan_number)) {
            echo json_encode(['success' => false, 'message' => "Invalid PAN number format."]);
            exit();
        }
        
        $stmt_fetch = $conn->prepare("SELECT aadhaar_document_url, pan_document_url FROM users WHERE user_id = ?");
        $stmt_fetch->bind_param("i", $user_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        $currentData = $result->fetch_assoc();
        $stmt_fetch->close();
        $aadhaar_doc_url = $currentData['aadhaar_document_url'];
        $pan_doc_url = $currentData['pan_document_url'];

        if (isset($_FILES['aadhaar_document']) && $_FILES['aadhaar_document']['error'] === UPLOAD_ERR_OK) {
            $upload_res = handleFileUpload($_FILES['aadhaar_document'], $user_id, 'aadhaar');
            if (!$upload_res['success']) {
                echo json_encode($upload_res);
                exit();
            }
            $aadhaar_doc_url = $upload_res['path'];
        }

        if (isset($_FILES['pan_document']) && $_FILES['pan_document']['error'] === UPLOAD_ERR_OK) {
            $upload_res = handleFileUpload($_FILES['pan_document'], $user_id, 'pan');
            if (!$upload_res['success']) {
                echo json_encode($upload_res);
                exit();
            }
            $pan_doc_url = $upload_res['path'];
        }

        try {
            $stmt = $conn->prepare("UPDATE users SET aadhaar_hash = ?, aadhaar_document_url = ?, pan_number = ?, pan_document_url = ?, identity_verification_status = 'Pending' WHERE user_id = ?");
            $stmt->bind_param("ssssi", $aadhar_number, $aadhaar_doc_url, $pan_number, $pan_doc_url, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Verification details submitted successfully! Pending approval.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error saving verification details.']);
            }
            $stmt->close();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred while saving your verification data.']);
        }
    } else if (isset($_POST['change_password'])) {
        $current_password = trim($_POST['current_password'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_new_password = trim($_POST['confirm_new_password'] ?? '');

        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            echo json_encode(['success' => false, 'message' => "All fields are required."]);
            exit();
        }
        if ($new_password !== $confirm_new_password) {
            echo json_encode(['success' => false, 'message' => "New password and confirmation do not match."]);
            exit();
        }
        if (strlen($new_password) < 8) {
            echo json_encode(['success' => false, 'message' => "New password must be at least 8 characters long."]);
            exit();
        }
        
        try {
            $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $db_hash = $row['password_hash'];
                if (password_verify($current_password, $db_hash)) {
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                    $updateStmt->bind_param("si", $new_hash, $user_id);
                    if ($updateStmt->execute()) {
                        echo json_encode(['success' => true, 'message' => "Password updated successfully!"]);
                    } else {
                        echo json_encode(['success' => false, 'message' => "Error updating password."]);
                    }
                    $updateStmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => "Your current password is incorrect."]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => "User not found."]);
            }
            $stmt->close();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred while updating your password.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid POST action.']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests to fetch data
    try {
        $stmt = $conn->prepare("SELECT full_name, email, phone_number, address, 
            aadhaar_hash, aadhaar_document_url, pan_number, pan_document_url, identity_verification_status 
            FROM users WHERE user_id = ?");

        if ($stmt === false) {
            throw new Exception("Database query prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();

            $userData['aadhaar_masked'] = mask_number($userData['aadhaar_hash']);
            $userData['pan_masked'] = mask_number($userData['pan_number']);
            $userData['is_verified'] = ($userData['identity_verification_status']);

            unset($userData['aadhaar_hash']);
            unset($userData['pan_number']);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'User data fetched successfully.',
                'data' => $userData
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An error occurred while fetching your profile data.']);
    }
} else {
    // Handle any other request methods
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
}

// Don't close the connection as it's shared from config.php
// $conn->close();
exit();
?>