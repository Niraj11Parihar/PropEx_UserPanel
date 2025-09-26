<?php
// UserPanel/templates/profile.php
session_start();
require_once __DIR__ . '/../config.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /PropEx/UserPanel/templates/index.php");
    exit();
}
include __DIR__ . '/../src/includes/header.php';
?>
<link rel="stylesheet" href="/PropEx/UserPanel/public/css/style.css">
<main class="min-h-[calc(100vh-80px)] bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl overflow-hidden md:flex">

        <div class="md:w-1/3 bg-white p-6 md:p-10 border-r border-gray-100 flex flex-col items-center justify-center space-y-8">
            <h1 class="text-3xl font-extrabold text-brand-primary mb-6 text-center">My Account</h1>

            <nav class="w-full space-y-4">
                <button data-section="profile"
                    class="tab-link w-full py-4 px-6 flex items-center gap-4 text-gray-700 hover:bg-gray-100 hover:text-brand-primary rounded-xl transition-colors duration-200 active bg-gray-100 text-brand-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4zm0 14.4c-2.73 0-5.1-1.25-6.72-3.24C6.27 14.41 9.07 13 12 13s5.73 1.41 6.72 3.16c-1.62 1.99-3.99 3.24-6.72 3.24z" />
                    </svg>
                    <span class="font-semibold text-lg">Profile</span>
                </button>

                <button data-section="verification"
                    class="tab-link w-full py-4 px-6 flex items-center gap-4 text-gray-700 hover:bg-gray-100 hover:text-brand-primary rounded-xl transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 15.69l-3.34-3.34 1.41-1.41 1.93 1.93 4.85-4.85 1.41 1.41-6.26 6.26z" />
                    </svg>
                    <span class="font-semibold text-lg">Verification</span>
                </button>

                <button data-section="security"
                    class="tab-link w-full py-4 px-6 flex items-center gap-4 text-gray-700 hover:bg-gray-100 hover:text-brand-primary rounded-xl transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" />
                    </svg>
                    <span class="font-semibold text-lg">Security</span>
                </button>
            </nav>

            <div class="mt-auto w-full pt-8">
                <form id="logout-form" method="POST" action="/PropEx/UserPanel/src/api/User/user_data.php" class="w-full">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="w-full py-3 px-6 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors duration-200 font-semibold shadow-md">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="md:w-2/3 p-6 md:p-10 space-y-12">
            <div id="status-message" class="hidden py-3 px-4 rounded-lg font-medium text-center"></div>

            <div id="section-profile" class="tab-content">
                <h2 class="text-3xl font-bold mb-8 text-gray-800 text-center md:text-left">Profile Information</h2>
                <form id="profile-form" class="space-y-6">
                    <input type="hidden" name="update_profile" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="full_name" name="full_name"
                                class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" disabled
                                class="w-full p-3 rounded-lg border border-gray-300 bg-gray-100 cursor-not-allowed text-gray-500">
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number"
                                class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3 bg-brand-primary text-white font-semibold rounded-lg hover:bg-brand-secondary transition-colors duration-200 shadow-lg">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <div id="section-verification" class="tab-content hidden">
                <h2 class="text-3xl font-bold mb-8 text-gray-800 text-center md:text-left">Identity Verification</h2>

                <div class="bg-gray-100 p-6 rounded-xl mb-8 flex items-center justify-between shadow-inner">
                    <span class="text-lg font-medium text-gray-700">Status</span>
                    <span id="verification-status" class="px-4 py-1.5 rounded-full text-sm font-semibold text-white"></span>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 text-center">
                        <label class="block text-lg font-semibold text-gray-800 mb-3">Aadhaar</label>
                        <p id="aadhaar-masked" class="text-sm text-gray-500 font-mono mb-4"></p>
                        <img id="aadhaar-doc-img" src="" alt="Aadhaar Document"
                            class="rounded-lg shadow border border-gray-300 w-full max-h-56 object-contain">
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 text-center">
                        <label class="block text-lg font-semibold text-gray-800 mb-3">PAN</label>
                        <p id="pan-masked" class="text-sm text-gray-500 font-mono mb-4"></p>
                        <img id="pan-doc-img" src="" alt="PAN Document"
                            class="rounded-lg shadow border border-gray-300 w-full max-h-56 object-contain">
                    </div>
                </div>

                <form id="verification-form" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="update_verification" value="1">
                    <h3 class="text-xl font-bold text-gray-800">Update Documents</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="aadhaar_hash" class="block text-sm font-medium text-gray-700 mb-2">Aadhaar Number</label>
                            <input type="text" id="aadhaar_hash" name="aadhaar_hash" placeholder="Enter Aadhaar"
                                class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200">
                        </div>
                        <div>
                            <label for="aadhaar_document" class="block text-sm font-medium text-gray-700 mb-2">Upload Aadhaar</label>
                            <input type="file" id="aadhaar_document" name="aadhaar_document"
                                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-primary file:text-white hover:file:bg-brand-secondary cursor-pointer transition-colors duration-200">
                        </div>
                        <div>
                            <label for="pan_number" class="block text-sm font-medium text-gray-700 mb-2">PAN Number</label>
                            <input type="text" id="pan_number" name="pan_number" placeholder="Enter PAN"
                                class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200">
                        </div>
                        <div>
                            <label for="pan_document" class="block text-sm font-medium text-gray-700 mb-2">Upload PAN</label>
                            <input type="file" id="pan_document" name="pan_document"
                                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-primary file:text-white hover:file:bg-brand-secondary cursor-pointer transition-colors duration-200">
                        </div>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3 bg-brand-primary text-white font-semibold rounded-lg hover:bg-brand-secondary transition-colors duration-200 shadow-lg">
                            Update Verification
                        </button>
                    </div>
                </form>
            </div>

            <div id="section-security" class="tab-content hidden">
                <h2 class="text-3xl font-bold mb-8 text-gray-800 text-center md:text-left">🔒 Change Password</h2>
                <form id="password-form" class="space-y-6">
                    <input type="hidden" name="change_password" value="1">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200">
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200">
                    </div>
                    <div>
                        <label for="confirm_new_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required
                            class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-colors duration-200">
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="w-full md:w-auto px-8 py-3 bg-brand-primary text-white font-semibold rounded-lg hover:bg-brand-secondary transition-colors duration-200 shadow-lg">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const messageBox = document.getElementById('status-message');

        function showMessage(message, isSuccess = true) {
            messageBox.textContent = message;
            messageBox.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
            if (isSuccess) {
                messageBox.classList.add('bg-green-100', 'text-green-800');
            } else {
                messageBox.classList.add('bg-red-100', 'text-red-800');
            }
            // Hide after a few seconds
            setTimeout(() => {
                messageBox.classList.add('hidden');
            }, 5000);
        }

        async function fetchUserData() {
            try {
                // The URL now points to the single API endpoint for both GET and POST
                const response = await fetch('/PropEx/UserPanel/src/api/User/user_data.php');
                const result = await response.json();

                if (response.ok && result.success) {
                    const userData = result.data;

                    // Populate Profile Tab
                    document.getElementById('full_name').value = userData.full_name || '';
                    document.getElementById('email').value = userData.email || '';
                    document.getElementById('phone_number').value = userData.phone_number || '';
                    document.getElementById('address').value = userData.address || '';

                    // Populate Verification Tab
                    const verificationStatus = document.getElementById('verification-status');
                    verificationStatus.textContent = userData.identity_verification_status;
                    verificationStatus.classList.remove('bg-green-600', 'bg-gray-500', 'bg-red-600');
                    if (userData.identity_verification_status === 'Verified') {
                        verificationStatus.classList.add('bg-green-600');
                    } else if (userData.identity_verification_status === 'Pending') {
                        verificationStatus.classList.add('bg-gray-500');
                    } else {
                        verificationStatus.classList.add('bg-red-600');
                    }
                    
                    document.getElementById('aadhaar-masked').textContent = userData.aadhaar_masked || 'Not provided';
                    document.getElementById('pan-masked').textContent = userData.pan_masked || 'Not provided';
                    document.getElementById('aadhaar-doc-img').src = `/PropEx/AdminPanel/public/${userData.aadhaar_document_url}`;
                    document.getElementById('pan-doc-img').src = `/PropEx/AdminPanel/public/${userData.pan_document_url}`;
                    
                } else {
                    showMessage(`Error fetching data: ${result.message}`, false);
                }
            } catch (error) {
                showMessage(`Network error: ${error.message}`, false);
            }
        }

        // Event listeners for form submissions
        document.getElementById('profile-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            try {
                const response = await fetch('/PropEx/UserPanel/src/api/User/user_data.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                showMessage(result.message, result.success);
                if (result.success) {
                    fetchUserData(); // Re-fetch data to update the UI
                }
            } catch (error) {
                showMessage(`Network error: ${error.message}`, false);
            }
        });

        document.getElementById('verification-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            try {
                const response = await fetch('/PropEx/UserPanel/src/api/User/user_data.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                showMessage(result.message, result.success);
                if (result.success) {
                    fetchUserData(); // Re-fetch data to update the UI
                }
            } catch (error) {
                showMessage(`Network error: ${error.message}`, false);
            }
        });

        document.getElementById('password-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            try {
                const response = await fetch('/PropEx/UserPanel/src/api/User/user_data.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                showMessage(result.message, result.success);
            } catch (error) {
                showMessage(`Network error: ${error.message}`, false);
            }
        });
        
        // Add event listener for the logout form
        document.getElementById('logout-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            try {
                const response = await fetch('/PropEx/UserPanel/src/api/User/user_data.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    window.location.href = '/PropEx/UserPanel/templates/index.php';
                } else {
                    showMessage(result.message, false);
                }
            } catch (error) {
                showMessage(`Network error during logout: ${error.message}`, false);
            }
        });


        // Tab functionality (no change here)
        const links = document.querySelectorAll(".tab-link");
        const contents = document.querySelectorAll(".tab-content");
        links.forEach(link => {
            link.addEventListener("click", () => {
                links.forEach(l => l.classList.remove("active", "bg-gray-100", "text-brand-primary"));
                contents.forEach(c => c.classList.add("hidden"));
                link.classList.add("active", "bg-gray-100", "text-brand-primary");
                const targetSection = document.getElementById("section-" + link.dataset.section);
                if (targetSection) {
                    targetSection.classList.remove("hidden");
                }
            });
        });

        // Initial data fetch on page load
        fetchUserData();
    });
</script>
<?php include __DIR__ . '/../src/includes/footer.php'; ?>