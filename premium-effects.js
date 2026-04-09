/**
 * Huuto247 — Premium Visual Effects
 * Hero entrance choreography, scroll-reveal, parallax, animated counters,
 * sticky-header intelligence, and CTA polish.
 *
 * Respects prefers-reduced-motion throughout.
 */
(() => {
  'use strict';

  const prefersReducedMotion =
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ------------------------------------------------------------------ */
  /*  1. HERO ENTRANCE CHOREOGRAPHY                                       */
  /* ------------------------------------------------------------------ */
  function initHeroEntrance() {
    const heroChildren = document.querySelectorAll('[data-hero]');
    if (!heroChildren.length) return;

    if (prefersReducedMotion) {
      heroChildren.forEach((el) => el.classList.add('hero-visible'));
      return;
    }

    // Slight defer so the browser has painted once
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        heroChildren.forEach((el) => el.classList.add('hero-visible'));
      });
    });
  }

  /* ------------------------------------------------------------------ */
  /*  2. SCROLL-REVEAL SYSTEM (IntersectionObserver)                      */
  /* ------------------------------------------------------------------ */
  function initScrollReveal() {
    const targets = document.querySelectorAll('[data-reveal]');
    if (!targets.length) return;

    if (prefersReducedMotion) {
      targets.forEach((el) => el.classList.add('reveal-visible'));
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('reveal-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    targets.forEach((el) => observer.observe(el));
  }

  /* ------------------------------------------------------------------ */
  /*  3. ANIMATED COUNTERS                                                */
  /* ------------------------------------------------------------------ */
  function easeOutQuart(t) {
    return 1 - Math.pow(1 - t, 4);
  }

  function animateCounter(el) {
    const target = parseFloat(el.dataset.countTarget || el.dataset.count || '0');
    const suffix = el.dataset.countSuffix || '';
    const prefix = el.dataset.countPrefix || '';
    const duration = parseInt(el.dataset.countDuration || '1400', 10);
    const decimals = parseInt(el.dataset.countDecimals || '0', 10);
    const start = performance.now();

    function tick(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const value = easeOutQuart(progress) * target;
      el.textContent = prefix + value.toFixed(decimals) + suffix;
      if (progress < 1) requestAnimationFrame(tick);
    }

    requestAnimationFrame(tick);
  }

  function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    // Pre-populate all counters with their final value immediately
    // so there is no blank flash before the IntersectionObserver fires
    counters.forEach((el) => {
      const target = el.dataset.countTarget || el.dataset.count || '0';
      const suffix = el.dataset.countSuffix || '';
      const prefix = el.dataset.countPrefix || '';
      const decimals = parseInt(el.dataset.countDecimals || '0', 10);
      el.textContent = prefix + parseFloat(target).toFixed(decimals) + suffix;
    });

    if (prefersReducedMotion) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.5 }
    );

    counters.forEach((el) => observer.observe(el));
  }

  /* ------------------------------------------------------------------ */
  /*  4. ULTRA-POLISHED STICKY HEADER                                     */
  /*     - Transparent at top, glass on scroll                           */
  /*     - Auto-hide when scrolling down fast, reveal on scroll up       */
  /* ------------------------------------------------------------------ */
  function initStickyHeader() {
    const header = document.getElementById('siteHeader');
    if (!header) return;

    let lastScrollY = window.scrollY;
    let ticking = false;
    const HIDE_THRESHOLD = 120; // px scrolled before hide kicks in
    const SCROLL_SPEED_HIDE = 6; // px per frame that triggers hide

    function onScroll() {
      if (!ticking) {
        requestAnimationFrame(() => {
          const currentY = window.scrollY;
          const delta = currentY - lastScrollY;

          // Scrolled class for glass effect
          header.classList.toggle('scrolled', currentY > 8);

          if (!prefersReducedMotion) {
            if (currentY > HIDE_THRESHOLD && delta > SCROLL_SPEED_HIDE) {
              // Scrolling down — hide header
              header.classList.add('header-hidden');
            } else if (delta < -2) {
              // Scrolling up — reveal header
              header.classList.remove('header-hidden');
            }
          }

          lastScrollY = currentY;
          ticking = false;
        });
        ticking = true;
      }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    // Reveal header on focus within so keyboard users aren't locked out
    header.addEventListener('focusin', () => header.classList.remove('header-hidden'));
  }

  /* ------------------------------------------------------------------ */
  /*  5. CURSOR PARALLAX (desktop only, hero area)                        */
  /* ------------------------------------------------------------------ */
  function initCursorParallax() {
    if (prefersReducedMotion) return;
    if (window.matchMedia('(hover: none)').matches) return; // touch devices

    const layers = document.querySelectorAll('[data-parallax]');
    if (!layers.length) return;

    let rafId = null;
    let targetX = 0;
    let targetY = 0;
    let currentX = 0;
    let currentY = 0;

    document.addEventListener('mousemove', (e) => {
      // Normalize to -1 … +1 relative to viewport centre
      targetX = (e.clientX / window.innerWidth - 0.5) * 2;
      targetY = (e.clientY / window.innerHeight - 0.5) * 2;

      if (!rafId) {
        rafId = requestAnimationFrame(animateParallax);
      }
    });

    function animateParallax() {
      // Lerp towards target for smooth follow
      currentX += (targetX - currentX) * 0.06;
      currentY += (targetY - currentY) * 0.06;

      layers.forEach((layer) => {
        const strength = parseFloat(layer.dataset.parallax || '8');
        const x = currentX * strength;
        const y = currentY * strength;
        layer.style.transform = `translate(${x}px, ${y}px)`;
      });

      // Keep animating while cursor is active
      const stillMoving =
        Math.abs(targetX - currentX) > 0.001 || Math.abs(targetY - currentY) > 0.001;
      rafId = stillMoving ? requestAnimationFrame(animateParallax) : null;
    }
  }

  /* ------------------------------------------------------------------ */
  /*  6. SCROLL PARALLAX for hero background decorations                  */
  /* ------------------------------------------------------------------ */
  function initScrollParallax() {
    if (prefersReducedMotion) return;

    const scrollLayers = document.querySelectorAll('[data-scroll-parallax]');
    if (!scrollLayers.length) return;

    let ticking = false;

    window.addEventListener(
      'scroll',
      () => {
        if (!ticking) {
          requestAnimationFrame(() => {
            const scrollY = window.scrollY;
            scrollLayers.forEach((layer) => {
              const speed = parseFloat(layer.dataset.scrollParallax || '0.3');
              layer.style.transform = `translateY(${scrollY * speed}px)`;
            });
            ticking = false;
          });
          ticking = true;
        }
      },
      { passive: true }
    );
  }

  /* ------------------------------------------------------------------ */
  /*  7. SECTION ENTRANCE (larger sections revealed as a whole)           */
  /* ------------------------------------------------------------------ */
  function initSectionReveal() {
    // Sections with [data-section-reveal] get a single reveal class
    const sections = document.querySelectorAll('[data-section-reveal]');
    if (!sections.length) return;

    if (prefersReducedMotion) {
      sections.forEach((s) => s.classList.add('reveal-visible'));
      return;
    }

    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('reveal-visible');
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.07 }
    );

    sections.forEach((s) => obs.observe(s));
  }

  /* ------------------------------------------------------------------ */
  /*  8. CARD GRID STAGGER REVEAL                                         */
  /* ------------------------------------------------------------------ */
  function initCardReveal() {
    if (prefersReducedMotion) return;

    const grids = document.querySelectorAll('[data-card-grid]');
    if (!grids.length) return;

    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const cards = entry.target.querySelectorAll('.card, .featured-card, .category-card-premium, .cta-block');
          cards.forEach((card, i) => {
            card.style.transitionDelay = `${i * 45}ms`;
            card.classList.add('reveal-visible');
          });
          obs.unobserve(entry.target);
        });
      },
      { threshold: 0.05 }
    );

    grids.forEach((g) => {
      // Prepare cards
      g.querySelectorAll('.card, .featured-card, .category-card-premium, .cta-block').forEach((card) => {
        if (!card.hasAttribute('data-reveal')) {
          card.setAttribute('data-reveal', '');
        }
      });
      obs.observe(g);
    });
  }

  /* ------------------------------------------------------------------ */
  /*  9. SMOOTH SCROLL for anchor links                                   */
  /* ------------------------------------------------------------------ */
  function initSmoothScroll() {
    document.addEventListener('click', (e) => {
      const anchor = e.target.closest('a[href^="#"]');
      if (!anchor) return;

      const href = anchor.getAttribute('href');
      if (!href || href === '#') return;

      const targetId = href.slice(1);
      if (!targetId) return;

      let decodedTargetId;
      try {
        decodedTargetId = decodeURIComponent(targetId);
      } catch (err) {
        return;
      }

      const target = document.getElementById(decodedTargetId);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth' });
    });
  }

  /* ------------------------------------------------------------------ */
  /*  INIT                                                                 */
  /* ------------------------------------------------------------------ */

  /* ------------------------------------------------------------------ */
  /*  10. MAGNETIC BUTTON hover effect on hero CTAs                       */
  /* ------------------------------------------------------------------ */
  function initMagneticButtons() {
    if (prefersReducedMotion) return;
    if (window.matchMedia('(hover: none)').matches) return;

    document.querySelectorAll('.btn-hero-primary, .btn-hero-secondary').forEach((btn) => {
      btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const x = (e.clientX - rect.left - rect.width / 2) * 0.14;
        const y = (e.clientY - rect.top - rect.height / 2) * 0.20;
        btn.style.transform = `translate(${x}px, ${y}px)`;
      });
      btn.addEventListener('mouseleave', () => {
        btn.style.transform = '';
      });
    });
  }

  /* ------------------------------------------------------------------ */
  /*  11. RIPPLE effect on button click                                   */
  /* ------------------------------------------------------------------ */
  function initRipple() {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest(
        '.btn-hero-primary, .btn-primary, .btn-hero-secondary, .search-submit, .load-more'
      );
      if (!btn) return;
      const rect = btn.getBoundingClientRect();
      const ripple = document.createElement('span');
      ripple.className = 'btn-ripple';
      ripple.style.left = (e.clientX - rect.left) + 'px';
      ripple.style.top  = (e.clientY - rect.top)  + 'px';
      btn.appendChild(ripple);
      ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
    });
  }

  function init() {
    initHeroEntrance();
    initScrollReveal();
    initCounters();
    initStickyHeader();
    initCursorParallax();
    initScrollParallax();
    initSectionReveal();
    initCardReveal();
    initSmoothScroll();
    initMagneticButtons();
    initRipple();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
