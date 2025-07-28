<?php
session_start();

// Initialize user session if not exists
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'name' => 'Guest',
        'role' => 'buyer',
        'co2_saved' => 0,
        'logged_in' => false
    ];
}

// Sample product data
$products = [
    [
        'id' => 1,
        'name' => 'Solar Power Bank',
        'price' => 49.99,
        'co2_saved' => 3.2,
        'category' => 'energy',
        'description' => 'High-capacity solar power bank with renewable charging capability.',
        'seller' => 'EcoTech Solutions',
        'sales' => 12
    ],
    [
        'id' => 2,
        'name' => 'Bamboo Water Bottle',
        'price' => 24.99,
        'co2_saved' => 1.8,
        'category' => 'reusables',
        'description' => 'Sustainable bamboo water bottle with leak-proof design.',
        'seller' => 'Green Living Co.',
        'sales' => 18
    ],
    [
        'id' => 3,
        'name' => 'Eco Detergent Set',
        'price' => 34.99,
        'co2_saved' => 2.5,
        'category' => 'home',
        'description' => 'Natural cleaning products made from organic ingredients.',
        'seller' => 'Pure Clean',
        'sales' => 25
    ],
    [
        'id' => 4,
        'name' => 'Reusable Food Wraps',
        'price' => 19.99,
        'co2_saved' => 0.8,
        'category' => 'reusables',
        'description' => 'Beeswax food wraps to replace plastic wrap.',
        'seller' => 'Bee Sustainable',
        'sales' => 45
    ]
];

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
    <title>Eco Store - Shop Sustainably, Save the Planet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/animations.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-green': '#059669',
                        'eco-light': '#10b981',
                        'eco-dark': '#047857'
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
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="<?php echo getLogoLink($_SESSION['user']); ?>" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                    <span class="text-2xl">🌱</span>
                    <h1 class="text-2xl font-bold text-eco-green">Eco Store</h1>
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
    <section class="bg-gradient-to-r from-eco-green to-eco-light text-white py-20" data-animate="fade-up">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-5xl font-bold mb-6 leading-tight">Shop Sustainably, Save the Planet</h2>
                <p class="text-xl mb-8 opacity-90" data-animate="fade-up" data-delay="0.2s">Every purchase counts. Join thousands making a difference with eco-friendly products that reduce carbon footprint.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="bg-white text-eco-green px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">Browse Products</a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-white text-eco-green px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">Join Us Today</a>
                    <?php endif; ?>
                    <a href="leaderboard.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-eco-green transition-all duration-300 transform hover:scale-105" data-animate="fade-up" data-delay="0.6s">See Your Impact</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-16 bg-white" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-12 text-gray-800" data-animate="fade-up">Shop by Category</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=reusables' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.1s">
                    <div class="bg-gradient-to-br from-green-100 to-green-200 p-8 rounded-xl text-center hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105">
                        <span class="text-4xl mb-4 block">♻️</span>
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">Reusables</h4>
                        <p class="text-gray-600">Bottles, bags, containers</p>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=energy' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.2s">
                    <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 p-8 rounded-xl text-center hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105">
                        <span class="text-4xl mb-4 block">⚡</span>
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">Green Energy</h4>
                        <p class="text-gray-600">Solar, wind, eco gadgets</p>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=home' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.3s">
                    <div class="bg-gradient-to-br from-blue-100 to-blue-200 p-8 rounded-xl text-center hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105">
                        <span class="text-4xl mb-4 block">🏠</span>
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">Home & Cleaning</h4>
                        <p class="text-gray-600">Natural cleaners, organics</p>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=personal' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="category-card" data-animate="fade-up" data-delay="0.4s">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 p-8 rounded-xl text-center hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105">
                        <span class="text-4xl mb-4 block">💚</span>
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">Personal Care</h4>
                        <p class="text-gray-600">Organic beauty, wellness</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Top Carbon Savers -->
    <section class="py-16 bg-gray-50" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-12 text-gray-800" data-animate="fade-up">Top Carbon Savers</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach (array_slice($products, 0, 4) as $product): ?>
                    <?php $badge = getCarbonBadge($product['co2_saved']); ?>
                    <?php $productLink = $_SESSION['user']['logged_in'] ? 'product.php?id=' . $product['id'] : 'auth.php'; ?>
                    <a href="<?php echo $productLink; ?>" class="product-card" data-animate="fade-up" data-delay="<?php echo ($loop_index ?? 0) * 0.1; ?>s">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <div class="h-48 bg-gradient-to-br from-green-200 to-green-300 relative flex items-center justify-center">
                                <span class="text-4xl"><?php echo getProductEmoji($product['category']); ?></span>
                                <div class="absolute top-3 right-3 carbon-badge <?php echo $badge['class']; ?> text-white px-2 py-1 rounded-full text-xs font-semibold animate-pulse-eco">
                                    <?php echo $badge['emoji']; ?> <?php echo $product['co2_saved']; ?>kg CO₂
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p class="text-eco-green font-bold text-lg carbon-counter" data-target="<?php echo $product['price']; ?>">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="text-sm text-gray-600">Saves <?php echo $product['co2_saved']; ?> kg CO₂ per year</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-2xl">🌱</span>
                        <h1 class="text-2xl font-bold text-eco-green">Eco Store</h1>
                    </div>
                    <p class="text-gray-400">Making sustainable shopping accessible for everyone.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <div class="space-y-2">
                        <a href="contact.php" class="block text-gray-400 hover:text-white">Contact</a>
                        <a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>
                        <?php if ($_SESSION['user']['logged_in']): ?>
                            <a href="products.php" class="block text-gray-400 hover:text-white">Products</a>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                <a href="admin_dashboard.php" class="block text-gray-400 hover:text-white">Admin</a>
                            <?php else: ?>
                                <a href="user_dashboard.php" class="block text-gray-400 hover:text-white">Dashboard</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Categories</h4>
                    <div class="space-y-2">
                        <?php if ($_SESSION['user']['logged_in']): ?>
                            <a href="products.php?category=reusables" class="block text-gray-400 hover:text-white">Reusables</a>
                            <a href="products.php?category=energy" class="block text-gray-400 hover:text-white">Green Energy</a>
                            <a href="products.php?category=home" class="block text-gray-400 hover:text-white">Home & Cleaning</a>
                        <?php else: ?>
                            <span class="block text-gray-500">Login to browse products</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>📧 hello@ecostore.com</p>
                        <p>📞 +1 (555) 123-4567</p>
                        <p>🌍 Making Earth Greener</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Eco Store. All rights reserved. 🌱 Carbon-neutral shipping available.</p>
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