const scrollContainer = document.getElementById('bookScroll');
const scrollLeftBtn = document.getElementById('scrollLeft');
const scrollRightBtn = document.getElementById('scrollRight');

// Tombol manual
scrollLeftBtn.addEventListener('click', () => {
    scrollContainer.scrollBy({
        left: -200,
        behavior: 'smooth'
    });
});

scrollRightBtn.addEventListener('click', () => {
    scrollContainer.scrollBy({
        left: 200,
        behavior: 'smooth'
    });
});

// Auto scroll setiap 3 detik
setInterval(() => {
    scrollContainer.scrollBy({
        left: 200,
        behavior: 'smooth'
    });
}, 3000);

// Recommendations scroll functionality
const recommendationScrollContainer = document.getElementById('recommendationScroll');
const recommendationScrollLeftBtn = document.getElementById('recommendationScrollLeft');
const recommendationScrollRightBtn = document.getElementById('recommendationScrollRight');

if (recommendationScrollContainer && recommendationScrollLeftBtn && recommendationScrollRightBtn) {
    // Tombol manual untuk recommendations
    recommendationScrollLeftBtn.addEventListener('click', () => {
        recommendationScrollContainer.scrollBy({
            left: -200,
            behavior: 'smooth'
        });
    });

    recommendationScrollRightBtn.addEventListener('click', () => {
        recommendationScrollContainer.scrollBy({
            left: 200,
            behavior: 'smooth'
        });
    });

    // Auto scroll untuk recommendations setiap 4 detik
    setInterval(() => {
        recommendationScrollContainer.scrollBy({
            left: 200,
            behavior: 'smooth'
        });
    }, 4000);
}
