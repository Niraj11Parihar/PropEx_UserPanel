<?php
session_start();
include __DIR__ . '/../src/includes/header.php';

// Path to the JSON file
$json_file = __DIR__ . '/../public/json/aboutUs.json';
$content = json_decode(file_get_contents($json_file), true);

?>
<main class="min-h-screen flex flex-col items-center py-12 px-4 font-sans">

    <!-- Hero Section -->
    <section class="w-full max-w-6xl rounded-3xl bg-brand-primary text-white p-12 mb-16 text-center shadow-xl animate-float">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
            <?php echo htmlspecialchars($content['hero']['title']); ?>
        </h1>
        <p class="text-xl md:text-2xl max-w-3xl mx-auto opacity-90">
            <?php echo htmlspecialchars($content['hero']['subtitle']); ?>
        </p>
    </section>

    <!-- About Us Section -->
    <section class="w-full max-w-6xl mb-20">
        <div class="flex flex-col lg:flex-row items-center gap-10">
            <div class="w-full lg:w-2/5">
                <?php if (!empty($content['about_us']['image_path'])): 
                    $imagePath = url($content['about_us']['image_path']);
                ?>
                    <img src="<?php echo $imagePath; ?>" 
                         onerror="this.onerror=null; this.src='<?php echo url('public/images/aboutUs1.png'); ?>';"
                        alt="<?php echo htmlspecialchars($content['about_us']['image_alt']); ?>"
                        style="width: 100%; height: 320px; object-fit: cover; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                <?php else: ?>
                    <div class="w-full h-80 bg-bg-secondary rounded-2xl flex items-center justify-center text-gray-400 text-3xl font-bold">
                        [Image Not Found]
                    </div>
                <?php endif; ?>
            </div>
            <div class="w-full lg:w-3/5">
                <h2 class="text-3xl md:text-4xl font-bold mb-6" style="color: var(--brand-primary);">
                    <?php echo htmlspecialchars($content['about_us']['heading']); ?>
                </h2>
                <div class="space-y-4" style="color: var(--text-secondary);">
                    <?php foreach ($content['about_us']['paragraphs'] as $paragraph) : ?>
                        <p class="leading-relaxed" style="line-height: 1.75;"><?php echo htmlspecialchars($paragraph); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="w-full max-w-6xl mb-20">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12" style="color: var(--brand-primary);">
            <?php echo htmlspecialchars($content['our_services']['heading']); ?>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($content['our_services']['services'] as $service) : ?>
                <div class="service-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <div class="flex items-center mb-4">
                        <span class="text-3xl mr-3" style="color: var(--brand-primary);"><?php echo htmlspecialchars_decode($service['icon_code']); ?></span>
                        <h3 class="text-xl font-semibold" style="color: var(--brand-primary);"><?php echo htmlspecialchars($service['title']); ?></h3>
                    </div>
                    <p style="color: var(--text-secondary);"><?php echo htmlspecialchars($service['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="w-full max-w-6xl mb-20">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12" style="color: var(--brand-primary);">
            <?php echo htmlspecialchars($content['benefits']['heading']); ?>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($content['benefits']['items'] as $benefit) : ?>
                <div class="bg-white p-6 rounded-xl shadow-md text-center transition-all duration-300 hover:shadow-lg" style="background: white;">
                    <span class="text-4xl mb-3 inline-block" style="color: var(--brand-secondary);"><?php echo htmlspecialchars_decode($benefit['icon_code']); ?></span>
                    <h3 class="text-lg font-semibold mb-2" style="color: var(--brand-primary);"><?php echo htmlspecialchars($benefit['title']); ?></h3>
                    <p class="text-sm" style="color: var(--text-secondary);"><?php echo htmlspecialchars($benefit['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="w-full max-w-6xl mb-20">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12" style="color: var(--brand-primary);">
            <?php echo htmlspecialchars($content['how_it_works']['heading']); ?>
        </h2>
        <div class="relative">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <?php foreach ($content['how_it_works']['steps'] as $index => $step) : ?>
                    <div class="relative flex flex-col items-center text-center p-6 rounded-xl shadow-md" style="background: white;">
                        <?php if ($index < count($content['how_it_works']['steps']) - 1): ?>
                            <div class="timeline-connector hidden md:block"></div>
                        <?php endif; ?>
                        <div class="step-number" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--brand-secondary); color: white; font-weight: bold; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($step['number']); ?>
                        </div>
                        <h3 class="text-xl font-semibold mb-2" style="color: var(--brand-primary);"><?php echo htmlspecialchars($step['title']); ?></h3>
                        <p style="color: var(--text-secondary);"><?php echo htmlspecialchars($step['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="w-full max-w-3xl text-center py-12 px-6 rounded-3xl shadow-xl mb-16" style="background: var(--brand-primary);">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
            <?php echo htmlspecialchars($content['cta']['title']); ?>
        </h2>
        <p class="text-lg max-w-2xl mx-auto mb-8 text-white opacity-90">
            <?php echo htmlspecialchars($content['cta']['subtitle']); ?>
        </p>
        <a href="<?php echo url('templates/register.php'); ?>"
            style="display: inline-block; padding: 16px 32px; background: white; color: var(--brand-primary); font-weight: bold; border-radius: 9999px; font-size: 18px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <?php echo htmlspecialchars($content['cta']['button_text']); ?>
        </a>
    </section>

</main>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>