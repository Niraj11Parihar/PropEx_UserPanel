<?php
// PropEx/UserPanel/portfolio.php
include __DIR__ . '/../src/includes/header.php';
include __DIR__ . '/../src/includes/backButton.php';
?>
<main class="bg-gray-100 min-h-screen pt-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between mb-8">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4 md:mb-0">My Portfolio</h1>
            <a href="/PropEx/UserPanel/templates/listProperties.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-brand-primary hover:bg-brand-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                </svg>
                List a New Property
            </a>
        </div>

        <div id="portfolio-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        </div>

        <div id="no-properties-message" class="text-center text-gray-500 mt-12 hidden">
            <p>You do not currently own any properties.</p>
            <a href="/PropEx/UserPanel/index.php" class="text-brand-primary font-semibold mt-2 block hover:underline">Explore the Marketplace</a>
        </div>

    </div>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const portfolioGrid = document.getElementById('portfolio-grid');
        const noPropertiesMessage = document.getElementById('no-properties-message');

        async function fetchUserProperties() {
            try {
                const response = await fetch('/PropEx/UserPanel/src/api/Property/get_user_properties.php');
                if (!response.ok) throw new Error('Failed to fetch portfolio.');
                const data = await response.json();

                if (data.success && data.data.properties.length > 0) {
                    portfolioGrid.innerHTML = '';
                    data.data.properties.forEach(property => {
                        const ownershipPercentage = parseFloat(property.ownership_percentage.trim()).toFixed(0); 
                        const estimatedValue = parseFloat(property.estimated_value).toLocaleString();
                        const card = document.createElement('div');
                        card.classList.add('bg-white', 'rounded-3xl', 'p-6', 'shadow-xl', 'hover:shadow-2xl', 'transition-transform', 'duration-300', 'hover:-translate-y-2');
                        card.innerHTML = `
                            <img src="/PropEx/AdminPanel/public/${property.property_image}" alt="${property.property_name}" class="w-full h-48 object-cover rounded-2xl mb-4">
                            <h3 class="text-xl font-bold text-gray-900 truncate mb-1">${property.property_name}</h3>
                            <p class="text-sm text-gray-600 mb-4 truncate"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.8A2 2 0 0112 21.414V19a2 2 0 00-2-2H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2v12z" /></svg>${property.location}</p>
                            <p class="text-2xl font-extrabold text-brand-primary">
                                <span class="text-base font-semibold text-gray-500">My Share:</span> ${ownershipPercentage}%
                            </p>
                            <p class="text-lg font-bold text-gray-700"><span class="text-base font-semibold text-gray-500">Estimated Value:</span>₹${estimatedValue}</p>
                            <div class="mt-6">
                                <a href="/PropEx/UserPanel/templates/listingForSale.php?property_id=${property.property_id}" class="w-full inline-block text-center px-4 py-3 bg-brand-primary text-white font-semibold rounded-lg hover:bg-brand-secondary transition">
                                    List My Share
                                </a>
                            </div>
                        `;
                        portfolioGrid.appendChild(card);
                    });
                } else {
                    noPropertiesMessage.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error fetching portfolio:', error);
                noPropertiesMessage.classList.remove('hidden');
                noPropertiesMessage.innerHTML = '<p class="text-red-500">Failed to load portfolio. Please try again later.</p>';
            }
        }
        fetchUserProperties();
    });
</script>
<?php include __DIR__ . '/../src/includes/footer.php'; ?>