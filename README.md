# TCM 2025 Child Theme

A modular child theme for WordPress Twenty Twenty-Five with vendor-specific styling and WooCommerce customizations for TCM Limited.

## Features

### Vendor-Specific Branding
- **CSS Classes**: Automatically applied based on user roles and B2BKing customer groups
- **Supported Vendors**:
  - Standard Pricing (default)
  - Canadian Tire
  - Costco
  - Loblaws
- **Customizations**: Logo, background colors, button colors per vendor

### WooCommerce Enhancements
- **Account Menu**: Customized menu items (removes unnecessary tabs, renames purchase lists)
- **Breadcrumb Prioritization**: Ensures product type categories appear first in breadcrumbs
- **Category Ordering**: Admin field to set custom display order for categories
- **Cart Models**: Custom category display with grid layout and pagination

### Login Behavior
- **Smart Redirects**: Customers → Home page, Administrators → wp-admin

### Modular Architecture
- Clean separation of concerns with `/inc/core/` and `/inc/modules/`
- Each feature in its own module for easy maintenance and updates

## Installation

### Via WordPress Admin
1. Build the theme: `npm run build`
2. Navigate to `builds/` folder
3. Upload `tcm-2025-child-vX.X.X.zip` to WordPress
4. Activate the theme

### Manual Installation
1. Upload the `tcm-2025-child` folder to `/wp-content/themes/`
2. Activate the theme through WordPress admin

## Development

### Setup
```bash
cd themes/tcm-2025-child
npm install
```

### Build Commands
```bash
npm run build          # Patch: 1.0.0 → 1.0.1
npm run build:minor    # Minor: 1.0.0 → 1.1.0
npm run build:major    # Major: 1.0.0 → 2.0.0
```

### Directory Structure
```
tcm-2025-child/
├── style.css                   # Theme header & vendor-specific CSS
├── functions.php               # Main functions file
├── inc/
│   ├── core/
│   │   ├── theme-setup.php    # Basic theme setup
│   │   └── loader.php         # Module loader
│   └── modules/
│       ├── login-redirect/    # Post-login redirect
│       ├── woocommerce/       # WooCommerce customizations
│       ├── breadcrumbs/       # Breadcrumb prioritization
│       └── cart-models/       # Cart models custom display
├── build.js                    # Build script
├── package.json                # NPM configuration
└── README.md                   # This file
```

## Modules

### Login Redirect
Redirects customers to home page after login while keeping administrators on wp-admin.

### WooCommerce
- Customizes account menu items
- Adds category ordering field to admin
- Removes unwanted menu tabs

### Breadcrumbs
Prioritizes product type categories (wheels, handles, locks, etc.) in breadcrumb navigation.

### Cart Models
- **Category Override**: Custom grid display for cart-models category
- **Shortcode**: `[display_cart_models columns="3" limit="12"]`
- **Features**: Responsive grid, pagination, customizable columns

## Vendor CSS Classes

The theme works with the TCM Vendor UI plugin to apply these classes:

- `.tcm-administrator` - Administrator role
- `.tcm-vendor-standard-pricing` - Standard pricing group
- `.tcm-vendor-canadian-tire` - Canadian Tire group
- `.tcm-vendor-costco` - Costco group
- `.tcm-vendor-loblaws` - Loblaws group
- `.tcm-logged-in` / `.tcm-guest` - Login status

## Requirements
- WordPress 5.0+
- Twenty Twenty-Five parent theme
- WooCommerce (for e-commerce features)
- B2BKing (optional, for vendor group support)
- TCM Vendor UI plugin (for body class injection)

## Customization

### Adding New Vendors
1. Add CSS rules in `style.css` following the existing vendor pattern
2. Logo, background, and button colors can be customized per vendor

### Adding New Modules
1. Create new folder in `inc/modules/your-module/`
2. Add `init.php` with your functionality
3. Add module name to `$modules` array in `inc/core/loader.php`

### Priority Categories for Breadcrumbs
Edit the `$priority_cats` array in `inc/modules/breadcrumbs/init.php` to add or remove categories.

## License
GPL v2 or later

## Authors
Marcus & Claude
