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

// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = 'none';
    }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Animate stats when in viewport
const stats = document.querySelectorAll('.stat-item h3');
const animateStats = () => {
    stats.forEach(stat => {
        const rect = stat.getBoundingClientRect();
        const isInViewport = rect.top <= window.innerHeight && rect.bottom >= 0;
        
        if (isInViewport) {
            stat.style.opacity = '1';
            stat.style.transform = 'translateY(0)';
        }
    });
};

// Initial animation setup
stats.forEach(stat => {
    stat.style.opacity = '0';
    stat.style.transform = 'translateY(20px)';
    stat.style.transition = 'all 0.6s ease';
});

// Listen for scroll to trigger animations
window.addEventListener('scroll', animateStats);
// Trigger once on load
animateStats();

// Pricing Toggle
const billingToggle = document.getElementById('billing-toggle');
if (billingToggle) {
    billingToggle.addEventListener('change', function() {
        const prices = document.querySelectorAll('.price .amount');
        prices.forEach(price => {
            const monthlyPrice = price.getAttribute('data-monthly');
            const yearlyPrice = price.getAttribute('data-yearly');
            if (this.checked) {
                // Yearly pricing
                price.textContent = yearlyPrice;
            } else {
                // Monthly pricing
                price.textContent = monthlyPrice;
            }
        });
    });
}

// Contact Form Validation
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        
        if (!name || !email || !message) {
            alert('Please fill in all required fields.');
            return;
        }
        
        if (!isValidEmail(email)) {
            alert('Please enter a valid email address.');
            return;
        }
        
        // If validation passes, you can submit the form
        // For now, just show a success message
        alert('Thank you for your message! We will get back to you soon.');
        this.reset();
    });
}

// Email validation helper
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
