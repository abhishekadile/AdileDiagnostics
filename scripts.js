// Basic script to enhance UX
document.addEventListener("DOMContentLoaded", function() {
    const ctaButton = document.querySelector('.cta-button');
    ctaButton.addEventListener('click', function() {
        window.scrollTo({
            top: document.querySelector("#features").offsetTop,
            behavior: "smooth"
        });
    });
});
