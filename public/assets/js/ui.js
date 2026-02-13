/**
 * Huuto - Premium UI Interactions
 * Vanilla JavaScript for smooth, polished interactions
 */

(function() {
  'use strict';

  // ===== Utility Functions =====
  
  const $ = (selector, context = document) => context.querySelector(selector);
  const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));
  
  // ===== Constants =====
  
  // Timing constants
  const FOCUS_DELAY = 100; // Delay for focusing elements after modal/menu open (ms)
  const URGENT_THRESHOLD_MS = 300000; // 5 minutes in milliseconds for urgent countdown
  const TOAST_DEFAULT_DURATION = 5000; // Default toast display duration (ms)
  const TOAST_EXIT_ANIMATION_DURATION = 200; // Toast exit animation duration (ms)
  const ALERT_AUTO_DISMISS_DURATION = 5000; // Alert auto-dismiss duration (ms)
  const COUNTDOWN_UPDATE_INTERVAL = 1000; // Countdown timer update interval (ms)
  const FORM_SUBMIT_FALLBACK_TIMEOUT = 10000; // Form submit button re-enable fallback (ms)
  
  // Scroll thresholds
  const HEADER_SHRINK_THRESHOLD = 100; // Scroll distance before header shrinks (px)
  const SCROLL_TO_TOP_THRESHOLD = 600; // Scroll distance before scroll-to-top button appears (px)
  
  // Throttle intervals
  const HEADER_SCROLL_THROTTLE = 100; // Header scroll handler throttle interval (ms)
  const SCROLL_TO_TOP_THROTTLE = 200; // Scroll-to-top visibility check throttle (ms)
  
  // IntersectionObserver threshold
  const REVEAL_ANIMATION_THRESHOLD = 0.1; // Percentage of element visibility to trigger reveal
  
  const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  };
  
  const throttle = (func, limit) => {
    let inThrottle;
    return function(...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  };

  // ===== Theme Toggle (Dark/Light Mode) =====
  
  class ThemeManager {
    constructor() {
      this.theme = localStorage.getItem('theme') || 'auto';
      this.init();
    }
    
    init() {
      this.applyTheme();
      this.createToggleButton();
      
      // Listen for system theme changes
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (this.theme === 'auto') {
          this.applyTheme();
        }
      });
    }
    
    applyTheme() {
      const root = document.documentElement;
      
      if (this.theme === 'dark') {
        root.setAttribute('data-theme', 'dark');
      } else if (this.theme === 'light') {
        root.setAttribute('data-theme', 'light');
      } else {
        root.removeAttribute('data-theme');
      }
    }
    
    toggle() {
      const themes = ['auto', 'light', 'dark'];
      const currentIndex = themes.indexOf(this.theme);
      this.theme = themes[(currentIndex + 1) % themes.length];
      
      localStorage.setItem('theme', this.theme);
      this.applyTheme();
      this.updateToggleButton();
    }
    
    createToggleButton() {
      const nav = $('.header-nav');
      if (!nav) return;
      
      const button = document.createElement('button');
      button.className = 'theme-toggle';
      button.setAttribute('aria-label', 'Toggle theme');
      button.innerHTML = this.getIcon();
      button.addEventListener('click', () => this.toggle());
      
      nav.insertBefore(button, nav.firstChild);
      this.toggleButton = button;
    }
    
    updateToggleButton() {
      if (this.toggleButton) {
        this.toggleButton.innerHTML = this.getIcon();
      }
    }
    
    getIcon() {
      const icons = {
        auto: '🌓',
        light: '☀️',
        dark: '🌙'
      };
      return icons[this.theme] || icons.auto;
    }
  }

  // ===== Sticky Header with Shrink Effect =====
  
  class StickyHeader {
    constructor() {
      this.header = $('.header');
      this.lastScroll = 0;
      this.init();
    }
    
    init() {
      if (!this.header) return;
      
      const handleScroll = throttle(() => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > HEADER_SHRINK_THRESHOLD) {
          this.header.classList.add('header-shrink');
        } else {
          this.header.classList.remove('header-shrink');
        }
        
        this.lastScroll = currentScroll;
      }, HEADER_SCROLL_THROTTLE);
      
      window.addEventListener('scroll', handleScroll, { passive: true });
    }
  }

  // ===== Mobile Menu =====
  
  class MobileMenu {
    constructor() {
      this.init();
    }
    
    init() {
      this.createMobileMenu();
      this.setupEventListeners();
    }
    
    createMobileMenu() {
      const header = $('.header');
      if (!header) return;
      
      const headerNav = $('.header-nav');
      if (!headerNav) return;
      
      // Create mobile menu button
      const button = document.createElement('button');
      button.className = 'mobile-menu-btn';
      button.setAttribute('aria-label', 'Toggle menu');
      button.innerHTML = '<span class="mobile-menu-icon"></span>';
      
      const headerContent = $('.header-content');
      headerContent.appendChild(button);
      
      // Create mobile menu
      const menu = document.createElement('div');
      menu.className = 'mobile-menu';
      menu.id = 'mobile-menu';
      
      // Clone navigation with event delegation
      const nav = document.createElement('nav');
      const links = $$('.header-nav a');
      links.forEach((link, index) => {
        const clone = link.cloneNode(true);
        
        // Forward click events to original link for proper event handling
        clone.addEventListener('click', function(event) {
          // If it's a regular navigation link, let it work normally
          if (!link.onclick && !link.dataset.customHandler) {
            return; // Let the link work naturally
          }
          
          // For links with custom handlers, forward to original
          event.preventDefault();
          link.click();
        });
        
        nav.appendChild(clone);
      });
      
      menu.appendChild(nav);
      document.body.appendChild(menu);
      
      // Create backdrop
      const backdrop = document.createElement('div');
      backdrop.className = 'mobile-menu-backdrop';
      backdrop.id = 'mobile-menu-backdrop';
      document.body.appendChild(backdrop);
      
      this.button = button;
      this.menu = menu;
      this.backdrop = backdrop;
    }
    
    setupEventListeners() {
      if (!this.button) return;
      
      this.button.addEventListener('click', () => this.toggle());
      this.backdrop.addEventListener('click', () => this.close());
      
      // Close on ESC key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && this.menu.classList.contains('active')) {
          this.close();
        }
      });
      
      // Close on navigation
      $$('a', this.menu).forEach(link => {
        link.addEventListener('click', () => this.close());
      });
      
      // Focus trap
      this.menu.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
          this.trapFocus(e);
        }
      });
    }
    
    toggle() {
      if (this.menu.classList.contains('active')) {
        this.close();
      } else {
        this.open();
      }
    }
    
    open() {
      this.menu.classList.add('active');
      this.backdrop.classList.add('active');
      this.button.classList.add('active');
      
      // Save and disable body scroll
      this.originalBodyOverflow = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      
      // Focus first link
      const firstLink = $('a', this.menu);
      if (firstLink) {
        setTimeout(() => firstLink.focus(), FOCUS_DELAY);
      }
    }
    
    close() {
      this.menu.classList.remove('active');
      this.backdrop.classList.remove('active');
      this.button.classList.remove('active');
      
      // Restore body scroll
      document.body.style.overflow = this.originalBodyOverflow || '';
    }
    
    trapFocus(e) {
      const focusableElements = $$('a, button', this.menu).filter(el => 
        el.offsetParent !== null && !el.disabled
      );
      
      const firstElement = focusableElements[0];
      const lastElement = focusableElements[focusableElements.length - 1];
      
      if (e.shiftKey && document.activeElement === firstElement) {
        e.preventDefault();
        lastElement.focus();
      } else if (!e.shiftKey && document.activeElement === lastElement) {
        e.preventDefault();
        firstElement.focus();
      }
    }
  }

  // ===== Toast Notifications =====
  
  class ToastManager {
    constructor() {
      this.container = this.createContainer();
    }
    
    createContainer() {
      let container = $('.toast-container');
      if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
      }
      return container;
    }
    
    show(message, type = 'info', duration = TOAST_DEFAULT_DURATION) {
      const toast = document.createElement('div');
      toast.className = `toast toast-${type}`;
      
      const content = document.createElement('div');
      content.className = 'toast-content';
      
      const title = document.createElement('div');
      title.className = 'toast-title';
      title.textContent = this.getTitle(type);
      
      const msg = document.createElement('div');
      msg.className = 'toast-message';
      msg.textContent = message;
      
      content.appendChild(title);
      content.appendChild(msg);
      
      const closeBtn = document.createElement('button');
      closeBtn.className = 'toast-close';
      closeBtn.innerHTML = '×';
      closeBtn.setAttribute('aria-label', 'Close');
      closeBtn.addEventListener('click', () => this.remove(toast));
      
      toast.appendChild(content);
      toast.appendChild(closeBtn);
      
      this.container.appendChild(toast);
      
      // Auto-remove
      if (duration > 0) {
        setTimeout(() => this.remove(toast), duration);
      }
      
      return toast;
    }
    
    remove(toast) {
      toast.classList.add('toast-exit');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, TOAST_EXIT_ANIMATION_DURATION);
    }
    
    getTitle(type) {
      const titles = {
        success: 'Onnistui!',
        error: 'Virhe',
        warning: 'Varoitus',
        info: 'Ilmoitus'
      };
      return titles[type] || titles.info;
    }
    
    success(message, duration) {
      return this.show(message, 'success', duration);
    }
    
    error(message, duration) {
      return this.show(message, 'error', duration);
    }
    
    warning(message, duration) {
      return this.show(message, 'warning', duration);
    }
    
    info(message, duration) {
      return this.show(message, 'info', duration);
    }
  }

  // ===== Modal Component =====
  
  class Modal {
    constructor(id, options = {}) {
      this.id = id;
      this.options = {
        closeOnBackdrop: true,
        closeOnEscape: true,
        ...options
      };
      this.create();
    }
    
    create() {
      // Create backdrop
      this.backdrop = document.createElement('div');
      this.backdrop.className = 'modal-backdrop';
      this.backdrop.id = `${this.id}-backdrop`;
      
      // Create modal
      this.modal = document.createElement('div');
      this.modal.className = 'modal';
      this.modal.id = this.id;
      this.modal.setAttribute('role', 'dialog');
      this.modal.setAttribute('aria-modal', 'true');
      
      document.body.appendChild(this.backdrop);
      document.body.appendChild(this.modal);
      
      this.setupEventListeners();
    }
    
    setupEventListeners() {
      // Close on backdrop click
      if (this.options.closeOnBackdrop) {
        this.backdrop.addEventListener('click', () => this.close());
      }
      
      // Close on ESC key
      if (this.options.closeOnEscape) {
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && this.isOpen()) {
            this.close();
          }
        });
      }
    }
    
    setContent(html) {
      this.modal.innerHTML = html;
      
      // Setup close button
      const closeBtn = $('.modal-close', this.modal);
      if (closeBtn) {
        closeBtn.addEventListener('click', () => this.close());
      }
    }
    
    open() {
      this.backdrop.classList.add('active');
      this.modal.classList.add('active');
      
      // Save and disable body scroll
      this.originalBodyOverflow = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      
      // Focus first focusable element
      setTimeout(() => {
        const firstFocusable = $('button, a, input, textarea', this.modal);
        if (firstFocusable) {
          firstFocusable.focus();
        }
      }, FOCUS_DELAY);
    }
    
    close() {
      this.backdrop.classList.remove('active');
      this.modal.classList.remove('active');
      
      // Restore body scroll
      document.body.style.overflow = this.originalBodyOverflow || '';
    }
    
    isOpen() {
      return this.modal.classList.contains('active');
    }
    
    destroy() {
      if (this.backdrop.parentNode) {
        this.backdrop.parentNode.removeChild(this.backdrop);
      }
      if (this.modal.parentNode) {
        this.modal.parentNode.removeChild(this.modal);
      }
    }
  }

  // ===== Dropdown Component =====
  
  class Dropdown {
    constructor(element) {
      this.element = element;
      this.button = $('.dropdown-toggle', element) || element.querySelector('button');
      this.menu = $('.dropdown-menu', element);
      this.init();
    }
    
    init() {
      if (!this.button || !this.menu) return;
      
      this.button.addEventListener('click', (e) => {
        e.stopPropagation();
        this.toggle();
      });
      
      // Close on outside click
      document.addEventListener('click', () => {
        if (this.isOpen()) {
          this.close();
        }
      });
      
      // Close on ESC key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && this.isOpen()) {
          this.close();
        }
      });
    }
    
    toggle() {
      if (this.isOpen()) {
        this.close();
      } else {
        this.open();
      }
    }
    
    open() {
      this.menu.classList.add('active');
    }
    
    close() {
      this.menu.classList.remove('active');
    }
    
    isOpen() {
      return this.menu.classList.contains('active');
    }
  }

  // ===== Scroll to Top Button =====
  
  class ScrollToTop {
    constructor() {
      this.createButton();
      this.init();
    }
    
    createButton() {
      this.button = document.createElement('button');
      this.button.className = 'scroll-to-top';
      this.button.setAttribute('aria-label', 'Scroll to top');
      this.button.innerHTML = '↑';
      document.body.appendChild(this.button);
    }
    
    init() {
      const handleScroll = throttle(() => {
        if (window.pageYOffset > SCROLL_TO_TOP_THRESHOLD) {
          this.button.classList.add('visible');
        } else {
          this.button.classList.remove('visible');
        }
      }, SCROLL_TO_TOP_THROTTLE);
      
      window.addEventListener('scroll', handleScroll, { passive: true });
      
      this.button.addEventListener('click', () => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    }
  }

  // ===== IntersectionObserver Reveal Animations =====
  
  class RevealAnimations {
    constructor() {
      this.init();
    }
    
    init() {
      const options = {
        root: null,
        rootMargin: '0px',
        threshold: REVEAL_ANIMATION_THRESHOLD
      };
      
      this.observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            this.observer.unobserve(entry.target);
          }
        });
      }, options);
      
      // Observe all cards
      $$('.card').forEach(card => {
        card.classList.add('reveal');
        this.observer.observe(card);
      });
    }
  }

  // ===== Image Lazy Loading =====
  
  class LazyLoadImages {
    constructor() {
      this.init();
    }
    
    init() {
      const images = $$('img[loading="lazy"]');
      
      if ('loading' in HTMLImageElement.prototype) {
        // Native lazy loading supported
        images.forEach(img => {
          if (img.dataset.src) {
            img.src = img.dataset.src;
          }
        });
      } else {
        // Fallback to IntersectionObserver
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const img = entry.target;
              if (img.dataset.src) {
                img.src = img.dataset.src;
              }
              img.classList.add('loaded');
              observer.unobserve(img);
            }
          });
        });
        
        images.forEach(img => observer.observe(img));
      }
    }
  }

  // ===== Enhanced Countdown Timers =====
  
  class CountdownTimer {
    constructor() {
      this.timers = [];
      this.init();
    }
    
    init() {
      const elements = $$('[data-countdown]');
      
      elements.forEach(element => {
        const endTime = new Date(element.getAttribute('data-countdown')).getTime();
        this.timers.push({ element, endTime });
      });
      
      if (this.timers.length > 0) {
        this.update();
        setInterval(() => this.update(), COUNTDOWN_UPDATE_INTERVAL);
      }
    }
    
    update() {
      const now = Date.now();
      
      this.timers.forEach(({ element, endTime }) => {
        const distance = endTime - now;
        
        if (distance < 0) {
          element.textContent = 'Päättynyt';
          element.classList.remove('countdown', 'countdown-urgent');
          return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        let text = '';
        if (days > 0) {
          text = `${days} pv ${hours} t`;
        } else if (hours > 0) {
          text = `${hours} t ${minutes} min`;
        } else {
          text = `${minutes} min ${seconds} s`;
        }
        
        element.textContent = text;
        
        // Add urgent class if less than URGENT_THRESHOLD_MS (5 minutes)
        if (distance < URGENT_THRESHOLD_MS) {
          element.classList.add('countdown-urgent');
        }
      });
    }
  }

  // ===== Skeleton Loader =====
  
  function createSkeletonLoader(type = 'card') {
    const skeleton = document.createElement('div');
    skeleton.className = 'skeleton-card';
    
    if (type === 'card') {
      skeleton.innerHTML = `
        <div class="skeleton skeleton-image"></div>
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text skeleton-text-short"></div>
      `;
    }
    
    return skeleton;
  }

  // ===== Form Enhancements =====
  
  class FormEnhancements {
    constructor() {
      this.init();
    }
    
    init() {
      // Auto-dismiss alerts
      this.autoDismissAlerts();
      
      // Enhance forms
      $$('form').forEach(form => {
        this.enhanceForm(form);
      });
    }
    
    autoDismissAlerts() {
      const alerts = $$('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          alert.style.transition = 'opacity 0.5s, transform 0.5s';
          alert.style.opacity = '0';
          alert.style.transform = 'translateY(-20px)';
          setTimeout(() => {
            if (alert.parentNode) {
              alert.parentNode.removeChild(alert);
            }
          }, 500);
        }, ALERT_AUTO_DISMISS_DURATION);
      });
    }
    
    enhanceForm(form) {
      // Add loading state on submit
      form.addEventListener('submit', function() {
        const submitBtn = $('button[type="submit"]', form);
        if (submitBtn && !submitBtn.disabled) {
          submitBtn.classList.add('btn-loading');
          submitBtn.disabled = true;
          
          // Fallback: re-enable button if submission doesn't complete
          setTimeout(function() {
            if (submitBtn.isConnected && submitBtn.disabled) {
              submitBtn.disabled = false;
              submitBtn.classList.remove('btn-loading');
            }
          }, FORM_SUBMIT_FALLBACK_TIMEOUT);
        }
      });
    }
  }

  // ===== Image Gallery =====
  
  class ImageGallery {
    constructor() {
      this.init();
    }
    
    init() {
      const thumbnails = $$('.thumbnail');
      const mainImage = $('.main-image');
      
      if (!mainImage || thumbnails.length === 0) return;
      
      thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
          const img = $('img', thumbnail);
          if (img) {
            // Resolve image source (handle lazy-loaded images)
            const imgSrc = img.src || img.dataset.src || img.getAttribute('src');
            if (imgSrc) {
              mainImage.src = imgSrc;
            }
            
            // Update active state
            $$('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
          }
        });
        
        // Set first as active
        if (index === 0) {
          thumbnail.classList.add('active');
        }
      });
    }
  }

  // ===== Smooth Anchor Scrolling =====
  
  function initSmoothScrolling() {
    $$('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        const target = $(href);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
  }

  // ===== Cookie Banner =====
  
  function initCookieBanner() {
    const banner = $('#cookie-banner');
    const acceptBtn = $('#accept-cookies');
    
    if (!banner) return;
    
    if (!localStorage.getItem('cookies-accepted')) {
      banner.classList.add('show');
    }
    
    if (acceptBtn) {
      acceptBtn.addEventListener('click', () => {
        localStorage.setItem('cookies-accepted', 'true');
        banner.classList.remove('show');
      });
    }
  }

  // ===== Initialize All Components =====
  
  function init() {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
      return;
    }
    
    // Initialize all components
    new ThemeManager();
    new StickyHeader();
    new MobileMenu();
    new ScrollToTop();
    new RevealAnimations();
    new LazyLoadImages();
    new CountdownTimer();
    new FormEnhancements();
    new ImageGallery();
    
    initSmoothScrolling();
    initCookieBanner();
    
    // Initialize dropdowns
    $$('.dropdown').forEach(el => new Dropdown(el));
    
    // Global toast manager
    window.toast = new ToastManager();
    
    // Global modal factory
    window.createModal = (id, options) => new Modal(id, options);
    
    // Expose utility to create skeleton loaders
    window.createSkeletonLoader = createSkeletonLoader;
  }
  
  // Start initialization
  init();
  
})();
