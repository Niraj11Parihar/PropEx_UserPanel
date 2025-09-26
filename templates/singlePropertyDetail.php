<?php
session_start();
include __DIR__ . '/../src/includes/header.php';

// Check if a listing_id is provided in the URL
if (!isset($_GET['listing_id'])) {
    header("Location: /PropEx/UserPanel/index.php");
    exit();
}

$listing_id = intval($_GET['listing_id']);

// Connect to database
require_once __DIR__ . '/../config.php';
global $conn;

$listing_details = null;
$property_details = null;
$owners = [];

try {
    // Get listing details with property info
    $sql = "SELECT 
                l.listing_id, l.property_id, l.owner_user_id, l.percentage_available, 
                l.percentage_original, l.price_total, l.status, l.created_at,
                p.property_name, p.property_type, p.description, p.location, 
                p.estimated_value, p.property_image, p.verification_status,
                u.full_name as owner_name, u.email as owner_email
            FROM listings l
            JOIN properties p ON p.property_id = l.property_id
            JOIN users u ON u.user_id = l.owner_user_id
            WHERE l.listing_id = ? AND l.status IN ('Active', 'Partially_Fulfilled')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Listing not found or no longer available");
    }
    
    $listing_details = $result->fetch_assoc();
    $property_id = $listing_details['property_id'];
    
    // Get property ownership details
    $ownership_sql = "SELECT 
                        upo.user_id, u.full_name, upo.ownership_percentage 
                      FROM user_property_ownership upo
                      JOIN users u ON u.user_id = upo.user_id
                      WHERE upo.property_id = ?
                      ORDER BY upo.ownership_percentage DESC";
    
    $stmt2 = $conn->prepare($ownership_sql);
    $stmt2->bind_param("i", $property_id);
    $stmt2->execute();
    $ownership_result = $stmt2->get_result();
    
    while ($row = $ownership_result->fetch_assoc()) {
        $owners[] = $row;
    }
    
    // Calculate pro-rata price if price_total is null
    if ($listing_details['price_total'] === null) {
        $pro_rata_price = ($listing_details['estimated_value'] * $listing_details['percentage_available']) / 100;
        $listing_details['calculated_price'] = round($pro_rata_price, 2);
    } else {
        $price_per_percentage = $listing_details['price_total'] / $listing_details['percentage_original'];
        $listing_details['calculated_price'] = round($price_per_percentage * $listing_details['percentage_available'], 2);
    }
    
} catch (Exception $e) {
    error_log("Error fetching property details: " . $e->getMessage());
    echo "<p class='text-center text-red-500'>Failed to load property details. Please try again later.</p>";
    exit();
}

if (!$listing_details) {
    echo "<p class='text-center text-gray-500'>Listing not found or is no longer active.</p>";
    exit();
}
?>

