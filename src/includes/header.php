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

    <!-- Custom Modal System -->
    <!-- Custom Modal System -->
    <div id="custom-modal-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4">
      <div id="custom-modal-container" class="bg-white rounded-[2rem] shadow-2xl max-w-md w-full transform transition-all scale-95 opacity-0 duration-300 overflow-hidden">
        <div class="p-8 md:p-10 text-center">
          <div id="modal-icon" class="mb-6 inline-flex p-4 rounded-full bg-blue-50 text-brand-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <h3 id="modal-title" class="text-2xl md:text-3xl font-extrabold mb-4" style="color: var(--brand-primary);">Notification</h3>
          <p id="modal-message" class="text-gray-600 mb-8 leading-relaxed font-medium text-lg">Message</p>
          <div id="modal-buttons" class="flex flex-col gap-3">
            <!-- Buttons will be injected here -->
          </div>
          <button onclick="window.closeCustomModal()" class="mt-6 text-sm text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest font-bold">
            Dismiss
          </button>
        </div>
      </div>
    </div>

    <!-- Script -->
    <script>
      const mobileBtn = document.getElementById('mobile-menu-btn');
      const mobileMenu = document.getElementById('mobile-menu');

      mobileBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });

      // Modal Logic
      function showCustomModal(options) {
        const overlay = document.getElementById('custom-modal-overlay');
        const container = document.getElementById('custom-modal-container');
        const title = document.getElementById('modal-title');
        const message = document.getElementById('modal-message');
        const buttonContainer = document.getElementById('modal-buttons');

        // Reset
        title.innerText = options.title || 'Notification';
        message.innerText = options.message || '';
        buttonContainer.innerHTML = '';

        // Create buttons
        const buttons = options.buttons || [{
          text: 'Okay',
          class: 'bg-brand-primary text-white',
          style: { backgroundColor: 'var(--brand-primary)' },
          onClick: () => window.closeCustomModal()
        }];

        buttons.forEach(btn => {
          const button = document.createElement('button');
          button.innerText = btn.text;
          button.className = `px-6 py-4 rounded-2xl font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 w-full shadow-md hover:shadow-lg transform active:scale-95 text-lg ${btn.class || 'bg-gray-100 text-gray-700 hover:bg-gray-200'}`;

          if (btn.style) {
            Object.assign(button.style, btn.style);
          }

          button.onclick = () => {
            if (btn.onClick) btn.onClick();
            else window.closeCustomModal();
          };
          buttonContainer.appendChild(button);
        });

        // Show modal
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        setTimeout(() => {
          container.classList.remove('scale-95', 'opacity-0');
          container.classList.add('scale-100', 'opacity-100');
        }, 10);

        window.closeCustomModal = function() {
          container.classList.remove('scale-100', 'opacity-100');
          container.classList.add('scale-95', 'opacity-0');
          setTimeout(() => {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            if (options.onClose) options.onClose();
          }, 300);
        };
      }
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