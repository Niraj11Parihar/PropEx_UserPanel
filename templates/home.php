<?php
// PropEx/UserPanel/index.php
include __DIR__ . '/../src/includes/header.php';
?>
<main class="bg-gray-100 min-h-screen pt-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between mb-8">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4 md:mb-0">Explore Properties</h1>
            <div class="space-x-4">
                <a href="/PropEx/UserPanel/templates/portfolio.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-brand-primary bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    My Portfolio
                </a>
                <a id="list-property-btn" href="#" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-brand-primary hover:bg-brand-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    List a Property
                </a>
            </div>
        </div>

        <div id="property-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-6 shadow-xl animate-pulse">
                <div class="w-full h-48 bg-gray-200 rounded-2xl mb-4"></div>
                <div class="h-6 w-3/4 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-1/2 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 w-1/4 bg-gray-200 rounded"></div>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-xl animate-pulse">
                <div class="w-full h-48 bg-gray-200 rounded-2xl mb-4"></div>
                <div class="h-6 w-3/4 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-1/2 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 w-1/4 bg-gray-200 rounded"></div>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-xl animate-pulse">
                <div class="w-full h-48 bg-gray-200 rounded-2xl mb-4"></div>
                <div class="h-6 w-3/4 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-1/2 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 w-1/4 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const propertyGrid = document.getElementById('property-grid');
        const listPropertyBtn = document.getElementById('list-property-btn');
        let isUserVerified = false; // Flag to store verification status

        // 1. Fetch user verification status first
        async function checkVerificationStatus() {
            try {
                const userResponse = await fetch('/PropEx/UserPanel/src/api/User/user_data.php');
                if (userResponse.ok) {
                    const userResult = await userResponse.json();
                    if (userResult.success && userResult.data) {
                        userData = userResult.data;
                        isUserVerified = (userData.identity_verification_status === 'Verified');
                    }
                } else {
                    console.error('Failed to fetch user data. User might not be logged in or an error occurred.');
                }
            } catch (error) {
                console.error('Error fetching verification status:', error);
            }
        }

        // 2. Fetch and render property listings
        async function fetchListings() {
            // First, fetch the verification status
            await checkVerificationStatus();

            try {
                const response = await fetch('/PropEx/UserPanel/src/api/Property/get_all_properties.php');
                if (!response.ok) throw new Error('Failed to fetch listings.');
                const data = await response.json();

                propertyGrid.innerHTML = ''; // Clear placeholders

                if (data.success && data.data.listings.length > 0) {
                    data.data.listings.forEach(listing => {
                        // Create a div wrapper for conditional link behavior
                        const cardWrapper = document.createElement('div');
                        cardWrapper.classList.add('bg-white', 'rounded-3xl', 'p-6', 'shadow-xl', 'transition-transform', 'duration-200', 'hover:-translate-y-1', 'relative', 'overflow-hidden');

                        const propertyImage = listing.property_image ? `/PropEx/AdminPanel/public/${listing.property_image}` : 'https://via.placeholder.com/600x400.png?text=No+Image';

                        let priceText = '';
                        if (listing.price_total) {
                            priceText = `
                                <p class="text-2xl font-extrabold text-brand-primary">₹${new Intl.NumberFormat().format(listing.price_total)}</p>
                                <p class="text-xs text-gray-500 mt-1">for ${listing.percentage_available}% share</p>
                            `;
                        } else {
                            const proRataPrice = (parseFloat(listing.estimated_value) * parseFloat(listing.percentage_available) / 100).toFixed(2);
                            priceText = `
                                <p class="text-2xl font-extrabold text-brand-primary">₹${new Intl.NumberFormat().format(proRataPrice)}</p>
                                <p class="text-xs text-gray-500 mt-1">Pro-rata for ${listing.percentage_available}% share</p>
                            `;
                        }

                        // Populate the card's inner HTML
                        cardWrapper.innerHTML = `
                            <img src="${propertyImage}" alt="${listing.property_name}" class="w-full h-48 object-cover rounded-2xl mb-4">
                            <h3 class="text-xl font-bold text-gray-900 truncate mb-1">${listing.property_name}</h3>
                            <p class="text-sm font-medium text-brand-primary uppercase tracking-wide mb-2">${listing.property_type}</p>
                            <p class="text-sm text-gray-600 mb-4 truncate"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.8A2 2 0 0112 21.414V19a2 2 0 00-2-2H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2v12z" /></svg>${listing.location}</p>
                            ${priceText.trim()}
                        `;

                        
                        cardWrapper.addEventListener('click', (event) => {
                            if (isUserVerified) {
                                window.location.href = `/PropEx/UserPanel/templates/singlePropertyDetail.php?listing_id=${listing.listing_id}`;
                            } else {
                                event.preventDefault();
                                alert('Please complete your profile verification to view property details.');
                            }
                        });

                        if (!isUserVerified) {
                            cardWrapper.style.cursor = 'not-allowed';
                            cardWrapper.classList.add('opacity-50');
                        }

                        propertyGrid.appendChild(cardWrapper);
                    });
                } else {
                    propertyGrid.innerHTML = '<p class="text-center col-span-full text-gray-500">No active listings found.</p>';
                }
            } catch (error) {
                console.error('Error fetching listings:', error);
                propertyGrid.innerHTML = '<p class="text-center col-span-full text-red-500">Failed to load listings. Please try again later.</p>';
            }
        }

        // 3. Add a click handler to the "List a Property" button
        listPropertyBtn.addEventListener('click', (event) => {
            if (isUserVerified) {
                window.location.href = '/PropEx/UserPanel/templates/listProperties.php';
            } else {
                event.preventDefault();
                alert('Please complete your profile verification to list a property.');
            }
        });

        // Initial call to fetch data
        fetchListings();
    });
</script>
<?php include __DIR__ . '/../src/includes/footer.php'; ?>