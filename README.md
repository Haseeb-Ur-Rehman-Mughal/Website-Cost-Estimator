# Nexus - The Ultimate Website Template

A stunning, modern website that combines the best design elements from the world's top 10 websites.

## Design Inspiration

This website incorporates the best features from:

| Website | Features Borrowed |
|---------|------------------|
| **Google** | Clean, minimal design, excellent UX, fast loading |
| **Apple** | Premium feel, smooth animations, bold typography, whitespace |
| **Netflix** | Hero sections, dark theme, engaging carousels |
| **YouTube** | Dark/light mode toggle, engaging layouts |
| **Amazon** | Trust signals, clear CTAs, social proof |
| **Twitter/X** | Real-time feel, minimal UI, modern aesthetic |
| **Instagram** | Visual-first, smooth animations, grid layouts |
| **LinkedIn** | Professional cards, clean structure |
| **Spotify** | Vibrant gradients, modern color schemes |
| **Airbnb** | Beautiful imagery, friendly UI, accessibility |

## Features

### Design & UI
- Dark/Light theme toggle with smooth transitions
- Gradient orbs with parallax animations
- Glassmorphism effects
- Modern typography using Inter font
- Responsive grid-based layouts
- Animated hero section

### Interactive Elements
- Smooth scroll navigation
- Tab-based showcase section
- Pricing toggle (monthly/yearly)
- Animated counters
- Button ripple effects
- Mobile-friendly hamburger menu

### Performance
- Intersection Observer for lazy animations
- Throttled scroll handlers
- CSS animations (GPU accelerated)
- Minimal JavaScript footprint

### Accessibility
- Keyboard navigation support
- ARIA labels
- Focus indicators
- Reduced motion support
- High contrast text

## File Structure

```
/
├── index.html      # Main HTML structure
├── styles.css      # All CSS styling (light/dark themes)
├── script.js       # Interactive JavaScript
└── README.md       # This file
```

## Sections

1. **Navigation** - Sticky navbar with theme toggle
2. **Hero** - Full-height with animated background, stats
3. **Logos** - Trusted by section with company logos
4. **Features** - 6 feature cards in a modern grid
5. **Showcase** - Tabbed content (Design/Develop/Deploy)
6. **Testimonials** - Customer reviews in cards
7. **Pricing** - 3-tier pricing with toggle
8. **CTA** - Call-to-action section
9. **Footer** - Links and social media

## Technologies

- **HTML5** - Semantic markup
- **CSS3** - Custom properties, Grid, Flexbox, Animations
- **JavaScript** - ES6+, Intersection Observer API
- **Font Awesome** - Icons
- **Google Fonts** - Inter typeface

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Getting Started

Simply open `index.html` in your browser. No build process required!

```bash
# Or use a local server
python -m http.server 8000
# Then visit http://localhost:8000
```

## Customization

### Colors
Edit the CSS variables in `styles.css`:

```css
:root {
    --accent-primary: #6366f1;
    --accent-secondary: #8b5cf6;
    --accent-gradient: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);
}
```

### Theme
The default theme is dark. Change it in `index.html`:

```html
<html lang="en" data-theme="light">
```

## License

MIT License - Feel free to use this template for your projects!

---

Built with the best practices from the world's top websites.
