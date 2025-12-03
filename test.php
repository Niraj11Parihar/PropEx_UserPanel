<?php
// Simple test file to verify PHP and paths are working
echo "<h1>Test Page</h1>";
echo "<p>PHP is working!</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><a href='/src/templates/login.php'>Test Login Page</a></p>";
echo "<p><a href='/index.php'>Test Index Page</a></p>";
?>


