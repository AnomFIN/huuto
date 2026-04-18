---
name: elite-website-enhancement
description: 'Build premium website sections with Apple/Tesla/Stripe-level design quality. Use for: hero sections, premium UI components, interactive features, modern animations, high-conversion landing areas, polished user experiences.'
argument-hint: 'Describe the section or feature you want to enhance (e.g., "auction listing page", "user dashboard", "homepage hero")'
---

# Elite Website Enhancement

Transform ordinary website sections into premium, investment-grade experiences that compete with Apple, Tesla, and Stripe. Target: "Modern premium marketplace 2025" - alive but restrained, focused on quality feel rather than decoration.

## When to Use
- Redesigning key website sections (hero, product pages, dashboards)
- Building premium marketplace/auction interfaces
- Creating dynamic carousels and product showcases
- Enhancing visual appeal and user experience
- Creating high-conversion landing areas
- Implementing modern UI patterns and animations
- Building components that impress investors or stakeholders

## Core Principles

### 1. Hierarchy Before Effects
**CRITICAL**: Solve layout hierarchy, spacing, typography, and contrast FIRST. Never try to fix weak layout with glows, blurs, or bright colors.
- Every view must have clear visual focus
- Information hierarchy = visual hierarchy
- Typography establishes trust before any effects
- White/negative space creates premium feel

### 2. Premium Dark UI Guidelines  
**Theme**: Dark backgrounds without heaviness
- Create depth through: shadows, transparency, soft borders, subtle glows
- Avoid: harsh neon effects, excessive blur, multiple accent colors
- One primary accent (e.g., blue-violet gradient) is sufficient
- Use glassmorphism sparingly and purposefully

### 3. Product Image Treatment
**Product visibility is paramount**
- Never blur entire product images for text readability
- Use gradient overlays at bottom for text areas instead
- Active cards: image closer, sharper, brighter than inactive
- Maintain image quality while creating hierarchy

### 4. Typography & Number Formatting
**Human-friendly data presentation**
- Clear font hierarchy (avoid robotic appearance)
- Time: "1 h 37 min left" not "01:37:23"
- Currency: "1 500 €" not "1500 €" (proper spacing)
- Emphasize most important numbers elegantly, not loudly
- Price displays should feel premium, not generic

### 5. Carousel Movement & Flow
**Continuous experience, not box swapping**
- Active slide: scale 1, opacity 1, clearest shadow
- Inactive slides: smaller, dimmer, partially visible at edges
- Smooth transitions: transform, opacity, filter properties
- Movement should be fluid, not mechanical or bouncy
- Use cubic-bezier easing for premium feel

### 6. Micro-Interactions & Responsiveness
**Subtle but noticeable feedback**
- Hover/active states: slight brightness, lift, or scale changes
- Images: subtle zoom/parallax on active slides
- Progress indicators: integrated part of whole, not separate dots
- Buttons/CTAs: gentle glow or shadow changes on interaction
- All interactions should feel immediate and smooth

### 7. Code-Based UI Elements
**Build UI with code, not images**
- Arrows, borders, glows, gradients, overlays, progress, highlights: CSS/SVG
- Product images: real photographs only  
- Decorative UI as images looks dated and cheap quickly
- Maintains crisp appearance at all screen sizes
- Easier to maintain and customize

### 8. Landing Page Composition
**Hero sections need unified design language**
- Left and right sides must feel like same product
- Right-side components can't look like separate widgets
- Strong brand message on left = equally polished component on right
- Maintain visual balance and design system consistency

### 9. Code Quality Standards
**Clean, maintainable premium code**
- Separate layout, data, and animations concerns
- Modern transitions and responsive structure  
- Avoid unnecessary DOM nesting and class spaghetti
- Use CSS custom properties for consistency
- Component-based architecture for reusability

## Implementation Workflow

### 1. Visual Design Foundation
- [ ] Define dark theme color palette (primary, accent, neutrals)
- [ ] Choose typography system (premium font hierarchy)
- [ ] Establish spacing grid (8px, 16px, 24px, 32px, 48px)
- [ ] Plan glassmorphism elements (subtle blur, transparency)
- [ ] Design for single accent color system

### 2. Information Architecture
- [ ] Define visual hierarchy (what gets attention first)
- [ ] Mobile-first responsive breakpoints
- [ ] Component hierarchy planning
- [ ] Number/currency formatting standards
- [ ] Accessibility considerations

### 3. Interactive Behavior Design
- [ ] Carousel flow and transition behavior
- [ ] Hover states for all interactive elements
- [ ] Loading animations for async actions
- [ ] Error/success state designs
- [ ] Micro-interaction timing and easing

### 4. Performance Optimization
- [ ] Lazy loading for images and heavy content
- [ ] CSS animations using `transform` and `opacity` only
- [ ] Minimal JavaScript for smooth interactions
- [ ] Optimized asset delivery
- [ ] Avoid heavy blur effects and complex 3D transforms

### 5. Technical Implementation

