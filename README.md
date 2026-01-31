# Website Cost Calculator - WordPress Plugin

A comprehensive, modern website cost calculator tool for WordPress that helps clients estimate the cost of their website project based on various customizable factors.

## Features

- **Multi-Step Calculator**: Guides users through a 5-step process to build their quote
- **Fully Customizable Pricing**: Configure all prices, features, and options from the admin panel
- **Modern UI/UX**: Beautiful, responsive design that works on all devices
- **Real-time Calculations**: Prices update instantly as users make selections
- **Quote Request System**: Collect leads with built-in contact form
- **Email Notifications**: Automatic emails to admin and customer
- **Quote Management**: View, manage, and track all quote requests from WordPress admin
- **Customizable Styling**: Change colors to match your brand
- **Rush Pricing**: Timeline multipliers for urgent projects
- **Maintenance Plans**: Optional monthly maintenance pricing

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

### Shortcode Attributes

```
[website_cost_calculator theme="default" compact="false"]
```

- `theme`: Currently supports "default"
- `compact`: Set to "true" for a more compact layout

## Calculator Steps

1. **Website Type**: Basic Blog, Business Website, E-commerce, Custom Web App, Enterprise
2. **Pages & Design**: Number of pages and design approach (template vs custom)
3. **Features**: Select from various website features (contact forms, galleries, etc.)
4. **Extras**: SEO, marketing, timeline options, and maintenance plans
5. **Summary**: Review selections and submit quote request

## Admin Features

### Settings Page

- **General**: Currency symbol and position
- **Pricing Options**: Configure website types, page ranges, design options, timelines, and maintenance plans
- **Features**: Customize available features and e-commerce add-ons
- **Styling**: Change primary, secondary, accent, text, and background colors
- **Contact Form**: Configure lead capture form settings

### Quote Requests

- View all submitted quote requests
- Filter by status (Pending, Contacted, Converted, Closed)
- View detailed selections for each quote
- Update status to track leads
- Delete unwanted quotes

## Default Pricing Structure

### Website Types (Base Prices)
- Basic Blog/Portfolio: $500
- Business Website: $1,500
- E-commerce Store: $3,000
- Custom Web Application: $5,000
- Enterprise Solution: $10,000

### Page Ranges (Additional)
- 1-5 pages: Included
- 6-10 pages: +$300
- 11-20 pages: +$600
- 21-50 pages: +$1,200
- 50+ pages: +$2,000

### Design Options
- Template Based: Included
- Semi-Custom Design: +$800
- Fully Custom Design: +$2,000

### Timeline Multipliers
- Standard (4-6 weeks): 1x
- Rush (2-3 weeks): 1.25x
- Urgent (1-2 weeks): 1.5x

All prices are fully customizable from the admin settings.

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## File Structure

```
website-cost-calculator/
├── website-cost-calculator.php  # Main plugin file
├── admin/
│   ├── admin-page.php          # Admin settings page
│   └── quotes-page.php         # Quote management page
├── assets/
│   ├── css/
│   │   ├── admin-style.css     # Admin styles
│   │   └── frontend-style.css  # Frontend calculator styles
│   └── js/
│       ├── admin-script.js     # Admin functionality
│       └── frontend-script.js  # Calculator logic
├── templates/
│   └── calculator.php          # Frontend calculator template
└── includes/                   # Reserved for future includes
```

## Customization

### CSS Variables

The calculator uses CSS custom properties that can be overridden:

```css
:root {
    --wcc-primary: #4F46E5;
    --wcc-secondary: #10B981;
    --wcc-accent: #F59E0B;
    --wcc-text: #1F2937;
    --wcc-background: #F9FAFB;
}
```

Or use the admin Styling tab to change colors without code.

## Database

The plugin creates a custom table `{prefix}_wcc_quotes` to store quote submissions with the following structure:

- id, name, email, phone, company, selections, total_cost, notes, created_at, status

## License

GPL v2 or later

## Changelog

### 1.0.0
- Initial release
- Multi-step calculator interface
- Admin settings panel
- Quote management system
- Email notifications
- Responsive design
