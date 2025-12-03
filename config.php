<?php
// Base path detection - works for both localhost (subdirectory) and InfinityFree (root)
function getBasePath() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = dirname($scriptName);
    
    // If we're in a subdirectory, get the base path
    // Example: /PropEx/UserPanel/index.php -> /PropEx/UserPanel
    // Example: /index.php -> /
    if ($scriptDir === '/' || $scriptDir === '\\') {
        return '';
    }
    return rtrim($scriptDir, '/\\');
}

// Set base path constant
define('BASE_PATH', getBasePath());

// Helper function to create URLs with base path
// For localhost: templates are in root/templates/, not src/templates/
function url($path) {
    $path = ltrim($path, '/');
    // Convert src/templates/ to templates/ for localhost
    $path = str_replace('src/templates/', 'templates/', $path);
    $base = BASE_PATH === '' ? '' : BASE_PATH;
    return $base . '/' . $path;
}

// Helper function to get AdminPanel public asset URL
function adminPublicUrl($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    
    // Get base path and go up one level to PropEx, then into AdminPanel/public
    $base = BASE_PATH === '' ? '' : BASE_PATH;
    // Remove /UserPanel or /UserPanel/templates from base to get PropEx root
    $base = preg_replace('#/UserPanel(/templates)?$#', '', $base);
    
    // Construct full path: /PropEx/AdminPanel/public/uploads/properties/file.jpg
    return ($base ? $base . '/' : '/') . 'AdminPanel/public/' . $path;
}

// Database configuration
// Check if environment variables are set (for production/InfinityFree)
// Otherwise use localhost configuration (for local development)
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'propex_database';

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);

// Encryption keys
$secret_key = getenv('SECRET_KEY') ?: 'your-secret-key';
$secret_iv  = getenv('SECRET_IV') ?: 'your-secret-iv';

// Connect to MySQL
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
