<?php
// PropEx/UserPanel/list-property.php
include __DIR__ . '/../src/includes/header.php';
include __DIR__ . '/../src/includes/backButton.php';
?>
<main class="bg-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl p-8 md:p-12">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-2">List a New Property</h1>
            <p class="text-gray-600">Fill out the details below to add your property to the marketplace.</p>
        </div>

        <form id="listing-form" class="space-y-6">
            <div>
                <label for="property_name" class="block text-sm font-medium text-gray-700">Property Name</label>
                <input type="text" name="property_name" id="property_name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary p-3">
            </div>

            <div>
                <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type</label>
                <select name="property_type" id="property_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary p-3">
                    <option value="Residential">Residential</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Land">Land</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location" id="location" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary p-3">
            </div>

            <div>
                <label for="estimated_value" class="block text-sm font-medium text-gray-700">Estimated Value (₹)</label>
                <input type="number" name="estimated_value" id="estimated_value" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary p-3" min="0">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary p-3"></textarea>
            </div>

            <div>
                <label for="property_image" class="block text-sm font-medium text-gray-700">Property Image</label>
                <input type="file" name="property_image" id="property_image" required
                    class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-brand-primary file:text-white hover:file:bg-brand-secondary transition cursor-pointer">
            </div>

            <div id="form-message" class="mt-4 text-center text-sm font-medium hidden"></div>

            <button type="submit" id="submit-button"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-primary hover:bg-brand-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition">
                <span id="button-text">List Property</span>
                <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
    </div>
</main>
<script>
    document.getElementById('listing-form').addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const formMessage = document.getElementById('form-message');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const loadingSpinner = document.getElementById('loading-spinner');

        formMessage.classList.add('hidden');
        formMessage.classList.remove('text-green-600', 'text-red-600');
        
        // Show loading state
        submitButton.disabled = true;
        buttonText.textContent = 'Listing...';
        loadingSpinner.classList.remove('hidden');

        try {
            const response = await fetch('/PropEx/UserPanel/src/api/Property/create_property.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                formMessage.textContent = 'Property listed successfully! Redirecting to your portfolio...';
                formMessage.classList.remove('hidden');
                formMessage.classList.add('text-green-600');
                form.reset();
                setTimeout(() => {
                    window.location.href = '/PropEx/UserPanel/templates/portfolio.php';
                }, 2000);
            } else {
                throw new Error(result.message || 'Something went wrong.');
            }
        } catch (error) {
            formMessage.textContent = `Error: ${error.message}`;
            formMessage.classList.remove('hidden');
            formMessage.classList.add('text-red-600');
        } finally {
            // Revert to original state
            submitButton.disabled = false;
            buttonText.textContent = 'List Property';
            loadingSpinner.classList.add('hidden');
        }
    });
</script>
<?php include __DIR__ . '/../src/includes/footer.php'; ?>
