  <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  
  // Include config.php if not already included (for url() and BASE_PATH functions)
  // header.php is in UserPanel/src/includes/, so go up 2 levels to UserPanel/ to find config.php
  if (!function_exists('url')) {
    $config_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config.php';
    if (file_exists($config_path)) {
      require_once $config_path;
    }
  }
  
  // Ensure BASE_PATH is defined
  if (!defined('BASE_PATH')) {
    function getBasePath() {
      $scriptName = $_SERVER['SCRIPT_NAME'];
      $scriptDir = dirname($scriptName);
      if ($scriptDir === '/' || $scriptDir === '\\') {
        return '';
      }
      return rtrim($scriptDir, '/\\');
    }
    define('BASE_PATH', getBasePath());
  }
  
  // Define url() function if not exists
  if (!function_exists('url')) {
    function url($path) {
      $path = ltrim($path, '/');
      $path = str_replace('src/templates/', 'templates/', $path);
      $base = BASE_PATH === '' ? '' : BASE_PATH;
      return $base . '/' . $path;
    }
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
    <link rel="stylesheet" href="<?php echo url('public/css/style.css'); ?>">
  </head>

  <body class="min-h-screen flex flex-col bg-primary text-primary">

    <!-- Navbar -->
    <nav class="w-full bg-white shadow-lg z-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

          <!-- Logo -->
          <a href="/<?php echo isset($_SESSION['user_id']) ? 'src/templates/home.php' : 'index.php'; ?>"
            class="text-4xl font-extrabold brand-primary">
            Prop<span class="brand-secondary">Ex</span>
          </a>

          <!-- Desktop Menu -->
          <div class="hidden lg:flex space-x-8">
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="../templates/home.php"
                class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>">
                Home
              </a>
              <a href="../templates/aboutUs.php"
                class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'aboutUs.php') ? 'active' : ''; ?>">
                AboutUs
              </a>
              <a href="../templates/profile.php" class="nav-link accent-negative">Profile</a>
            <?php else: ?>
              <a href="/index.php"
                class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                Dashboard
              </a>
              <a href="../templates/aboutUs.php"
                class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'aboutUs.php') ? 'active' : ''; ?>">
                AboutUs
              </a>
              <a href="/src/templates/login.php" class="nav-link btn-secondary">Login</a>
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
          <a href="/src/templates/aboutUs.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'aboutUs.php') ? 'active' : ''; ?>">AboutUs</a>
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/src/templates/dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
            <a href="PropEx/UserPanel/templates/profile.php" class="nav-link accent-negative">Profile</a>
          <?php else: ?>
            <a href="/src/templates/login.php" class="nav-link btn-secondary">Login</a>
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
    const BASE_PATH = '<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>';
    const baseUrl = (path) => {
      path = path.startsWith('/') ? path.substring(1) : path;
      let base = BASE_PATH || '';
      if (base.endsWith('/templates')) {
        base = base.replace('/templates', '');
      }
      if (path.startsWith('src/') || path.startsWith('public/')) {
        return base ? base + '/' + path : '/' + path;
      }
      if (path.startsWith('templates/')) {
        return base ? base + '/' + path : '/' + path;
      }
      return base ? base + '/' + path : '/' + path;
    };
    
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