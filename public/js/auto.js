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
