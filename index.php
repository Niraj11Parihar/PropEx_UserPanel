<?php
ini_set('session.cookie_path', '/');
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: templates/dashboard.php');
    exit();
}

include 'src/includes/header.php';

?>
<link rel="stylesheet" href="/public/css/style.css">

<main class="min-h-screen flex flex-col items-center justify-center py-12 px-4 bg-gradient-to-br from-bg-primary to-bg-secondary">

    <!-- Hero Section -->
    <section class="w-full max-w-5xl bg-secondary rounded-3xl p-8 md:p-12 shadow-2xl text-center mb-16 animate-fade-in-up">
        <h1 class="text-4xl md:text-5xl font-extrabold brand-primary mb-6 leading-tight">
            PropEx: Envisioning Tomorrow's Sustainable Communities
        </h1>
        <p class="text-secondary text-lg md:text-xl max-w-3xl mx-auto mb-10 animate-fade-in">
            Dive into interactive 3D visualizations and explore a new era of eco-conscious living.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-6 animate-scale-in">
            <a href="/templates/explore.php"
                class="px-8 py-4 btn-primary rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-brand-primary">
                Explore Community
            </a>
            <a href="/templates/register.php"
                class="px-8 py-4 border-2 border-brand-secondary text-brand-secondary font-bold rounded-full text-lg hover:bg-brand-secondary hover:text-white transition-all duration-300 transform hover:scale-105 shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-brand-secondary">
                Join PropEx Today
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="w-full max-w-6xl grid md:grid-cols-3 gap-8 mt-8 md:mt-16">
        <div class="bg-secondary p-8 rounded-2xl shadow-xl border-t-4 border-brand-primary transform hover:scale-105 transition-transform duration-300 animate-fade-in-left">
            <h2 class="text-2xl font-bold brand-primary mb-4">Secure & Private</h2>
            <p class="text-secondary">Robust password hashing and secure session management keep your data safe and sound.</p>
        </div>

        <div class="bg-secondary p-8 rounded-2xl shadow-xl border-t-4 border-brand-secondary transform hover:scale-105 transition-transform duration-300 animate-fade-in">
            <h2 class="text-2xl font-bold brand-secondary mb-4">Sleek & Responsive</h2>
            <p class="text-secondary">Experience a beautifully crafted user interface, optimized for all devices.</p>
        </div>

        <div class="bg-secondary p-8 rounded-2xl shadow-xl border-t-4 border-accent-positive transform hover:scale-105 transition-transform duration-300 animate-fade-in-right">
            <h2 class="text-2xl font-bold accent-positive mb-4">Effortless Access</h2>
            <p class="text-secondary">Simple, intuitive registration and login flows with clear validation for a seamless experience.</p>
        </div>
    </section>

</main>

<?php include 'src/includes/footer.php'; ?>