<?php
// PropEx/UserPanel/templates/home.php
ini_set('session.cookie_path', '/');
session_start();

// Check if user is logged in, if not redirect to login
if (!isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../config.php';
    header('Location: ' . url('templates/login.php'));
    exit();
}

require_once __DIR__ . '/../config.php';

// Fetch user verification status
$isUserVerified = false;
$userData = null;
try {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT identity_verification_status FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $isUserVerified = ($userData['identity_verification_status'] === 'Verified');
    }
    $stmt->close();
} catch (Exception $e) {
    // Continue without verification status
}

// Fetch property listings using the helper function
require_once __DIR__ . '/../src/api/Property/fetch_listings_helper.php';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
$listings = fetchListingsData($conn, $limit);

include __DIR__ . '/../src/includes/header.php';
?>
<main class="min-h-screen pt-12" style="background: var(--bg-primary); padding-top: 80px;">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between mb-8">
            <h1 class="text-4xl font-extrabold mb-4 md:mb-0" style="color: var(--brand-primary);">Explore Properties</h1>
            <div class="space-x-4">
                <a href="<?php echo url('templates/portfolio.php'); ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 transition" style="color: var(--brand-primary); border-color: var(--brand-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    My Portfolio
                </a>
                <a id="list-property-btn" href="<?php echo url('templates/listProperties.php'); ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition" style="background: var(--brand-primary);" onmouseover="this.style.background='var(--brand-secondary)'" onmouseout="this.style.background='var(--brand-primary)'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    List a Property
                </a>
            </div>
        </div>

        <div id="property-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($listings)): ?>
                <?php foreach ($listings as $listing): ?>
                    <?php
                    $propertyImage = '';
                    if (!empty($listing['property_image'])) {
                        // property_image might be stored as '/uploads/properties/file.jpg' or 'uploads/properties/file.jpg'
                        $imgPath = ltrim($listing['property_image'], '/');
                        $propertyImage = adminPublicUrl($imgPath);
                    } else {
                        $propertyImage = 'https://via.placeholder.com/600x400.png?text=No+Image';
                    }
                    $listingId = htmlspecialchars($listing['listing_id']);
                    $propertyName = htmlspecialchars($listing['property_name']);
                    $propertyType = htmlspecialchars($listing['property_type']);
                    $location = htmlspecialchars($listing['location']);
                    $percentageAvailable = htmlspecialchars($listing['percentage_available']);
                    
                    if (isset($listing['price_total']) && $listing['price_total']) {
                        $priceText = '<p class="text-2xl font-extrabold" style="color: var(--brand-primary);">₹' . number_format($listing['price_total']) . '</p><p class="text-xs mt-1" style="color: var(--text-secondary);">for ' . $percentageAvailable . '% share</p>';
                    } else {
                        $proRataPrice = (floatval($listing['estimated_value']) * floatval($listing['percentage_available']) / 100);
                        $priceText = '<p class="text-2xl font-extrabold" style="color: var(--brand-primary);">₹' . number_format($proRataPrice, 2) . '</p><p class="text-xs mt-1" style="color: var(--text-secondary);">Pro-rata for ' . $percentageAvailable . '% share</p>';
                    }
                    
                    $cardClass = 'bg-white rounded-3xl p-6 shadow-xl transition-transform duration-200 hover:-translate-y-1 relative overflow-hidden';
                    // No opacity change for unverified users as requested
                    ?>
                    <div class="<?php echo $cardClass; ?>" 
                         data-listing-id="<?php echo $listingId; ?>"
                    style="cursor: pointer;">
                        <img src="<?php echo $propertyImage; ?>" alt="<?php echo $propertyName; ?>" class="w-full h-48 object-cover rounded-2xl mb-4">
                        <h3 class="text-xl font-bold truncate mb-1" style="color: var(--text-primary);"><?php echo $propertyName; ?></h3>
                        <p class="text-sm font-medium uppercase tracking-wide mb-2" style="color: var(--brand-primary);"><?php echo $propertyType; ?></p>
                        <p class="text-sm mb-4 truncate" style="color: var(--text-secondary);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" style="color: var(--text-secondary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.8A2 2 0 0112 21.414V19a2 2 0 00-2-2H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2v12z" />
                            </svg>
                            <?php echo $location; ?>
                        </p>
                        <?php echo $priceText; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center col-span-full" style="color: var(--text-secondary);">No active listings found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const propertyGrid = document.getElementById('property-grid');
        const listPropertyBtn = document.getElementById('list-property-btn');
        const isUserVerified = <?php echo $isUserVerified ? 'true' : 'false'; ?>;

        // Debug: Log fetched listings
        const listingsData = <?php echo json_encode($listings); ?>;
        console.log('Total Listings Fetched:', listingsData.length);
        console.log('Fetched Listings Data:', listingsData);

        // Handle property card clicks
        const propertyCards = propertyGrid.querySelectorAll('[data-listing-id]');
        propertyCards.forEach(card => {
            card.addEventListener('click', function() {
                if (isUserVerified) {
                    const listingId = this.getAttribute('data-listing-id');
                    window.location.href = baseUrl(`/templates/singlePropertyDetail.php?listing_id=${listingId}`);
                } else {
                    showCustomModal({
                        title: '🏠 Ready to Start Your Investment Journey?',
                        message: "Like what you see? To maintain a high-trust community and protect our investors, we require a quick one-time profile verification before you can explore detailed analytics and buy property shares. Verify now to unlock the full potential of PropEx!",
                        buttons: [
                            {
                                text: 'Start Verification',
                                class: 'bg-brand-primary text-white',
                                onClick: () => window.location.href = baseUrl('templates/profile.php')
                            },
                            {
                                text: 'Explore More',
                                class: 'bg-gray-100 text-gray-700'
                            }
                        ]
                    });
                }
            });
        });

        // Handle "List a Property" button click
        listPropertyBtn.addEventListener('click', function(event) {
            if (!isUserVerified) {
                event.preventDefault();
                showCustomModal({
                    title: '🚀 Unlock the Marketplace!',
                    message: "Ready to turn your property into a prime investment opportunity? To ensure a safe, trusted, and elite community for all PropEx users, we require a quick identity verification before you can list new properties. Join our verified investors today!",
                    buttons: [
                        {
                            text: 'Verify My Profile Now',
                            class: 'bg-brand-primary text-white',
                            onClick: () => window.location.href = baseUrl('templates/profile.php')
                        },
                        {
                            text: 'Maybe Later',
                            class: 'bg-gray-100 text-gray-700'
                        }
                    ]
                });
            }
        });
    });
</script>
<?php include __DIR__ . '/../src/includes/footer.php'; ?>