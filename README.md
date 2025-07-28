# 🌱 Eco Store - Sustainable E-commerce Platform

A modern, beautiful PHP-based e-commerce website focused on eco-friendly products with carbon footprint tracking, gamification features, and PostgreSQL database integration.

## Features

### 🌱 Core Functionality
- **Product Catalog**: Browse eco-friendly products with detailed information
- **Shopping Cart**: Add products to cart and manage quantities
- **User Authentication**: Secure login/register with role-based access (Admin/Buyer/Seller)
- **Carbon Tracking**: Track CO₂ savings from purchases
- **Leaderboard**: Gamified ranking system based on environmental impact
- **PostgreSQL Database**: Robust database with proper relationships and indexing
- **Modern UI/UX**: Beautiful, responsive design with smooth animations
- **Glass Morphism**: Modern glass effects and gradients
- **Advanced Animations**: Floating elements, glowing effects, and smooth transitions

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

### 🎨 Enhanced Design Features
- **Modern Responsive Design**: Mobile-first approach with Tailwind CSS
- **Advanced Animations**: Float, glow, morphing, and gradient animations
- **Glass Morphism**: Beautiful glass effects with backdrop blur
- **Interactive Elements**: Enhanced hover effects, loading states, and micro-interactions
- **Gradient Backgrounds**: Dynamic gradient backgrounds with animated elements
- **Smooth Transitions**: Cubic-bezier transitions for premium feel
- **Accessibility**: High contrast support and reduced motion preferences

## Technology Stack

- **Backend**: PHP with PostgreSQL database and PDO
- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript
- **Database**: PostgreSQL with proper schema and relationships
- **Animations**: Advanced CSS animations with JavaScript triggers
- **Icons**: Emoji-based iconography for eco-friendly feel
- **Security**: Password hashing, prepared statements, and input validation

## Getting Started

### Prerequisites
- PHP 7.4 or higher
- PostgreSQL 12 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation
1. Clone or download the project files
2. Place files in your web server directory
3. Install PostgreSQL and create a database named `eco_store`
4. Copy `.env.example` to `.env` and update database credentials
5. Run `php setup_database.php` to set up the database
6. Ensure PHP sessions are enabled
7. Access `index.php` in your browser

### Default Login Credentials
- **Admin**: 
  - Email: `admin@ecostore.com`
  - Password: `admin123`
- **Sample User**: 
  - Email: `john@example.com`
  - Password: `password123`

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
├── setup_database.php     # Database setup script
├── config/
│   └── database.php       # Database configuration
├── models/
│   ├── User.php           # User model
│   ├── Product.php        # Product model
│   ├── Category.php       # Category model
│   ├── Cart.php           # Cart model
│   └── Order.php          # Order model
├── database/
│   └── schema.sql         # Database schema
├── assets/
│   ├── css/
│   │   └── animations.css # Enhanced animations
│   └── js/
│       └── animations.js  # Animation controls
├── .env.example           # Environment variables template
└── README.md              # This file
```

## Key Features Explained

### Database Integration
- **PostgreSQL**: Robust relational database with proper schema
- **Models**: Object-oriented approach with dedicated model classes
- **Security**: Prepared statements and password hashing
- **Relationships**: Proper foreign keys and constraints
- **Performance**: Optimized queries with indexes

### Enhanced UI/UX
- **Glass Morphism**: Modern glass effects with backdrop blur
- **Advanced Animations**: Float, glow, morphing effects
- **Responsive Design**: Mobile-first with smooth transitions
- **Interactive Elements**: Enhanced hover states and micro-interactions
- **Accessibility**: Support for reduced motion and high contrast

### Carbon Footprint Tracking
- Each product shows CO₂ savings potential
- Users accumulate CO₂ savings with purchases
- Leaderboard ranks users by environmental impact
- Achievement badges for different impact levels

### Database-Driven Features
- User profiles and authentication
- Persistent shopping cart
- Order history and tracking
- Real-time statistics and analytics

### Enhanced Responsive Design
- Mobile-first approach
- Collapsible navigation
- Touch-friendly interfaces
- Optimized for all screen sizes
- Smooth animations on all devices

### Advanced Animation System
- Page transition effects
- Scroll-triggered animations
- Interactive hover states
- Loading and success states
- Glass morphism effects
- Gradient animations
- Floating elements

## Customization

### Adding Products
Use the database to add new products:
- Insert into `products` table
- Link to categories and sellers
- Include features as JSON
- Set stock quantities

### Enhanced Styling
- Modify `assets/css/animations.css` for custom animations
- Update Tailwind configuration for new colors
- Customize CSS variables for glass effects
- Add new animation keyframes

### Functionality
- Extend database models
- Implement payment processing
- Add email notifications
- Extend admin features
- Add product reviews
- Implement wishlist functionality

## Database Setup

1. **Install PostgreSQL**
   ```bash
   # Ubuntu/Debian
   sudo apt-get install postgresql postgresql-contrib
   
   # macOS
   brew install postgresql
   
   # Windows
   Download from https://www.postgresql.org/download/windows/
   ```

2. **Create Database**
   ```sql
   CREATE DATABASE eco_store;
   CREATE USER eco_user WITH PASSWORD 'your_password';
   GRANT ALL PRIVILEGES ON DATABASE eco_store TO eco_user;
   ```

3. **Configure Environment**
   - Copy `.env.example` to `.env`
   - Update database credentials
   - Run `php setup_database.php`

## Performance Optimizations

- **Database Indexes**: Optimized queries with proper indexing
- **CSS Animations**: Hardware-accelerated animations
- **Image Optimization**: Responsive images and lazy loading
- **Caching**: Database query optimization
- **Minification**: Compressed CSS and JavaScript

## Browser Support
- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Security Features

- **Password Hashing**: Secure bcrypt hashing
- **SQL Injection Protection**: Prepared statements
- **XSS Protection**: Input sanitization
- **CSRF Protection**: Session-based security
- **Role-Based Access**: Proper authorization checks

## License
This project is open source and available under the MIT License.

## Contributing
Feel free to submit issues and enhancement requests!

## Changelog

### Version 2.0 (Latest)
- ✨ PostgreSQL database integration
- 🎨 Modern UI with glass morphism effects
- 🚀 Advanced animations and transitions
- 📱 Enhanced responsive design
- 🔒 Improved security features
- 📊 Real-time analytics and statistics
- 🛒 Persistent shopping cart
- 👥 User management system

---

**🌱 Eco Store** - Making sustainable shopping accessible for everyone with modern technology and beautiful design!