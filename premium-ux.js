// Premium UX Enhancements for Huuto247
(() => {
  'use strict';

  // Performance monitoring
  const performanceMonitor = {
    init() {
      this.trackPageLoad();
      this.trackUserInteractions();
      this.optimizeImages();
      this.preloadCriticalResources();
    },

    trackPageLoad() {
      window.addEventListener('load', () => {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        console.log(`Page loaded in ${loadTime}ms`);
        
        // Log performance issues
        if (loadTime > 3000) {
          console.warn('⚠️ Slow page load detected:', loadTime + 'ms');
        }
      });
    },

    trackUserInteractions() {
      // Track click responsiveness
      document.addEventListener('click', (e) => {
        const start = performance.now();
        requestAnimationFrame(() => {
          const responseTime = performance.now() - start;
          if (responseTime > 100) {
            console.warn('⚠️ Slow interaction:', e.target, responseTime + 'ms');
          }
        });
      });
    },

    optimizeImages() {
      // Lazy loading for images
      if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const img = entry.target;
              if (img.dataset.src) {
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
              }
            }
          });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
          imageObserver.observe(img);
        });
      }
    },

    preloadCriticalResources() {
      // Preload critical CSS and fonts
      const criticalResources = [
        { href: '/styles.css', as: 'style' },
        { href: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', as: 'style' }
      ];

      criticalResources.forEach(resource => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.href = resource.href;
        link.as = resource.as;
        document.head.appendChild(link);
      });
    }
  };

  // Enhanced user interaction feedback
  const interactionEnhancements = {
    init() {
      this.addButtonFeedback();
      this.enhanceFormValidation();
      this.addLoadingStates();
      this.improveKeyboardNavigation();
    },

    addButtonFeedback() {
      // Add visual feedback for all buttons
      document.addEventListener('click', (e) => {
        const button = e.target.closest('button, .btn, [role="button"]');
        if (button && !button.disabled) {
          button.style.transform = 'scale(0.98)';
          setTimeout(() => {
            button.style.transform = '';
          }, 150);
        }
      });
    },

    enhanceFormValidation() {
      // Real-time form validation
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
          const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
          let isValid = true;

          inputs.forEach(input => {
            if (!input.value.trim()) {
              this.showFieldError(input, 'Tämä kenttä on pakollinen');
              isValid = false;
            } else {
              this.clearFieldError(input);
            }
          });

          if (!isValid) {
            e.preventDefault();
            this.showFormError(form, 'Tarkista pakollisten kenttien täyttö');
          }
        });
      });
    },

    showFieldError(field, message) {
      field.classList.add('error');
      let errorMsg = field.parentNode.querySelector('.field-error');
      if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'field-error';
        field.parentNode.insertBefore(errorMsg, field.nextSibling);
      }
      errorMsg.textContent = message;
    },

    clearFieldError(field) {
      field.classList.remove('error');
      const errorMsg = field.parentNode.querySelector('.field-error');
      if (errorMsg) {
        errorMsg.remove();
      }
    },

    showFormError(form, message) {
      let errorMsg = form.querySelector('.form-error');
      if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'form-error';
        form.insertBefore(errorMsg, form.firstChild);
      }
      errorMsg.textContent = message;
    },

    addLoadingStates() {
      // Add loading states for async operations
      document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-loading-target]');
        if (trigger) {
          const target = document.querySelector(trigger.dataset.loadingTarget);
          if (target) {
            target.classList.add('loading');
            setTimeout(() => target.classList.remove('loading'), 2000);
          }
        }
      });
    },

    improveKeyboardNavigation() {
      // Enhanced keyboard navigation
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
          document.body.classList.add('keyboard-nav');
        }
      });

      document.addEventListener('mousedown', () => {
        document.body.classList.remove('keyboard-nav');
      });

      // ESC key handling
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          // Close modals
          document.querySelectorAll('dialog[open], .modal.open').forEach(modal => {
            if (modal.close) modal.close();
            else modal.classList.remove('open');
          });
          
          // Clear search
          const search = document.querySelector('input[type="search"]');
          if (search && search === document.activeElement) {
            search.value = '';
            search.blur();
          }
        }
      });
    }
  };

  // Progressive enhancement features
  const progressiveEnhancements = {
    init() {
      this.enhanceAuctionCountdowns();
      this.addSmartSearch();
      this.improveImageGalleries();
      this.addMicroInteractions();
    },

    enhanceAuctionCountdowns() {
      // Enhanced countdown timers
      document.querySelectorAll('.countdown').forEach(countdown => {
        const endTime = countdown.dataset.endTime;
        if (endTime) {
          this.updateCountdown(countdown, new Date(endTime));
          setInterval(() => {
            this.updateCountdown(countdown, new Date(endTime));
          }, 1000);
        }
      });
    },

    updateCountdown(element, endTime) {
      const now = new Date().getTime();
      const distance = endTime.getTime() - now;

      if (distance < 0) {
        element.innerHTML = '<span class="ended">Päättynyt</span>';
        element.classList.add('ended');
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Color coding based on time left
      element.classList.remove('urgent', 'warning');
      if (distance < 3600000) { // Less than 1 hour
        element.classList.add('urgent');
      } else if (distance < 86400000) { // Less than 1 day
        element.classList.add('warning');
      }

      if (days > 0) {
        element.innerHTML = `${days}p ${hours}t ${minutes}min`;
      } else if (hours > 0) {
        element.innerHTML = `${hours}t ${minutes}min ${seconds}s`;
      } else {
        element.innerHTML = `${minutes}min ${seconds}s`;
      }
    },

    addSmartSearch() {
      // Smart search with suggestions
      const searchInput = document.querySelector('input[type="search"], .search-input');
      if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
            this.showSearchSuggestions(e.target, e.target.value);
          }, 300);
        });
      }
    },

    showSearchSuggestions(input, query) {
      if (query.length < 2) return;
      
      // Mock suggestions - in real app, fetch from API
      const suggestions = [
        'BMW', 'Mercedes', 'Audi', 'Toyota', 'Volkswagen',
        'Asunto Helsinki', 'Asunto Tampere', 'Asunto Turku',
        'Työkoneet', 'Kaivinkone', 'Traktori',
        'iPhone', 'Samsung', 'MacBook'
      ].filter(s => s.toLowerCase().includes(query.toLowerCase()));

      this.renderSuggestions(input, suggestions);
    },

    renderSuggestions(input, suggestions) {
      // Remove existing suggestions
      const existing = input.parentNode.querySelector('.search-suggestions');
      if (existing) existing.remove();

      if (suggestions.length === 0) return;

      const suggestionsDiv = document.createElement('div');
      suggestionsDiv.className = 'search-suggestions';
      suggestions.slice(0, 5).forEach(suggestion => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.textContent = suggestion;
        item.addEventListener('click', () => {
          input.value = suggestion;
          suggestionsDiv.remove();
          // Trigger search
          input.form?.dispatchEvent(new Event('submit'));
        });
        suggestionsDiv.appendChild(item);
      });

      input.parentNode.style.position = 'relative';
      input.parentNode.appendChild(suggestionsDiv);
    },

    improveImageGalleries() {
      // Enhanced image gallery functionality
      document.querySelectorAll('.image-gallery').forEach(gallery => {
        this.addGalleryKeyboardNav(gallery);
        this.addGallerySwipeSupport(gallery);
      });
    },

    addGalleryKeyboardNav(gallery) {
      gallery.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
          gallery.querySelector('.gallery-prev')?.click();
        } else if (e.key === 'ArrowRight') {
          gallery.querySelector('.gallery-next')?.click();
        }
      });
    },

    addGallerySwipeSupport(gallery) {
      // Touch swipe support for galleries
      let startX = 0;
      let startY = 0;
      
      gallery.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
      });
      
      gallery.addEventListener('touchend', (e) => {
        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;
        const diffX = startX - endX;
        const diffY = startY - endY;
        
        // Only trigger if horizontal swipe is significant
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
          if (diffX > 0) {
            gallery.querySelector('.gallery-next')?.click();
          } else {
            gallery.querySelector('.gallery-prev')?.click();
          }
        }
      });
    },

    addMicroInteractions() {
      // Add subtle micro-interactions
      document.querySelectorAll('.category-card-premium, .auction-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
          card.style.transition = 'all 0.3s ease';
        });
      });

      // Parallax effect for hero sections
      window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.hero-section');
        if (parallax) {
          parallax.style.transform = `translateY(${scrolled * 0.1}px)`;
        }
      });
    }
  };

  // Error handling and monitoring
  const errorHandling = {
    init() {
      this.setupGlobalErrorHandler();
      this.trackUserErrors();
      this.addFallbackFeatures();
    },

    setupGlobalErrorHandler() {
      window.addEventListener('error', (e) => {
        console.error('JavaScript Error:', e.error);
        this.logError('javascript', e.error.message);
      });

      window.addEventListener('unhandledrejection', (e) => {
        console.error('Unhandled Promise Rejection:', e.reason);
        this.logError('promise', e.reason);
      });
    },

    trackUserErrors() {
      // Track user-facing errors
      document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (link && link.href.includes('404')) {
          this.logError('navigation', '404 page accessed');
        }
      });
    },

    logError(type, message) {
      // In production, send to analytics service
      console.log(`Error logged: ${type} - ${message}`);
    },

    addFallbackFeatures() {
      // CSS Grid fallback
      if (!CSS.supports('display', 'grid')) {
        document.body.classList.add('no-grid-support');
      }

      // Modern JS features fallback
      if (!window.fetch) {
        console.warn('Fetch API not supported, consider adding a polyfill');
      }
    }
  };

  // Initialize all UX enhancements
  document.addEventListener('DOMContentLoaded', () => {
    performanceMonitor.init();
    interactionEnhancements.init();
    progressiveEnhancements.init();
    errorHandling.init();
    
    console.log('🚀 Premium UX enhancements loaded');
  });

  // Add custom CSS for UX enhancements
  const uxStyles = `
    <style>
    /* UX Enhancement Styles */
    .loading {
      position: relative;
      pointer-events: none;
    }
    
    .loading::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .loading::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 20px;
      height: 20px;
      border: 2px solid var(--primary-500);
      border-top: 2px solid transparent;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      z-index: 1;
    }
    
    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    .field-error {
      color: var(--danger-600);
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    
    .form-error {
      background: var(--danger-50);
      border: 1px solid var(--danger-200);
      color: var(--danger-700);
      padding: 0.75rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
    }
    
    input.error,
    select.error,
    textarea.error {
      border-color: var(--danger-500);
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .search-suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid var(--gray-200);
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      z-index: 50;
      max-height: 200px;
      overflow-y: auto;
    }
    
    .suggestion-item {
      padding: 0.75rem;
      cursor: pointer;
      border-bottom: 1px solid var(--gray-100);
    }
    
    .suggestion-item:hover {
      background: var(--gray-50);
    }
    
    .suggestion-item:last-child {
      border-bottom: none;
    }
    
    .countdown.urgent {
      color: var(--danger-600);
      font-weight: 600;
    }
    
    .countdown.warning {
      color: var(--warning-600);
      font-weight: 500;
    }
    
    .countdown.ended {
      color: var(--gray-500);
      font-style: italic;
    }
    
    .keyboard-nav *:focus {
      outline: 2px solid var(--primary-500);
      outline-offset: 2px;
    }
    
    .no-grid-support .categories-grid {
      display: block;
    }
    
    .no-grid-support .category-card-premium {
      display: inline-block;
      width: calc(50% - 1rem);
      margin: 0.5rem;
      vertical-align: top;
    }
    
    @media (max-width: 768px) {
      .no-grid-support .category-card-premium {
        width: calc(100% - 1rem);
      }
    }
    
    /* Lazy loading placeholder */
    img.lazy {
      background: linear-gradient(90deg, #f0f0f0 25%, transparent 37%, #f0f0f0 63%);
      background-size: 400% 100%;
      animation: shimmer 1.5s ease-in-out infinite;
    }
    
    @keyframes shimmer {
      0% { background-position: 100% 0; }
      100% { background-position: -100% 0; }
    }
    </style>
  `;
  
  document.head.insertAdjacentHTML('beforeend', uxStyles);

})();