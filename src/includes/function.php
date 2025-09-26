<?php
// PropEx/src/includes/functions.php

/**
 * A simple helper function to safely redirect the user to a new URL.
 * It's a good practice to use this after processing form submissions
 * to prevent form resubmission on page refresh.
 *
 * @param string $url The destination URL.
 */
function redirectTo($url) {
    header('Location: ' . $url);
    exit();
}

// You can add more reusable functions here as your application grows, for example:
// function validateEmail($email) { ... }
// function encryptData($data, $key, $iv) { ... }
// function getPropertyOwners($propertyId) { ... }

?>