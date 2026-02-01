# Website Cost Calculator Pro - WordPress Plugin

The most comprehensive and beautiful website cost calculator tool for WordPress. Built by analyzing the top 10 website cost calculators on the web and combining all their best features into one premium solution.

## Premium Features

### Industry-Leading Design
- **6-Step Guided Process**: Industry → Type & Size → Design → Features → Extras → Summary
- **Beautiful Modern UI**: Gradient headers, smooth animations, glassmorphism effects
- **Fully Responsive**: Perfect on desktop, tablet, and mobile devices
- **Dark/Light Mode Ready**: CSS variables for easy theming
- **Accessibility Compliant**: Keyboard navigation, screen reader support

### Industry Presets (Unique Feature)
Pre-configured recommendations for 10 industries:
- Restaurant & Food
- E-commerce & Retail
- Professional Services
- Healthcare & Medical
- Real Estate
- Startup & Tech
- Non-Profit & Charity
- Education & Training
- Portfolio & Creative
- Custom / Other

Each preset auto-suggests website type, features, and applies industry-specific pricing modifiers.

### Advanced Pricing Engine
- **Price Range Display**: Shows Budget / Your Quote / Premium tiers
- **Timeline Multipliers**: Flexible (-5%) to Emergency (+100%)
- **Industry Modifiers**: Automatic pricing adjustments per industry
- **Real-time Calculations**: Instant updates as users make selections
- **Animated Price Widget**: Floating total that updates with animations

### Cost Breakdown Visualization
- **Interactive Doughnut Chart**: Visual breakdown of costs by category
- **Detailed Line Items**: Grouped by category with individual pricing
- **Subtotal & Adjustments**: Clear display of timeline adjustments
- **Monthly Recurring**: Separate display of hosting & maintenance costs

### PDF Quote Generation
- **Client-side PDF**: No server load, instant download
- **Professional Layout**: Branded, detailed quote document
- **Complete Summary**: All selections, pricing, and contact info

### Save & Load Quotes
- **Unique Quote Codes**: 8-character shareable codes
- **30-Day Expiration**: Quotes saved for a month
- **View Tracking**: See how many times quotes are viewed
- **Easy Restoration**: Load any saved quote instantly

### Share Functionality
- **Direct Link Sharing**: Unique URLs for each quote
- **Email Integration**: One-click email sharing
- **Social Sharing**: Twitter & LinkedIn integration
- **Print-Optimized**: Clean print styles

### Lead Capture & CRM
- **Built-in Contact Form**: Name, email, phone, company, notes
- **Email Notifications**: Admin + customer confirmation emails
- **Quote Management**: View, filter, update status, delete
- **Status Tracking**: Pending → Contacted → Converted → Closed

### Analytics Dashboard
- **Total Quotes**: Track all submissions
- **Pipeline Value**: Total estimated revenue
- **Average Quote Value**: Benchmark your quotes
- **Conversion Rate**: Measure effectiveness
- **Quotes Over Time**: 30-day trend chart
- **Status Distribution**: Visual breakdown
- **Top Industries**: See which industries request most quotes
- **Popular Features**: Track most-requested features

### Extensive Customization
- **Currency Settings**: Any symbol, before/after position
- **Color Theming**: Primary, secondary, accent, background colors
- **Gradient Customization**: Header gradient colors
- **All Pricing Editable**: Every price, option, and multiplier
- **Feature Management**: Add/remove/edit all features
- **Industry Presets**: Fully configurable

## Installation

1. Upload the `website-cost-calculator` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Cost Calculator' in the admin menu to configure settings
4. Add the calculator to any page using the shortcode

## Usage

### Basic Shortcode
```
[website_cost_calculator]
```

### With Attributes
```
[website_cost_calculator theme="default" compact="false" industry="ecommerce"]
```

**Attributes:**
- `theme`: Visual theme (default: "default")
- `compact`: Compact layout (default: "false")
- `industry`: Pre-select an industry (restaurant, ecommerce, professional, healthcare, realestate, startup, nonprofit, education, portfolio, custom)

## Calculator Steps

### Step 1: Industry Selection
Choose from 10 pre-configured industry presets that automatically suggest relevant features and apply appropriate pricing.

### Step 2: Website Type & Size
- **Landing Page** - $300
- **Basic Website** - $800
- **Business Website** - $2,000
- **E-commerce Store** - $4,000
- **Web Application** - $8,000
- **Enterprise Platform** - $15,000

Plus page count selection (1-3 up to 30+).

### Step 3: Design Approach
- Template Based (included)
- Premium Template (+$500)
- Semi-Custom (+$1,500)
- Fully Custom (+$3,500)
- Premium Custom (+$6,000)

Plus content & media options.

### Step 4: Features
15+ standard features including:
- Contact Form, Blog, Gallery, Video Integration
- Social Media, Newsletter, Search, Multi-language
- Member Area, Live Chat, Appointment Booking
- Reviews, FAQ, Maps, Animations

