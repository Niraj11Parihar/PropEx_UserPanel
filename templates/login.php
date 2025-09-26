<?php
ini_set('session.cookie_path', '/');
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/api/Auth/auth_function.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = validateLogin($_POST);

    if (empty($errors)) {
        if (loginUser($conn, $_POST)) {
            header('Location: home.php');
            exit();
        } else {
            $_SESSION = [];
            session_destroy();

            $errors['general'] = "Invalid email or password.";
        }
    }
}
?>

<link rel="stylesheet" href="/PropEx/UserPanel/public/css/style.css">
<div class="auth-container card">
    <div class="auth-header">
        <h1 class="brand-primary brand  text-center text-4xl">
            Prop<span class="brand-secondary">Ex</span>
        </h1>
        <h5 class="auth-title">Login to Your Account</h5>
        <p class="auth-subtitle">Enter your credentials to continue</p>
    </div>

    <?php if (isset($errors['general'])): ?>
        <div class="message message-error"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            <?php if (isset($errors['email'])): ?>
                <small class="accent-negative"><?= $errors['email'] ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <?php if (isset($errors['password'])): ?>
                <small class="accent-negative"><?= $errors['password'] ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Login</button>

        <div class="text-center" style="margin-top: 20px;">
            <p>Don't have an account? <a href="register.php" class="brand-primary">Register here</a></p>
        </div>
    </form>
</div>
<?php include '../src/includes/footer.php'; ?>