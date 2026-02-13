// Huuto - Main JavaScript

// Cookie Banner
document.addEventListener('DOMContentLoaded', function() {
    const cookieBanner = document.getElementById('cookie-banner');
    const acceptCookies = document.getElementById('accept-cookies');
    
    if (cookieBanner && !localStorage.getItem('cookies-accepted')) {
        cookieBanner.classList.add('show');
    }
    
    if (acceptCookies) {
        acceptCookies.addEventListener('click', function() {
            localStorage.setItem('cookies-accepted', 'true');
            cookieBanner.classList.remove('show');
        });
    }
});

// Countdown Timers
function updateCountdowns() {
    const countdowns = document.querySelectorAll('[data-countdown]');
    
    countdowns.forEach(function(element) {
        const endTime = new Date(element.getAttribute('data-countdown')).getTime();
        const now = new Date().getTime();
        const distance = endTime - now;
        
        if (distance < 0) {
            element.textContent = 'Päättynyt';
            element.classList.remove('countdown');
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        let text = '';
        if (days > 0) {
            text = days + ' pv ' + hours + ' t';
        } else if (hours > 0) {
            text = hours + ' t ' + minutes + ' min';
        } else {
            text = minutes + ' min ' + seconds + ' s';
        }
        
        element.textContent = text;
        
        // Add warning class if less than 1 hour
        if (distance < 3600000) {
            element.classList.add('badge-danger');
        }
    });
}

// Update countdowns every second
setInterval(updateCountdowns, 1000);
updateCountdowns();

// Image Lazy Loading
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src || img.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}

// Bid Form Validation
const bidForm = document.getElementById('bid-form');
if (bidForm) {
    bidForm.addEventListener('submit', function(e) {
        const amountInput = document.getElementById('bid-amount');
        const minBid = parseFloat(amountInput.getAttribute('data-min-bid'));
        const amount = parseFloat(amountInput.value);
        
        if (amount < minBid) {
            e.preventDefault();
            alert('Huudon tulee olla vähintään ' + minBid.toFixed(2) + ' €');
            return false;
        }
    });
}

// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', function() {
        mobileMenu.classList.toggle('show');
    });
}

// Image Gallery
const galleryImages = document.querySelectorAll('.listing-images img');
const mainImage = document.getElementById('main-image');

if (mainImage && galleryImages.length > 0) {
    galleryImages.forEach(function(img) {
        img.addEventListener('click', function() {
            mainImage.src = this.src;
            galleryImages.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

// Auto-dismiss alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 5000);

// Search autocomplete placeholder
const searchInput = document.querySelector('.search-bar input[type="search"]');
if (searchInput) {
    const placeholders = [
        'Etsi ilmoituksia...',
        'Esim. polkupyörä',
        'Esim. sohva',
        'Esim. auto',
        'Etsi kategorioista...'
    ];
    let currentPlaceholder = 0;
    
    setInterval(function() {
        currentPlaceholder = (currentPlaceholder + 1) % placeholders.length;
        searchInput.placeholder = placeholders[currentPlaceholder];
    }, 3000);
}

// Confirm actions
document.querySelectorAll('[data-confirm]').forEach(function(element) {
    element.addEventListener('click', function(e) {
        if (!confirm(this.getAttribute('data-confirm'))) {
            e.preventDefault();
            return false;
        }
    });
});
