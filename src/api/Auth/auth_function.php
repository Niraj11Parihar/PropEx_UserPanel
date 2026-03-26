<?php
// Validate user registration
function validateRegistration($data)
{
    $errors = [];

    if (empty($data['fullName'])) {
        $errors['fullName'] = "Full name is required";
    } elseif (strlen($data['fullName']) < 6) {
        $errors['fullName'] = "Full name must be at least 6 characters";
    }

    if (empty($data['email'])) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($data['password'])) {
        $errors['password'] = "Password is required";
    } elseif (strlen($data['password']) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    } elseif (!preg_match('/[A-Za-z]/', $data['password'])) {
        $errors['password'] = "Password must contain at least one alphabet";
    } elseif (!preg_match('/[0-9]/', $data['password'])) {
        $errors['password'] = "Password must contain at least one number";
    } elseif (!preg_match('/[^A-Za-z0-9]/', $data['password'])) {
        $errors['password'] = "Password must contain at least one special character";
    }

    if ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    return $errors;
}

// Validate user login
function validateLogin($data)
{
    $errors = [];

    if (empty($data['email'])) {
        $errors['email'] = "Email is required";
    }

    if (empty($data['password'])) {
        $errors['password'] = "Password is required";
    }

    return $errors;
}

// Register new user
function registerUser($conn, $data)
{
    $fullName = $conn->real_escape_string($data['fullName']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password_hash) VALUES ('$fullName', '$email', '$password')";

    return $conn->query($sql);
}

// Login user
function loginUser($conn, $data)
{
    if (!isset($data['email']) || !isset($data['password'])) {
        return false;
    }

    $email = $conn->real_escape_string($data['email']);

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();


        if (password_verify($data['password'], $user['password_hash'])) {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['fullName'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['full_name']; // For compatibility
            return true;
        }
    }
    return false;
}