**Premium Dark Theme CSS**
```css
/* Premium marketplace dark theme system */
:root {
  --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  --dark-bg: #0f0f23;
  --dark-surface: #1a1a2e;
  --glass-bg: rgba(255, 255, 255, 0.05);
  --glass-border: rgba(255, 255, 255, 0.1);
  --shadow-premium: 0 8px 32px rgba(0, 0, 0, 0.3);
  --shadow-lift: 0 12px 40px rgba(0, 0, 0, 0.4);
  --text-primary: #ffffff;
  --text-secondary: rgba(255, 255, 255, 0.7);
  --accent-primary: #6366f1;
}

/* Carousel premium transitions */
.carousel-item {
  transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  transform-origin: center;
}

.carousel-item.active {
  transform: scale(1);
  opacity: 1;
  z-index: 10;
  box-shadow: var(--shadow-premium);
}

.carousel-item.inactive {
  transform: scale(0.85);
  opacity: 0.6;
  filter: brightness(0.7);
}

/* Premium number formatting */
.price-display {
  font-variant-numeric: tabular-nums;
  letter-spacing: 0.025em;
}

/* Gradient overlay for image text */
.product-image-overlay {
  background: linear-gradient(
    0deg, 
    rgba(0, 0, 0, 0.8) 0%, 
    rgba(0, 0, 0, 0.3) 40%, 
    transparent 70%
  );
}
```

**Animation Principles for Marketplaces**
- Carousel: `transform: translateX()` + `scale()` + `opacity` combinations
- `transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)` for premium feel
- Intersection Observer API for scroll-triggered animations
- RequestAnimationFrame for complex product showcase animations
- Never blur product images completely - use selective overlays

**PHP Backend Quality for Marketplaces**
- Prepared statements for all auction/product queries
- Input sanitization for bid amounts and product data  
- Proper error handling with user-friendly marketplace messages
- Clean MVC separation for auction logic
- Real-time bid updates with smooth UI transitions

### 6. Premium Marketplace Features
Choose 2-3 elements that enhance the auction/marketplace experience:
- [ ] Real-time bid counter with smooth number animations
- [ ] Dynamic auction countdown with human-readable time
- [ ] Product carousel with depth and smooth transitions  
- [ ] Advanced filtering with animated result updates
- [ ] Live bidder activity indicators
- [ ] Interactive product galleries with hero zoom
- [ ] Smooth category browsing with premium navigation
- [ ] Trust indicators and seller verification displays

## Quality Checklist

**Visual Excellence**
- [ ] Dark theme executed without heaviness or confusion
- [ ] Consistent spacing and premium typography
- [ ] Single accent color used effectively throughout
- [ ] Product images remain sharp and prominent
- [ ] Gradient overlays used instead of image blurring
- [ ] All UI elements built with CSS/SVG (no decorative images)

**Marketplace-Specific Polish**
- [ ] Currency formatted properly (e.g., "1 500 €")
- [ ] Time displays are human-friendly ("2h 15min left")
- [ ] Carousel feels continuous, not mechanical
- [ ] Hero left/right sides feel unified
- [ ] Active states provide subtle but clear feedback
- [ ] Trust elements (ratings, verification) are prominent

**Technical Excellence** 
- [ ] Fast loading on auction pages (< 2 seconds)
- [ ] Smooth on mobile, tablet, desktop
- [ ] No console errors in browser
- [ ] Secure bid handling and form processing
- [ ] SEO-optimized for marketplace content
- [ ] Real-time updates don't break UI flow

**User Experience**
- [ ] Clear auction/product action hierarchy
- [ ] Intuitive bidding and purchasing flows
- [ ] Helpful loading states during bid processing
- [ ] Accessible to screen readers
- [ ] Feels trustworthy and commercially credible
- [ ] "Modern premium marketplace 2025" aesthetic achieved

## Resources

- [Animation Patterns](./references/animation-patterns.md) - CSS and JS animation templates
- [Component Library](./references/component-library.md) - Premium UI component examples
- [Performance Guide](./references/performance-optimization.md) - Speed optimization techniques

## Next Steps After Enhancement

1. **Marketplace Testing** - Test on real auction/product pages with actual data
2. **Performance Monitoring** - Track load times on product-heavy pages  
3. **Conversion Analysis** - Measure impact on bid rates and sales metrics
4. **Mobile Optimization** - Ensure premium feel translates to mobile bidding experience
5. **User Feedback** - Validate that premium aesthetic increases trust and engagement
6. **Iterate** - Refine based on real marketplace behavior data

## Key Success Indicators

- Users perceive the platform as "premium" and "trustworthy"
- Auction engagement rates increase (more bids, higher final prices)
- Mobile bidding experience feels as premium as desktop
- Loading times remain fast despite enhanced visual elements
- Platform feels "alive but restrained" - dynamic without being distracting
- Visual hierarchy guides users to most important information (price, time, bid button) effortlessly

**Remember**: Premium marketplace design isn't about flashy effects—it's about creating trust, clarity, and an effortless premium experience that makes users confident to bid higher and buy more.