12+ e-commerce features (shown for e-commerce sites):
- Product Catalogs (25 to 500+)
- Payment Gateway, Inventory, Shipping
- Coupons, Reviews, Wishlist, Comparisons
- Abandoned Cart Recovery

### Step 5: Extras
- 8 SEO & Marketing add-ons
- 5 Timeline options with multipliers
- 6 Hosting options (monthly)
- 5 Maintenance plans (monthly)

### Step 6: Summary
- Price comparison (Budget/Your Quote/Premium)
- Interactive cost breakdown chart
- Detailed line-item summary
- Contact form for lead capture
- PDF download, share, and print options

## Admin Features

### Settings Tabs
1. **General**: Currency, positioning
2. **Pricing**: Website types, pages, design, timelines, maintenance
3. **Features**: Standard features, e-commerce features, SEO/marketing
4. **Industries**: Configure industry presets
5. **Styling**: Colors, gradients, branding
6. **Form**: Contact form settings, email configuration

### Quote Management
- View all submitted quotes
- Filter by status (Pending, Contacted, Converted, Closed)
- View detailed selections and breakdown
- Update status to track leads
- Delete unwanted quotes

### Analytics
- Total quotes and pipeline value
- Average quote value
- Conversion rate tracking
- 30-day trend charts
- Industry breakdown
- Popular features analysis

## Technical Details

### File Structure
```
website-cost-calculator/
├── website-cost-calculator.php    # Main plugin file
├── readme.txt                     # WordPress readme
├── admin/
│   ├── admin-page.php            # Settings page
│   ├── quotes-page.php           # Quote management
│   └── analytics-page.php        # Analytics dashboard
├── assets/
│   ├── css/
│   │   ├── admin-style.css       # Admin styles
│   │   └── frontend-style.css    # Calculator styles
│   └── js/
│       ├── admin-script.js       # Admin functionality
│       └── frontend-script.js    # Calculator logic
├── templates/
│   ├── calculator.php            # Basic template
│   └── calculator-pro.php        # Pro template
└── includes/                     # Reserved for extensions
```

### Database Tables
- `{prefix}_wcc_quotes`: Stores quote submissions
- `{prefix}_wcc_saved_quotes`: Stores saved quote codes

### External Libraries (CDN)
- Feather Icons - Beautiful icons
- Chart.js - Cost breakdown charts
- jsPDF - PDF generation
- html2canvas - PDF capture

### CSS Variables
```css
:root {
    --wcc-primary: #6366F1;
    --wcc-secondary: #10B981;
    --wcc-accent: #F59E0B;
    --wcc-text: #1F2937;
    --wcc-background: #F8FAFC;
    --wcc-gradient-start: #6366F1;
    --wcc-gradient-end: #8B5CF6;
}
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern browser with JavaScript enabled

## Comparison with Competitors

| Feature | Our Plugin | WebFX | GoodFirms | Others |
|---------|-----------|-------|-----------|--------|
| Industry Presets | ✅ | ❌ | ❌ | ❌ |
| PDF Generation | ✅ | ❌ | ❌ | ❌ |
| Save/Load Quotes | ✅ | ❌ | ❌ | ❌ |
| Cost Breakdown Chart | ✅ | ❌ | ✅ | ❌ |
| Price Range Display | ✅ | ✅ | ❌ | ❌ |
| Lead Capture | ✅ | ❌ | ✅ | ✅ |
| Analytics Dashboard | ✅ | ❌ | ❌ | ❌ |
| Social Sharing | ✅ | ❌ | ❌ | ❌ |
| Timeline Pricing | ✅ | ❌ | ❌ | ❌ |
| Hosting Options | ✅ | ❌ | ❌ | ❌ |
| E-commerce Features | ✅ | ❌ | ✅ | ❌ |
| Fully Customizable | ✅ | ❌ | ❌ | ❌ |

## License

**Proprietary Software** - Copyright (C) 2026 Haseeb Ur Rehman Mughal / EzyOnTech

All rights reserved. This software is proprietary and confidential.
Unauthorized copying, modification, distribution, or use is strictly prohibited.

For licensing inquiries: [ezyontech.com](https://ezyontech.com)

## Changelog

### 2.0.0
- Complete redesign with modern UI
- Added industry presets (10 industries)
- Added PDF quote generation
- Added save/load quote functionality
- Added cost breakdown chart (Chart.js)
- Added price range comparison (Budget/Standard/Premium)
- Added analytics dashboard
- Added share functionality (Email, Twitter, LinkedIn)
- Added hosting options with monthly pricing
- Added content/copywriting options
- Added 15+ new features
- Enhanced responsive design
- Added print-optimized styles
- Added accessibility improvements
- Added smooth animations and transitions
- Added floating price widget with animations

### 1.0.0
- Initial release
- Basic multi-step calculator
- Admin settings panel
- Quote management system
- Email notifications
