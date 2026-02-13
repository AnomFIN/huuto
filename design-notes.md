# Huuto - Premium Design System Documentation

## Overview

This document describes the premium Nordic luxury design system implemented for Huuto auction platform. The design system provides a modern, clean, and sophisticated user experience with smooth interactions and premium visual aesthetics.

## Design Philosophy

### Nordic Luxury Aesthetic
- **Clean & Minimal**: Generous whitespace, clear hierarchy, focused content
- **Premium Feel**: High-quality shadows, smooth animations, refined typography
- **Modern**: Contemporary color palette, rounded corners, layered depth
- **Accessible**: Strong contrast, clear focus states, keyboard navigation

### Core Principles
1. **Consistency**: Reusable components with predictable behavior
2. **Performance**: Lightweight, vanilla JavaScript, optimized CSS
3. **Accessibility**: WCAG 2.1 AA compliant, keyboard navigable, screen reader friendly
4. **Responsiveness**: Mobile-first, seamless across all devices
5. **Progressive Enhancement**: Works without JavaScript, enhanced with it

## File Structure

```
public/assets/
├── css/
│   ├── theme.css        # CSS variables & design tokens
│   ├── components.css   # Reusable UI components
│   ├── pages.css        # Page-specific layouts
│   ├── utilities.css    # Helper classes
│   └── style.css        # Legacy styles (preserved for compatibility)
└── js/
    ├── ui.js           # Premium interactions
    └── main.js         # Legacy scripts (preserved for compatibility)
```

## Design Tokens

### Color System

