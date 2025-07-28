<?php
session_start();
require_once 'config/database.php';

// Initialize user session if not exists
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'name' => 'Guest',
        'role' => 'buyer',
        'co2_saved' => 0,
        'logged_in' => false
    ];
}

// Fetch products from database
try {
    $products = $db->fetchAll("
        SELECT p.*, c.slug as category, u.name as seller 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN users u ON p.seller_id = u.id 
        WHERE p.is_active = true 
        ORDER BY p.created_at DESC 
        LIMIT 4
    ");
} catch (Exception $e) {
    $products = [];
    error_log("Error fetching products: " . $e->getMessage());
}

// Function to get carbon badge
function getCarbonBadge($co2Amount) {
    if ($co2Amount < 1) return ['emoji' => '🟢', 'text' => 'Low Impact', 'class' => 'bg-green-500'];
    if ($co2Amount < 2) return ['emoji' => '🟡', 'text' => 'Medium Impact', 'class' => 'bg-yellow-500'];
    return ['emoji' => '🔴', 'text' => 'High Impact', 'class' => 'bg-red-500'];
}

// Function to get product emoji
function getProductEmoji($category) {
    $emojis = [
        'reusables' => '♻️',
        'energy' => '⚡',
        'home' => '🏠',
        'personal' => '💚'
    ];
    return $emojis[$category] ?? '🌱';
}

// Function to get logo link based on user role
function getLogoLink($user) {
    if (!$user['logged_in']) {
        return 'index.php';
    }
    
    switch ($user['role']) {
        case 'admin':
            return 'admin_dashboard.php';
        case 'buyer':
        default:
            return 'products.php';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌱 Eco Store - Shop Sustainably, Save the Planet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/animations.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-green': '#16a34a',
                        'eco-light': '#22c55e',
                        'eco-dark': '#15803d',
                        'eco-accent': '#84cc16',
                        'eco-secondary': '#06b6d4'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate'
                    }
                }
            }
        }
    </script>
    <style>
        .nav-link { @apply text-gray-600 hover:text-eco-green transition-colors font-medium; }
        .nav-link.active { @apply text-eco-green font-semibold; }
        .product-card { @apply transition-all duration-300 ease-in-out; }
        .category-card { @apply transition-all duration-300 ease-in-out; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(34, 197, 94, 0.3); }
            to { box-shadow: 0 0 30px rgba(34, 197, 94, 0.6); }
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="<?php echo getLogoLink($_SESSION['user']); ?>" class="flex items-center space-x-3 hover:scale-105 transition-all duration-300 group">
                    <span class="text-3xl animate-float group-hover:animate-glow">🌱</span>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-eco-green to-eco-accent bg-clip-text text-transparent">Eco Store</h1>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="nav-link active">Home</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                    <a href="leaderboard.php" class="nav-link">Leaderboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="nav-link">Products</a>
                        <a href="cart.php" class="nav-link">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="admin_dashboard.php" class="nav-link">Admin</a>
                        <?php else: ?>
                            <a href="user_dashboard.php" class="nav-link">Dashboard</a>
                        <?php endif; ?>
                        <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">Logout</a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors">Login</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Toggle mobile menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Navigation -->
            <div class="mobile-menu hidden md:hidden mt-4 pb-4" id="mobileMenu">
                <div class="flex flex-col space-y-2">
                    <a href="index.php" class="nav-link active py-2 px-4 rounded-lg">Home</a>
                    <a href="contact.php" class="nav-link py-2 px-4 rounded-lg">Contact</a>
                    <a href="leaderboard.php" class="nav-link py-2 px-4 rounded-lg">Leaderboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="nav-link py-2 px-4 rounded-lg">Products</a>
                        <a href="cart.php" class="nav-link py-2 px-4 rounded-lg">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="admin_dashboard.php" class="nav-link py-2 px-4 rounded-lg">Admin</a>
                        <?php else: ?>
                            <a href="user_dashboard.php" class="nav-link py-2 px-4 rounded-lg">Dashboard</a>
                        <?php endif; ?>
                        <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors text-center">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-eco-green via-eco-light to-eco-secondary text-white py-24 overflow-hidden" data-animate="fade-up">
        <!-- Animated background elements -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full animate-float"></div>
            <div class="absolute top-32 right-20 w-16 h-16 bg-eco-accent rounded-full animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-white rounded-full animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-32 right-1/3 w-24 h-24 bg-eco-accent rounded-full animate-float" style="animation-delay: 0.5s;"></div>
        </div>
        
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-6xl font-bold mb-8 leading-tight animate-fade-up">
                    Shop Sustainably, 
                    <span class="text-eco-accent animate-glow">Save the Planet</span>
                </h2>
                <p class="text-2xl mb-10 opacity-95 leading-relaxed" data-animate="fade-up" data-delay="0.2s">
                    Every purchase counts. Join <span class="font-bold text-eco-accent">10,000+</span> eco-warriors making a difference with products that reduce carbon footprint.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="bg-white text-eco-green px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-110 hover:shadow-2xl animate-glow" data-animate="fade-up" data-delay="0.4s">
                            🛍️ Browse Products
                        </a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-white text-eco-green px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-110 hover:shadow-2xl animate-glow" data-animate="fade-up" data-delay="0.4s">
                            🚀 Join Us Today
                        </a>
                    <?php endif; ?>
                    <a href="leaderboard.php" class="glass-effect text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-eco-green transition-all duration-300 transform hover:scale-110 hover:shadow-2xl" data-animate="fade-up" data-delay="0.6s">
                        📊 See Your Impact
                    </a>
                </div>
                
                <!-- Stats preview -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8" data-animate="fade-up" data-delay="0.8s">
                    <div class="glass-effect rounded-2xl p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="text-4xl font-bold text-eco-accent">15,847</div>
                        <div class="text-lg opacity-90">kg CO₂ Saved</div>
                    </div>
                    <div class="glass-effect rounded-2xl p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="text-4xl font-bold text-eco-accent">2,847</div>
                        <div class="text-lg opacity-90">Happy Customers</div>
                    </div>
                    <div class="glass-effect rounded-2xl p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="text-4xl font-bold text-eco-accent">156</div>
                        <div class="text-lg opacity-90">Eco Products</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-16 bg-white" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <h3 class="text-4xl font-bold text-center mb-16 bg-gradient-to-r from-gray-800 to-eco-green bg-clip-text text-transparent" data-animate="fade-up">Shop by Category</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=reusables' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.1s">
                    <div class="bg-gradient-to-br from-green-100 to-green-200 p-10 rounded-2xl text-center hover:shadow-2xl transition-all duration-500 cursor-pointer transform hover:scale-110 hover:rotate-2 group">
                        <span class="text-6xl mb-6 block group-hover:animate-bounce">♻️</span>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3">Reusables</h4>
                        <p class="text-gray-700 text-lg">Bottles, bags, containers</p>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=energy' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.2s">
                    <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 p-10 rounded-2xl text-center hover:shadow-2xl transition-all duration-500 cursor-pointer transform hover:scale-110 hover:rotate-2 group">
                        <span class="text-6xl mb-6 block group-hover:animate-bounce">⚡</span>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3">Green Energy</h4>
                        <p class="text-gray-700 text-lg">Solar, wind, eco gadgets</p>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=home' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.3s">
                    <div class="bg-gradient-to-br from-blue-100 to-blue-200 p-10 rounded-2xl text-center hover:shadow-2xl transition-all duration-500 cursor-pointer transform hover:scale-110 hover:rotate-2 group">
                        <span class="text-6xl mb-6 block group-hover:animate-bounce">🏠</span>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3">Home & Cleaning</h4>
                        <p class="text-gray-700 text-lg">Natural cleaners, organics</p>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=personal' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.4s">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 p-10 rounded-2xl text-center hover:shadow-2xl transition-all duration-500 cursor-pointer transform hover:scale-110 hover:rotate-2 group">
                        <span class="text-6xl mb-6 block group-hover:animate-bounce">💚</span>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3">Personal Care</h4>
                        <p class="text-gray-700 text-lg">Organic beauty, wellness</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Top Carbon Savers -->
    <section class="py-16 bg-gray-50" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <h3 class="text-4xl font-bold text-center mb-16 bg-gradient-to-r from-gray-800 to-eco-green bg-clip-text text-transparent" data-animate="fade-up">🌟 Top Carbon Savers</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($products as $index => $product): ?>
                    <?php $badge = getCarbonBadge($product['co2_saved']); ?>
                    <?php $productLink = $_SESSION['user']['logged_in'] ? 'product.php?id=' . $product['id'] : 'auth.php'; ?>
                    <a href="<?php echo $productLink; ?>" class="product-card group" data-animate="fade-up" data-delay="<?php echo $index * 0.1; ?>s">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:scale-110 hover:-rotate-1 group-hover:animate-glow">
                            <div class="h-56 bg-gradient-to-br from-green-200 via-green-300 to-eco-light relative flex items-center justify-center overflow-hidden">
                                <!-- Animated background pattern -->
                                <div class="absolute inset-0 opacity-20">
                                    <div class="absolute top-2 left-2 w-4 h-4 bg-white rounded-full animate-ping"></div>
                                    <div class="absolute bottom-4 right-4 w-3 h-3 bg-eco-accent rounded-full animate-pulse"></div>
                                </div>
                                <span class="text-6xl group-hover:scale-125 transition-transform duration-500"><?php echo getProductEmoji($product['category']); ?></span>
                                <div class="absolute top-4 right-4 carbon-badge <?php echo $badge['class']; ?> text-white px-3 py-2 rounded-full text-sm font-bold animate-bounce shadow-lg">
                                    <?php echo $badge['emoji']; ?> <?php echo $product['co2_saved']; ?>kg CO₂
                                </div>
                            </div>
                            <div class="p-6">
                                <h4 class="font-bold text-gray-800 mb-3 text-lg group-hover:text-eco-green transition-colors"><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p class="text-eco-green font-bold text-2xl carbon-counter mb-2" data-target="<?php echo $product['price']; ?>">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="text-base text-gray-700 font-medium">🌱 Saves <?php echo $product['co2_saved']; ?> kg CO₂ per year</p>
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-sm text-gray-500">by <?php echo htmlspecialchars($product['seller']); ?></span>
                                    <div class="flex items-center space-x-1">
                                        <span class="text-yellow-400">⭐</span>
                                        <span class="text-sm font-medium">4.8</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Call to action -->
            <div class="text-center mt-16" data-animate="fade-up" data-delay="0.6s">
                <?php if ($_SESSION['user']['logged_in']): ?>
                    <a href="products.php" class="inline-flex items-center space-x-3 bg-gradient-to-r from-eco-green to-eco-accent text-white px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 animate-glow">
                        <span>🛍️</span>
                        <span>View All Products</span>
                        <span>→</span>
                    </a>
                <?php else: ?>
                    <a href="auth.php" class="inline-flex items-center space-x-3 bg-gradient-to-r from-eco-green to-eco-accent text-white px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 animate-glow">
                        <span>🚀</span>
                        <span>Start Shopping</span>
                        <span>→</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Environmental Impact Section -->
    <section class="py-20 bg-gradient-to-br from-eco-green to-eco-secondary text-white relative overflow-hidden" data-animate="fade-up">
        <!-- Animated background -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-32 h-32 bg-white rounded-full animate-float"></div>
            <div class="absolute bottom-20 right-20 w-24 h-24 bg-eco-accent rounded-full animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white rounded-full animate-float" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="container mx-auto px-4 text-center relative z-10">
            <h3 class="text-5xl font-bold mb-8" data-animate="fade-up">🌍 Our Global Impact</h3>
            <p class="text-2xl mb-16 opacity-90" data-animate="fade-up" data-delay="0.2s">Together, we're making a real difference for our planet</p>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8" data-animate="fade-up" data-delay="0.4s">
                <div class="glass-effect rounded-2xl p-8 transform hover:scale-110 transition-all duration-300">
                    <div class="text-5xl mb-4">🌱</div>
                    <div class="text-4xl font-bold text-eco-accent mb-2">15,847</div>
                    <div class="text-lg">kg CO₂ Saved</div>
                </div>
                <div class="glass-effect rounded-2xl p-8 transform hover:scale-110 transition-all duration-300">
                    <div class="text-5xl mb-4">🌳</div>
                    <div class="text-4xl font-bold text-eco-accent mb-2">792</div>
                    <div class="text-lg">Trees Equivalent</div>
                </div>
                <div class="glass-effect rounded-2xl p-8 transform hover:scale-110 transition-all duration-300">
                    <div class="text-5xl mb-4">🚗</div>
                    <div class="text-4xl font-bold text-eco-accent mb-2">39,618</div>
                    <div class="text-lg">Miles Not Driven</div>
                </div>
                <div class="glass-effect rounded-2xl p-8 transform hover:scale-110 transition-all duration-300">
                    <div class="text-5xl mb-4">⚡</div>
                    <div class="text-4xl font-bold text-eco-accent mb-2">28,525</div>
                    <div class="text-lg">kWh Saved</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16 relative overflow-hidden">
        <!-- Animated background -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-10 left-10 w-20 h-20 bg-eco-green rounded-full animate-float"></div>
            <div class="absolute bottom-10 right-10 w-16 h-16 bg-eco-accent rounded-full animate-float" style="animation-delay: 1s;"></div>
        </div>
        
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <span class="text-4xl animate-float">🌱</span>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-eco-green to-eco-accent bg-clip-text text-transparent">Eco Store</h1>
                    </div>
                    <p class="text-gray-300 text-lg leading-relaxed">Making sustainable shopping accessible for everyone. Join our mission to create a greener future, one purchase at a time.</p>
                    <div class="mt-6 flex space-x-4">
                        <a href="#" class="w-12 h-12 bg-eco-green rounded-full flex items-center justify-center hover:bg-eco-light transition-colors transform hover:scale-110">
                            <span class="text-xl">📘</span>
                        </a>
                        <a href="#" class="w-12 h-12 bg-eco-green rounded-full flex items-center justify-center hover:bg-eco-light transition-colors transform hover:scale-110">
                            <span class="text-xl">🐦</span>
                        </a>
                        <a href="#" class="w-12 h-12 bg-eco-green rounded-full flex items-center justify-center hover:bg-eco-light transition-colors transform hover:scale-110">
                            <span class="text-xl">📷</span>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-xl text-eco-accent">Quick Links</h4>
                    <div class="space-y-2">
                        <a href="contact.php" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">📞 Contact</a>
                        <a href="leaderboard.php" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">🏆 Leaderboard</a>
                        <?php if ($_SESSION['user']['logged_in']): ?>
                            <a href="products.php" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">🛍️ Products</a>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                <a href="admin_dashboard.php" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">👑 Admin</a>
                            <?php else: ?>
                                <a href="user_dashboard.php" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">📊 Dashboard</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-xl text-eco-accent">Categories</h4>
                    <div class="space-y-2">
                        <?php if ($_SESSION['user']['logged_in']): ?>
                            <a href="products.php?category=reusables" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">♻️ Reusables</a>
                            <a href="products.php?category=energy" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">⚡ Green Energy</a>
                            <a href="products.php?category=home" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">🏠 Home & Cleaning</a>
                            <a href="products.php?category=personal" class="block text-gray-300 hover:text-eco-accent transition-colors hover:translate-x-2 transform duration-300">💚 Personal Care</a>
                        <?php else: ?>
                            <span class="block text-gray-500 italic">🔒 Login to browse products</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-xl text-eco-accent">Contact</h4>
                    <div class="space-y-4 text-gray-300">
                        <p class="flex items-center space-x-3 hover:text-eco-accent transition-colors">
                            <span>📧</span>
                            <span>hello@ecostore.com</span>
                        </p>
                        <p class="flex items-center space-x-3 hover:text-eco-accent transition-colors">
                            <span>📞</span>
                            <span>+1 (555) 123-4567</span>
                        </p>
                        <p class="flex items-center space-x-3 hover:text-eco-accent transition-colors">
                            <span>🌍</span>
                            <span>Making Earth Greener</span>
                        </p>
                        <div class="mt-6 p-4 bg-eco-green bg-opacity-20 rounded-xl">
                            <p class="text-sm text-eco-accent font-semibold">🌱 Carbon-neutral shipping</p>
                            <p class="text-sm text-gray-300">Free on orders over $50</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center">
                <p class="text-gray-300 text-lg">
                    &copy; 2025 <span class="text-eco-accent font-bold">Eco Store</span>. All rights reserved. 
                    <span class="inline-block animate-float">🌱</span> 
                    Making the world a better place, one purchase at a time.
                </p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.classList.add('animate-slide-down');
            } else {
                menu.classList.add('animate-slide-up');
                setTimeout(() => {
                    menu.classList.add('hidden');
                    menu.classList.remove('animate-slide-up', 'animate-slide-down');
                }, 300);
            }
        }
    </script>
    <script src="assets/js/animations.js"></script>
</body>
</html>