document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        const chapterFilter = document.querySelector("[dusk='chapter-filter'] select");
        if (chapterFilter) {
            chapterFilter.addEventListener("change", function () {
                Nova.$emit('filter-changed');
            });
        }
    }, 1000);
});
