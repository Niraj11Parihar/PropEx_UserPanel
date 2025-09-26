<?php
ini_set('session.cookie_path', '/'); 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'] ?? 'User';

include __DIR__ . '/../src/includes/header.php';

?>

<main class="min-h-screen flex flex-col items-center justify-center py-12 px-4 bg-gradient-to-br from-bg-primary to-bg-secondary">

    <!-- Dashboard Welcome Card -->
    <section class="w-full max-w-4xl bg-secondary rounded-3xl p-8 md:p-12 shadow-2xl text-center mb-16 animate-fade-in-up">
        <h1 class="text-3xl md:text-4xl font-extrabold brand-primary mb-4 leading-tight">
            Welcome Back, <?php echo htmlspecialchars($username); ?>!
        </h1>
        <p class="text-secondary text-lg md:text-xl max-w-2xl mx-auto mb-8 animate-fade-in">
            Your personalized space to manage your community interactions and settings.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-6 animate-scale-in">
            <a href="#"
               class="px-8 py-4 btn-primary rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-brand-primary">
                Manage Properties
            </a>
            <a href="#"
               class="px-8 py-4 border-2 border-brand-primary text-brand-primary font-bold rounded-full text-lg hover:bg-brand-primary hover:text-white transition-all duration-300 transform hover:scale-105 shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-brand-primary">
                Edit Profile
            </a>
        </div>
    </section>

    <!-- Dashboard Content -->
    <section class="w-full max-w-5xl grid md:grid-cols-2 gap-8 mt-8 md:mt-12">
        <div class="bg-secondary p-8 rounded-2xl shadow-xl border-t-4 border-brand-primary animate-fade-in-left">
            <h2 class="text-2xl font-bold brand-primary mb-4">Your Recent Activity</h2>
            <ul class="text-secondary list-disc list-inside space-y-2">
                <li>Viewed "Green Valley Eco-Homes" on <?php echo date('Y-m-d', strtotime('-2 days')); ?></li>
                <li>Updated profile information on <?php echo date('Y-m-d', strtotime('-5 days')); ?></li>
                <li>Explored "Riverside Smart Lofts" on <?php echo date('Y-m-d', strtotime('-1 week')); ?></li>
            </ul>
        </div>

        <div class="bg-secondary p-8 rounded-2xl shadow-xl border-t-4 border-brand-secondary animate-fade-in-right">
            <h2 class="text-2xl font-bold brand-secondary mb-4">Quick Links</h2>
            <ul class="text-secondary list-disc list-inside space-y-2">
                <li><a href="#" class="hover:brand-primary transition-colors duration-200">Browse Communities</a></li>
                <li><a href="#" class="hover:brand-primary transition-colors duration-200">Contact Support</a></li>
                <li><a href="#" class="hover:brand-primary transition-colors duration-200">Account Settings</a></li>
            </ul>
        </div>
    </section>

</main>

<?php include __DIR__. '/../src/includes/footer.php'; ?> 