-- Eco Store Database Schema
-- PostgreSQL Database Setup

-- Create database (run this separately)
-- CREATE DATABASE eco_store;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'buyer' CHECK (role IN ('buyer', 'seller', 'admin')),
    business_name VARCHAR(255),
    co2_saved DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    emoji VARCHAR(10),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    co2_saved DECIMAL(8,2) NOT NULL,
    category_id INTEGER REFERENCES categories(id),
    seller_id INTEGER REFERENCES users(id),
    stock_quantity INTEGER DEFAULT 0,
    image_url VARCHAR(500),
    features JSONB,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    total_co2_saved DECIMAL(8,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled')),
    shipping_address JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(id),
    quantity INTEGER NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    co2_saved DECIMAL(8,2) NOT NULL
);

-- Shopping cart table
CREATE TABLE IF NOT EXISTS cart_items (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, product_id)
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    product_id INTEGER REFERENCES products(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    rating INTEGER CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, slug, emoji, description) VALUES
('Reusables', 'reusables', '♻️', 'Bottles, bags, containers'),
('Green Energy', 'energy', '⚡', 'Solar, wind, eco gadgets'),
('Home & Cleaning', 'home', '🏠', 'Natural cleaners, organics'),
('Personal Care', 'personal', '💚', 'Organic beauty, wellness')
ON CONFLICT (slug) DO NOTHING;

-- Insert sample products
INSERT INTO products (name, description, price, co2_saved, category_id, seller_id, stock_quantity, features) VALUES
('Solar Power Bank', 'Harness the power of the sun with our high-capacity solar power bank. Features dual USB ports, 20,000mAh capacity, and built-in solar panels for emergency charging.', 49.99, 3.2, 2, 1, 25, '["20,000mAh high-capacity battery", "Built-in solar panels", "Dual USB-A and USB-C ports", "Waterproof design", "Made from 60% recycled materials"]'),
('Bamboo Water Bottle', 'Sustainable bamboo water bottle with leak-proof design and natural antimicrobial properties.', 24.99, 1.8, 1, 1, 42, '["Made from sustainable bamboo", "Leak-proof design", "BPA-free materials", "500ml capacity", "Easy to clean"]'),
('Eco Detergent Set', 'Natural cleaning products made from organic ingredients with biodegradable formula.', 34.99, 2.5, 3, 1, 18, '["Made from organic ingredients", "Biodegradable formula", "Concentrated for efficiency", "Plastic-free packaging", "Safe for sensitive skin"]'),
('Reusable Food Wraps', 'Beeswax food wraps to replace plastic wrap, keeping food fresh naturally.', 19.99, 0.8, 1, 1, 67, '["Made from organic beeswax", "Reusable up to 1 year", "Various sizes included", "Naturally antimicrobial", "Compostable at end of life"]'),
('Solar LED Lights', 'Energy-efficient solar-powered LED lighting system for outdoor and indoor use.', 39.99, 2.1, 2, 1, 31, '["Solar powered", "LED technology", "Weather resistant", "Auto on/off sensor", "8-hour battery life"]'),
('Organic Shampoo Bar', 'Zero-waste shampoo bar with natural ingredients, plastic-free packaging.', 16.99, 1.2, 4, 1, 89, '["Zero waste packaging", "Natural ingredients", "Suitable for all hair types", "Long lasting", "Cruelty free"]')
ON CONFLICT DO NOTHING;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_seller ON products(seller_id);
CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_cart_user ON cart_items(user_id);
CREATE INDEX IF NOT EXISTS idx_reviews_product ON reviews(product_id);

-- Create updated_at trigger function
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for updated_at
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_products_updated_at BEFORE UPDATE ON products FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_orders_updated_at BEFORE UPDATE ON orders FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();