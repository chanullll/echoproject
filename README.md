# Eco Store - Sustainable E-commerce Platform

A complete PHP-based e-commerce website focused on eco-friendly products with carbon footprint tracking and gamification features.

## Features

### 🌱 Core Functionality
- **Product Catalog**: Browse eco-friendly products with detailed information
- **Shopping Cart**: Add products to cart and manage quantities
- **User Authentication**: Login/Register with role-based access (Admin/Buyer)
- **Carbon Tracking**: Track CO₂ savings from purchases
- **Leaderboard**: Gamified ranking system based on environmental impact

### 👤 User Roles
- **Buyers**: Browse products, make purchases, track environmental impact
- **Admins**: Manage users, orders, and view analytics

### 📱 Pages
- **Homepage** (`index.php`): Welcome page with featured products
- **Products** (`products.php`): Product catalog with filtering and search
- **Product Detail** (`product.php`): Detailed product information
- **Shopping Cart** (`cart.php`): Cart management and checkout
- **User Dashboard** (`user_dashboard.php`): Personal stats and order history
- **Admin Dashboard** (`admin_dashboard.php`): Administrative controls
- **Leaderboard** (`leaderboard.php`): Environmental impact rankings
- **Contact** (`contact.php`): Contact form and information
- **Authentication** (`auth.php`): Login and registration

### 🎨 Design Features
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Smooth Animations**: Custom CSS animations and transitions
- **Modern UI**: Clean, eco-friendly design aesthetic
- **Interactive Elements**: Hover effects, loading states, and micro-interactions

## Technology Stack

- **Backend**: PHP with session-based state management
- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript
- **Animations**: Custom CSS animations with JavaScript triggers
- **Icons**: Emoji-based iconography for eco-friendly feel

## Getting Started

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation
1. Clone or download the project files
2. Place files in your web server directory
3. Ensure PHP sessions are enabled
4. Access `index.php` in your browser

### Default Login Credentials
- **Admin**: 
  - Email: `admin@ecostore.com`
  - Password: `admin123`
- **Regular User**: Any email/password combination will work for demo

## File Structure

```
eco-store/
├── index.php              # Homepage
├── products.php            # Product catalog
├── product.php             # Product detail page
├── cart.php               # Shopping cart
├── auth.php               # Authentication
├── user_dashboard.php     # User dashboard
├── admin_dashboard.php    # Admin dashboard
├── leaderboard.php        # Environmental leaderboard
├── contact.php            # Contact page
├── assets/
│   ├── css/
│   │   └── animations.css # Custom animations
│   └── js/
│       └── animations.js  # Animation controls
└── README.md              # This file
```

## Key Features Explained

### Carbon Footprint Tracking
- Each product shows CO₂ savings potential
- Users accumulate CO₂ savings with purchases
- Leaderboard ranks users by environmental impact
- Achievement badges for different impact levels

### Session-Based Data
- User profiles and preferences
- Shopping cart persistence
- Order history simulation
- Admin statistics

### Responsive Design
- Mobile-first approach
- Collapsible navigation
- Touch-friendly interfaces
- Optimized for all screen sizes

### Animation System
- Page transition effects
- Scroll-triggered animations
- Interactive hover states
- Loading and success states

## Customization

### Adding Products
Edit the `$products` array in relevant PHP files to add new products with:
- ID, name, price
- CO₂ savings amount
- Category and description
- Seller information

### Styling
- Modify `assets/css/animations.css` for custom animations
- Update Tailwind configuration in HTML files
- Customize color scheme via CSS variables

### Functionality
- Add real database integration
- Implement payment processing
- Add email notifications
- Extend admin features

## Browser Support
- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+

## License
This project is open source and available under the MIT License.

## Contributing
Feel free to submit issues and enhancement requests!

---

**Eco Store** - Making sustainable shopping accessible for everyone 🌱