#### Primary (Deep Nordic Blue)
- Used for: Primary actions, links, focus states
- Scale: `--color-primary-50` to `--color-primary-900`
- Main: `--color-primary-600` (#4F46E5)

#### Secondary (Nordic Green)
- Used for: Success states, positive actions
- Scale: `--color-secondary-50` to `--color-secondary-900`
- Main: `--color-secondary-500` (#10B981)

#### Accent (Warm Amber)
- Used for: Highlights, warnings, countdown urgency
- Scale: `--color-accent-50` to `--color-accent-900`
- Main: `--color-accent-500` (#F59E0B)

#### Semantic Colors
- Success: `--color-success` (#10B981)
- Error: `--color-error` (#EF4444)
- Warning: `--color-warning` (#F59E0B)
- Info: `--color-info` (#3B82F6)

#### Theme Support
The design system supports three modes:
- **Auto**: Follows system preference
- **Light**: Force light mode
- **Dark**: Force dark mode

Toggle between modes using the theme toggle button in the header.

### Typography

#### Font Family
- **Primary**: `-apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', 'Roboto', 'Helvetica Neue', Arial, sans-serif`
- **Monospace**: `'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas`

#### Font Sizes
```
--text-xs: 0.75rem    (12px)
--text-sm: 0.875rem   (14px)
--text-base: 1rem     (16px)
--text-lg: 1.125rem   (18px)
--text-xl: 1.25rem    (20px)
--text-2xl: 1.5rem    (24px)
--text-3xl: 1.875rem  (30px)
--text-4xl: 2.25rem   (36px)
--text-5xl: 3rem      (48px)
```

#### Font Weights
- Normal: 400
- Medium: 500
- Semibold: 600
- Bold: 700
- Extrabold: 800

### Spacing Scale

Based on 4px increments:
```
--space-1: 0.25rem   (4px)
--space-2: 0.5rem    (8px)
--space-3: 0.75rem   (12px)
--space-4: 1rem      (16px)
--space-5: 1.25rem   (20px)
--space-6: 1.5rem    (24px)
--space-7: 2rem      (32px)
--space-8: 2.5rem    (40px)
--space-9: 3rem      (48px)
--space-10: 4rem     (64px)
```

### Border Radius

```
--radius-sm: 0.25rem   (4px)
--radius-base: 0.5rem  (8px)
--radius-md: 0.75rem   (12px)
--radius-lg: 1rem      (16px)
--radius-xl: 1.5rem    (24px)
--radius-2xl: 2rem     (32px)
--radius-full: 9999px
```

### Shadows

Premium layered shadows for depth:
```
--shadow-xs: Subtle hover
--shadow-sm: Card default
--shadow-base: Card hover
--shadow-md: Elevated cards
--shadow-lg: Modals, dropdowns
--shadow-xl: Hero elements
--shadow-2xl: Maximum drama
```

Colored shadows for primary actions:
```
--shadow-primary: Blue glow
--shadow-secondary: Green glow
--shadow-error: Red glow
```

### Animation

#### Durations
```
--duration-instant: 50ms
--duration-fast: 150ms
--duration-base: 200ms
--duration-moderate: 300ms
--duration-slow: 500ms
```

#### Easing
```
--ease-in-out: cubic-bezier(0.4, 0, 0.2, 1)
--ease-out: cubic-bezier(0, 0, 0.2, 1)
--ease-smooth: cubic-bezier(0.25, 0.46, 0.45, 0.94)
--ease-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55)
```

## Components

### Buttons

#### Variants
```html
<!-- Primary action -->
<button class="btn btn-primary">Huuda</button>

<!-- Secondary action -->
<button class="btn btn-secondary">Peruuta</button>

<!-- Success action -->
<button class="btn btn-success">+ Luo ilmoitus</button>

<!-- Danger action -->
<button class="btn btn-danger">Poista</button>

<!-- Ghost button -->
<button class="btn btn-ghost">Lisätietoja</button>
```

#### Sizes
```html
<button class="btn btn-sm">Pieni</button>
<button class="btn">Normaali</button>
<button class="btn btn-lg">Iso</button>
<button class="btn btn-icon">🔍</button>
```

#### States
```html
<!-- Disabled -->
<button class="btn btn-primary" disabled>Ei käytössä</button>

<!-- Loading -->
<button class="btn btn-primary btn-loading">Ladataan...</button>
```

### Cards

Premium auction listing cards with hover effects:

```html
<a href="/kohde/123" class="card">
  <img src="image.jpg" alt="Product" class="card-image" loading="lazy">
  <div class="card-body">
    <div class="card-title">Tuotteen nimi</div>
    <div class="card-text">Kategoria</div>
    <div class="card-price">150,00 €</div>
    <span class="badge badge-ending" data-countdown="2026-02-20T12:00:00">
      2 pv 5 t
    </span>
  </div>
</a>
```

Features:
- Smooth hover lift effect
- Image scale on hover
- Automatic reveal animation on scroll
- Lazy loading images

### Forms

#### Basic Form
```html
<form>
  <div class="form-group">
    <label class="form-label">Sähköposti</label>
    <input type="email" class="form-input" required>
  </div>
  
  <div class="form-group">
    <label class="form-label">Viesti</label>
    <textarea class="form-textarea"></textarea>
  </div>
  
  <button type="submit" class="btn btn-primary">Lähetä</button>
</form>
```

#### Floating Labels
```html
<div class="form-group form-group-floating">
  <input type="text" class="form-input" placeholder=" " id="name">
  <label class="form-label" for="name">Nimi</label>
</div>
```

#### Validation States
```html
<!-- Valid -->
<input type="email" class="form-input is-valid">

<!-- Invalid -->
<input type="email" class="form-input is-invalid">
<span class="form-error">Virheellinen sähköpostiosoite</span>

<!-- Help text -->
<span class="form-help">Syötä sähköpostiosoitteesi</span>
```

### Badges

```html
<span class="badge badge-primary">Uusi</span>
<span class="badge badge-success">Aktiivinen</span>
<span class="badge badge-warning">Odottaa</span>
<span class="badge badge-danger">Päättynyt</span>
<span class="badge badge-ending">5 min</span>
```

### Alerts

Auto-dismissing alerts with animation:

```html
<div class="alert alert-success">
  Toiminto onnistui!
</div>

<div class="alert alert-error">
  Virhe tapahtui.
</div>

<div class="alert alert-info">
  Tiedoksi: Huomaa tämä.
</div>
```

### Modal

JavaScript-driven modal component:

```javascript
// Create modal
const modal = window.createModal('my-modal', {
  closeOnBackdrop: true,
  closeOnEscape: true
});

// Set content
modal.setContent(`
  <div class="modal-header">
    <h3 class="modal-title">Otsikko</h3>
    <button class="modal-close" aria-label="Sulje">×</button>
  </div>
  <div class="modal-body">
    <p>Sisältö tähän</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary">Peruuta</button>
    <button class="btn btn-primary">Vahvista</button>
  </div>
`);

// Open/close
modal.open();
modal.close();
```

### Toast Notifications

Global toast manager for notifications:

```javascript
// Success toast
window.toast.success('Huuto tehty onnistuneesti!');

// Error toast
window.toast.error('Virhe huutaessa. Yritä uudelleen.');

// Warning toast
window.toast.warning('Huutokauppa päättyy pian!');

// Info toast
window.toast.info('Uusi viesti saapui.');
```

### Dropdown

```html
<div class="dropdown">
  <button class="btn btn-secondary">Valikko ▼</button>
  <div class="dropdown-menu">
    <button class="dropdown-item">Vaihtoehto 1</button>
    <button class="dropdown-item">Vaihtoehto 2</button>
    <div class="dropdown-divider"></div>
    <button class="dropdown-item">Vaihtoehto 3</button>
  </div>
</div>
```

Auto-initializes on page load. Closes on outside click and ESC key.

## Interactive Features

### 1. Sticky Header with Shrink Effect
Header becomes compact on scroll for better space utilization.

### 2. Mobile Menu
Responsive drawer menu with:
- Slide-in animation
- Backdrop overlay
- Focus trap
- ESC key close
- Auto-close on navigation

### 3. Theme Toggle
Switch between auto/light/dark modes. Preference saved to localStorage.

### 4. Scroll to Top Button
Appears after scrolling 600px down. Smooth scroll to top.

### 5. Reveal Animations
Cards and content fade in as you scroll using IntersectionObserver.

### 6. Image Gallery
Thumbnail gallery with:
- Main image display
- Clickable thumbnails
- Active state indicator
- Smooth transitions

### 7. Enhanced Countdown Timers
Real-time countdown with:
- Smart time formatting
- Urgent state (< 5 minutes)
- Pulse animation when urgent

### 8. Lazy Loading Images
Native lazy loading with IntersectionObserver fallback.

### 9. Form Enhancements
- Auto-submit loading state
- Auto-dismiss alerts
- Inline validation

## Utility Classes

### Display
```
.d-none, .d-block, .d-flex, .d-grid
```

### Flexbox
```
.justify-center, .justify-between
.align-center, .align-start
.flex-1, .flex-column
```

### Spacing
```
.m-4, .mt-6, .mb-7, .mx-auto
.p-4, .pt-6, .pb-7
.gap-4
```

### Typography
```
.text-sm, .text-lg, .text-2xl
.font-bold, .font-semibold
.text-center, .text-left
.truncate, .line-clamp-2
```

### Colors
```
.text-primary, .text-secondary
.bg-surface, .bg-tertiary
```

### Borders & Shadows
```
.rounded-lg, .rounded-full
.shadow-md, .shadow-lg
.border, .border-t
```

### Responsive
```
.hide-mobile, .show-mobile
.hide-tablet, .show-tablet
```

## Responsive Breakpoints

```css
/* Mobile: < 768px */
/* Tablet: 768px - 1024px */
/* Desktop: > 1024px */
```

Key responsive adjustments:
- Stacked layouts on mobile
- Collapsible navigation
- Optimized touch targets
- Fluid typography with clamp()

## Accessibility

### Keyboard Navigation
- All interactive elements are keyboard accessible
- Visible focus states
- Logical tab order
- Skip links available

### Screen Readers
- Semantic HTML5
- ARIA labels where needed
- Descriptive alt text
- Hidden state management

### Motion
Respects `prefers-reduced-motion` - disables animations for users who prefer reduced motion.

### Color Contrast
All color combinations meet WCAG 2.1 AA standards:
- Text contrast: minimum 4.5:1
- Large text: minimum 3:1
- Interactive elements: clear visual distinction

## Best Practices

### Adding New Pages
1. Use existing template structure
2. Include premium CSS/JS in layout
3. Apply utility classes for spacing
4. Use semantic components
5. Test responsive behavior

### Custom Components
1. Start with base component styles
2. Add modifiers with BEM-like naming
3. Use CSS variables for theming
4. Ensure dark mode compatibility
5. Add smooth transitions

### Performance Tips
1. Use lazy loading for images
2. Minimize reflows with CSS transforms
3. Use requestAnimationFrame for animations
4. Debounce/throttle scroll handlers
5. Load critical CSS first

## Browser Support

- **Chrome/Edge**: Last 2 versions
- **Firefox**: Last 2 versions
- **Safari**: Last 2 versions
- **Mobile Safari**: iOS 12+
- **Chrome Mobile**: Last 2 versions

### Graceful Degradation
- Native lazy loading with fallback
- CSS Grid with Flexbox fallback
- Modern features with feature detection

## Maintenance

### Updating Colors
Edit CSS variables in `theme.css`:
```css
:root {
  --color-primary-600: #4F46E5;
}
```

### Adding Components
1. Add styles to `components.css`
2. Follow existing naming conventions
3. Include hover/focus/active states
4. Test in light and dark modes

### Modifying Interactions
Edit `ui.js` and follow the class-based pattern:
```javascript
class NewFeature {
  constructor() {
    this.init();
  }
  
  init() {
    // Setup code
  }
}
```

## Testing Checklist

- [ ] Visual regression on all pages
- [ ] Mobile responsiveness
- [ ] Dark mode appearance
- [ ] Keyboard navigation
- [ ] Screen reader compatibility
- [ ] Animation performance
- [ ] Form validation
- [ ] Error states
- [ ] Loading states
- [ ] Cross-browser compatibility

## Support

For questions or issues:
1. Check this documentation
2. Review component examples
3. Inspect existing implementations
4. Test in browser DevTools

---

**Design System Version**: 1.0.0  
**Last Updated**: February 2026  
**Author**: AnomFIN
