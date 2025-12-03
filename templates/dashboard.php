<?php
ini_set('session.cookie_path', '/'); 
session_start();
if (!isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../config.php';
    header('Location: ' . url('templates/login.php'));
    exit();
}

$username = $_SESSION['fullName'] ?? $_SESSION['username'] ?? 'User';

include __DIR__ . '/../src/includes/header.php';

?>

<main class="min-h-screen flex flex-col items-center justify-center py-12 px-4 bg-gradient-to-br from-bg-primary to-bg-secondary">

    <!-- Dashboard Welcome Card -->
    <section class="w-full max-w-4xl bg-secondary rounded-3xl p-8 md:p-12 shadow-2xl text-center mb-16 animate-fade-in-up">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight" style="color: var(--brand-primary);">
            Welcome Back, <?php echo htmlspecialchars($username); ?>!
        </h1>
        <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8 animate-fade-in" style="color: var(--text-secondary);">
            Your personalized space to manage your community interactions and settings.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-6 animate-scale-in">
            <a href="<?php echo url('templates/home.php'); ?>"
               class="px-8 py-4 rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg active:scale-95 focus:outline-none focus:ring-2 text-white" style="background: var(--brand-primary);">
                Manage Properties
            </a>
            <a href="<?php echo url('templates/profile.php'); ?>"
               class="px-8 py-4 border-2 rounded-full font-bold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg active:scale-95 focus:outline-none focus:ring-2" style="border-color: var(--brand-primary); color: var(--brand-primary);" onmouseover="this.style.background='var(--brand-primary)'; this.style.color='white';" onmouseout="this.style.background='transparent'; this.style.color='var(--brand-primary)';">
                Edit Profile
            </a>
        </div>
    </section>

    <!-- Dashboard Content -->
    <section class="w-full max-w-5xl grid md:grid-cols-2 gap-8 mt-8 md:mt-12">
        <div class="bg-secondary p-8 rounded-2xl shadow-xl border-t-4 border-brand-primary animate-fade-in-left">
            <h2 class="text-2xl font-bold mb-4" style="color: var(--brand-primary);">Your Recent Activity</h2>
            <ul class="list-disc list-inside space-y-2" style="color: var(--text-secondary);">
                <li>Viewed "Green Valley Eco-Homes" on <?php echo date('Y-m-d', strtotime('-2 days')); ?></li>
                <li>Updated profile information on <?php echo date('Y-m-d', strtotime('-5 days')); ?></li>
                <li>Explored "Riverside Smart Lofts" on <?php echo date('Y-m-d', strtotime('-1 week')); ?></li>
            </ul>
        </div>

        <div class="bg-secondary p-8 rounded-2xl shadow-xl border-t-4 border-brand-secondary animate-fade-in-right">
            <h2 class="text-2xl font-bold mb-4" style="color: var(--brand-secondary);">Quick Links</h2>
            <ul class="list-disc list-inside space-y-2" style="color: var(--text-secondary);">
                <li><a href="<?php echo url('templates/home.php'); ?>" style="color: var(--brand-primary); text-decoration: none;" onmouseover="this.style.color='var(--brand-secondary)'" onmouseout="this.style.color='var(--brand-primary)'">Browse Communities</a></li>
                <li><a href="#" style="color: var(--brand-primary); text-decoration: none;" onmouseover="this.style.color='var(--brand-secondary)'" onmouseout="this.style.color='var(--brand-primary)'">Contact Support</a></li>
                <li><a href="<?php echo url('templates/profile.php'); ?>" style="color: var(--brand-primary); text-decoration: none;" onmouseover="this.style.color='var(--brand-secondary)'" onmouseout="this.style.color='var(--brand-primary)'">Account Settings</a></li>
            </ul>
        </div>
    </section>

</main>

<?php include __DIR__. '/../src/includes/footer.php'; ?> 