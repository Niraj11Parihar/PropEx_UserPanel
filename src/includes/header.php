<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PropEx - Sustainable Communities</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/css/style.css">
</head>

<body class="min-h-screen flex flex-col bg-primary text-primary">

  <!-- Navbar -->
  <nav class="w-full bg-white shadow-lg z-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">

        <!-- Logo -->
        <a href="/<?php echo isset($_SESSION['user_id']) ? 'templates/home.php' : 'templates/index.php'; ?>"
          class="text-4xl font-extrabold brand-primary">
          Prop<span class="brand-secondary">Ex</span>
        </a>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex space-x-8">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/templates/home.php"
              class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>">
              Home
            </a>
            <a href="/templates/aboutUs.php"
              class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'aboutUs.php') ? 'active' : ''; ?>">
              AboutUs
            </a>
            <a href="/templates/profile.php" class="nav-link accent-negative">Profile</a>
          <?php else: ?>
            <a href="/templates/index.php"
              class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
              Dashboard
            </a>
            <a href="/templates/aboutUs.php"
              class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'aboutUs.php') ? 'active' : ''; ?>">
              AboutUs
            </a>
            <a href="/templates/login.php" class="nav-link btn-secondary">Login</a>
          <?php endif; ?>
        </div>

        <!-- Mobile Button -->
        <div class="lg:hidden">
          <button id="mobile-menu-btn" class="text-2xl focus:outline-none">
            &#9776;
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white shadow-md">
      <div class="flex flex-col space-y-4 px-6 py-4">
        <a href="/index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a>
        <a href="/templates/aboutUs.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'aboutUs.php') ? 'active' : ''; ?>">AboutUs</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="/templates/dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
          <a href="/templates/profile.php" class="nav-link accent-negative">Profile</a>
        <?php else: ?>
          <a href="/templates/login.php" class="nav-link btn-secondary">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- Script -->
  <script>
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  </script>

</body>

</html>

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: {
            primary: "var(--brand-primary)",
            secondary: "var(--brand-secondary)",
          },
          bg: {
            primary: "var(--bg-primary)",
            secondary: "var(--bg-secondary)",
          },
          text: {
            primary: "var(--text-primary)",
            secondary: "var(--text-secondary)",
          },
          accent: {
            positive: "var(--accent-positive)",
            negative: "var(--accent-negative)",
          },
        },
      },
    },
  };
</script>

<style>
  :root {
    --brand-primary: #102f85;
    --brand-secondary: #d96512c3;
    --bg-primary: #f3f4f6;
    --bg-secondary: #d96512c3;
    --text-primary: #3a4a6cdc;
    --text-secondary: #6b7281;
    --accent-positive: #16a34a;
    --accent-negative: #dc2626;
  }
</style>