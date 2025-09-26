<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/api/Auth/auth_function.php';

$errors = [];
$formData = [
    'fullName' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'fullName' => $_POST['fullName'],
        'email' => $_POST['email']
    ];

    $errors = validateRegistration($_POST);

    if (empty($errors)) {
        if (registerUser($conn, $_POST)) {
            $_SESSION['success_message'] = "Registration successful! Please login.";
            header('Location: login.php');
            exit();
        } else {
            $errors['general'] = "Registration failed. Please try again.";
        }
    }
}
?>

<link rel="stylesheet" href="/PropEx/UserPanel/public/css/style.css">

<div class="auth-container card">
    <div class="auth-header text-center">
        <!-- Application name -->
        <h1 class="brand">
            <span class="brand-primary">Prop</span><span class="brand-secondary">Ex</span>
        </h1>

        <h2 class="auth-title">Create an Account</h2>
        <p class="auth-subtitle">Join us today</p>
    </div>

    <?php if (isset($errors['general'])): ?>
        <div class="message message-error"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" name="fullName" id="fullName" class="form-control"
                value="<?= htmlspecialchars($formData['fullName']) ?>" required>
            <?php if (isset($errors['fullName'])): ?>
                <small style="color: var(--accent-negative);"><?= $errors['fullName'] ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control"
                value="<?= htmlspecialchars($formData['email']) ?>" required>
            <?php if (isset($errors['email'])): ?>
                <small style="color: var(--accent-negative);"><?= $errors['email'] ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <?php if (isset($errors['password'])): ?>
                <small style="color: var(--accent-negative);"><?= $errors['password'] ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            <?php if (isset($errors['confirm_password'])): ?>
                <small style="color: var(--accent-negative);"><?= $errors['confirm_password'] ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary w-full">Register</button>

        <div class="text-center" style="margin-top: 20px;">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </form>
</div>

<?php include '../src/includes/footer.php'; ?>