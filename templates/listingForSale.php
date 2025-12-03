<?php
// PropEx/UserPanel/list-for-sale.php
include __DIR__ . '/../src/includes/header.php';
include __DIR__ . '/../src/includes/backButton.php';

$property_id = $_GET['property_id'] ?? null;
if (!$property_id) {
    echo "<p class='text-center text-red-500 mt-12'>Invalid property.</p>";
    exit();
}
?>

<main class="bg-gray-100 min-h-screen pt-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">List Your Share</h1>

        <form id="listShareForm" class="bg-white shadow-lg rounded-2xl p-8 max-w-lg mx-auto">
            <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property_id); ?>">

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Ownership % to Sell</label>
                <input type="number" name="percentage_to_list" min="0.0001" max="100" step="0.0001"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-brand-primary"
                    placeholder="Enter percentage (e.g. 50)" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Total Price (₹) <span class="text-gray-500 text-sm">(optional)</span></label>
                <input type="number" name="price_total" min="0"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-brand-primary"
                    placeholder="Enter price in INR">
            </div>

            <button type="submit"
                class="w-full bg-brand-primary text-white py-3 px-6 rounded-xl font-semibold hover:bg-brand-secondary transition">
                Submit Listing
            </button>
        </form>

        <div id="listing-message" class="text-center mt-6"></div>
    </div>
</main>

<script>
    document.getElementById('listShareForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const messageBox = document.getElementById('listing-message');

        try {
            const response = await fetch(baseUrl('src/api/Property/create_listing.php'), {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                messageBox.innerHTML = `<p class="text-green-600 font-bold">${result.message}</p>`;
                setTimeout(() => {
                    window.location.href = baseUrl("templates/portfolio.php");
                }, 2000);
            } else {
                messageBox.innerHTML = `<p class="text-red-600 font-bold">${result.message}</p>`;
            }
        } catch (error) {
            console.error(error);
            messageBox.innerHTML = `<p class="text-red-600 font-bold">Something went wrong.</p>`;
        }
    });
</script>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>