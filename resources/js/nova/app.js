console.log('Social login script loaded - initial');

window.addEventListener('load', () => {
    console.log('Window loaded');

    // Log the current URL and path
    console.log('Current path:', window.location.pathname);

    // Try to find the login form
    const form = document.querySelector('form');
    console.log('Found form:', form);

    if (form) {
        console.log('Form found, attempting to add social buttons');

        // Create the social buttons container
        const socialButtons = document.createElement('div');
        socialButtons.className = 'mt-6 text-center';
        socialButtons.setAttribute('id', 'social-login-buttons');
        socialButtons.innerHTML = `
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">Or continue with</span>
                </div>
            </div>
            <div class="flex space-x-4 justify-center mt-4">
                <a href="/auth/google/redirect"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Sign in with Google
                </a>
            </div>
        `;

        // Try to insert after the form
        form.parentNode.appendChild(socialButtons);
        console.log('Social buttons added');
    }
});

// Also try DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded event fired');
});
