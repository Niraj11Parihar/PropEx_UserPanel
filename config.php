    <?php
    // Database configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); // Change to your MySQL username
    define('DB_PASS', '');     // Change to your MySQL password
    define('DB_NAME', 'propex_database');
    $secret_key = "your-secret-key";
    $secret_iv  = "your-secret-iv";

    // Connect to MySQL
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    ?>