<main class="bg-gray-100 min-h-screen pt-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 lg:p-16 mb-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="lg:w-1/2">
                    <img src="/PropEx/AdminPanel/public/<?php echo htmlspecialchars($listing_details['property_image']); ?>" alt="<?php echo htmlspecialchars($listing_details['property_name']); ?>" class="w-full h-96 object-cover rounded-2xl shadow-md">
                </div>
                <div class="lg:w-1/2">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-2"><?php echo htmlspecialchars($listing_details['property_name']); ?></h1>
                    <p class="text-lg font-medium text-brand-primary uppercase tracking-wide mb-4"><?php echo htmlspecialchars($listing_details['property_type']); ?></p>
                    <p class="text-gray-600 text-sm mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.8A2 2 0 0112 21.414V19a2 2 0 00-2-2H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2v12z" /></svg>
                        <?php echo htmlspecialchars($listing_details['location']); ?>
                    </p>
                    
                    <div class="border-t border-b border-gray-200 py-6 mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Listing Details</h3>
                        <p class="text-4xl font-extrabold text-brand-primary mb-2">₹<?php echo number_format($listing_details['calculated_price']); ?></p>
                        <p class="text-sm text-gray-500 mb-4">
                            <?php echo htmlspecialchars($listing_details['percentage_available']); ?>% Ownership Share
                            <?php if ($listing_details['price_total'] === null): ?>
                                (Pro-rata based on estimated value)
                            <?php endif; ?>
                        </p>
                        <p class="text-gray-600 mb-4"><?php echo nl2br(htmlspecialchars($listing_details['description'])); ?></p>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Current Owners</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            <?php foreach ($owners as $owner): ?>
                                <li>
                                    <?php echo htmlspecialchars($owner['full_name']); ?>: 
                                    <span class="font-bold"><?php echo htmlspecialchars(number_format($owner['ownership_percentage'], 2)); ?>%</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $listing_details['owner_user_id']): ?>
                        <button id="buyButton" class="w-full inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-bold rounded-xl shadow-sm text-white bg-brand-primary hover:bg-brand-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition">
                            Purchase <?php echo htmlspecialchars($listing_details['percentage_available']); ?>% Share
                        </button>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                            <p class="text-blue-800">This is your listing. You cannot purchase your own property share.</p>
                        </div>
                    <?php else: ?>
                        <a href="/PropEx/auth/login.php" class="w-full inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-bold rounded-xl shadow-sm text-white bg-brand-primary hover:bg-brand-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition">
                            Login to Purchase
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Modal -->
    <div id="purchaseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-2xl leading-6 font-medium text-gray-900">Confirm Your Purchase</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-lg text-gray-500">
                        You are about to purchase a <strong><?php echo htmlspecialchars($listing_details['percentage_available']); ?>%</strong> share of <strong><?php echo htmlspecialchars($listing_details['property_name']); ?></strong> for <strong>₹<?php echo number_format($listing_details['calculated_price']); ?></strong>.
                    </p>
                    <div class="mt-4">
                        <label for="paymentMethod" class="block text-sm font-medium text-gray-700 text-left mb-2">Payment Method</label>
                        <select id="paymentMethod" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-brand-primary focus:border-brand-primary">
                            <option value="bank_transfer">Bank Transfer (Simulated)</option>
                            <option value="crypto_transfer">Crypto Wallet (Simulated)</option>
                        </select>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmPurchaseBtn" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-full mb-2">
                        Confirm & Pay
                    </button>
                    <button id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 w-full">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buyButton = document.getElementById('buyButton');
        const purchaseModal = document.getElementById('purchaseModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const confirmPurchaseBtn = document.getElementById('confirmPurchaseBtn');

        if (buyButton) {
            buyButton.addEventListener('click', function() {
                purchaseModal.classList.remove('hidden');
            });
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                purchaseModal.classList.add('hidden');
            });
        }

        if (confirmPurchaseBtn) {
            confirmPurchaseBtn.addEventListener('click', async function() {
                try {
                    const buyer_user_id = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
                    const listing_id = <?php echo $listing_id; ?>;
                    const percentage_to_buy = <?php echo $listing_details['percentage_available']; ?>;

                    if (!buyer_user_id) {
                        alert('You must be logged in to purchase a property.');
                        window.location.href = '/PropEx/auth/login.php';
                        return;
                    }
                    
                    confirmPurchaseBtn.innerText = 'Processing Payment...';
                    confirmPurchaseBtn.disabled = true;
                    
                    const response = await fetch('/PropEx/UserPanel/src/api/Property/property_purchase.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            buyer_user_id: buyer_user_id,
                            listing_id: listing_id,
                            percentage_to_buy: percentage_to_buy
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Congratulations! Your purchase was successful and ownership has been transferred.');
                        window.location.reload();
                    } else {
                        alert('Purchase failed: ' + data.message);
                    }
                } catch (error) {
                    console.error('Purchase error:', error);
                    alert('An unexpected error occurred. Please try again.');
                } finally {
                    if (confirmPurchaseBtn) {
                        confirmPurchaseBtn.innerText = 'Confirm & Pay';
                        confirmPurchaseBtn.disabled = false;
                    }
                    purchaseModal.classList.add('hidden');
                }
            });
        }
    });
</script>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>