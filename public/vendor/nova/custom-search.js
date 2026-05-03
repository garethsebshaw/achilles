function updateSearchBox(searchValue) {
    console.log('🔍 updateSearchBox triggered with:', searchValue);

    if (window.Nova) {
        console.log('✅ Nova is available');

        // Find the search input field
        const searchInput = document.querySelector('[dusk="search-input"]');

        if (searchInput) {
            console.log('✅ Search input found:', searchInput);

            // Create a custom event handler
            const processSearch = () => {
                // Update value and trigger single input event
                searchInput.value = searchValue;

                // Create and dispatch a custom input event
                const inputEvent = new InputEvent('input', {
                    bubbles: true,
                    cancelable: true,
                    data: searchValue
                });
                searchInput.dispatchEvent(inputEvent);
            };

            // Execute with minimal interaction with Nova's router
            processSearch();
        }
    }

    // Prevent default link behavior
    return false;
}
