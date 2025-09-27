<?php
// PropEx/src/includes/back-button.php
?>
<div class="w-full max-w-2xl mb-2 mt-6 px-4">
    <button 
        onclick="if (document.referrer !== '') { window.history.back(); } else { window.location.href='/templates/portfolio.php'; }" 
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-brand-secondary hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back
    </button>
</div>
