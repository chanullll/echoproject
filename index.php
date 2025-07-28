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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="bg-gradient-to-br from-gray-50 via-green-50 to-emerald-50 min-h-screen">
    <!-- Particles Background -->
    <div class="particles-bg"></div>
    
    <!-- Header -->
    <header class="nav-modern sticky top-0 z-50 transition-all duration-300">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="<?php echo getLogoLink($_SESSION['user']); ?>" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                    <span class="text-3xl floating">🌱</span>
                    <h1 class="text-2xl font-bold gradient-text">Eco Store</h1>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="nav-link active magnetic">Home</a>
                    <a href="contact.php" class="nav-link magnetic">Contact</a>
                    <a href="leaderboard.php" class="nav-link magnetic">Leaderboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="nav-link magnetic">Products</a>
                        <a href="cart.php" class="nav-link magnetic">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="admin_dashboard.php" class="nav-link magnetic">Admin</a>
                        <?php else: ?>
                            <a href="user_dashboard.php" class="nav-link magnetic">Dashboard</a>
                        <?php endif; ?>
                        <a href="auth.php?action=logout" class="btn-modern bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-2 rounded-full glow-on-hover">Logout</a>
                    <?php else: ?>
                        <a href="auth.php" class="btn-modern px-6 py-2 rounded-full glow-on-hover">Login</a>
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
    <section class="relative bg-gradient-to-br from-emerald-600 via-green-600 to-teal-600 text-white py-32 overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-32 h-32 bg-white bg-opacity-10 rounded-full morph-shape"></div>
            <div class="absolute bottom-20 right-10 w-48 h-48 bg-white bg-opacity-5 rounded-full morph-shape" style="animation-delay: -2s;"></div>
            <div class="absolute top-1/2 left-1/3 w-24 h-24 bg-white bg-opacity-10 rounded-full floating"></div>
        </div>
        
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-5xl mx-auto hero-content relative z-10">
                <h2 class="text-6xl md:text-7xl font-bold mb-8 leading-tight">
                    <span class="block">Shop Sustainably,</span>
                    <span class="block gradient-text bg-gradient-to-r from-white to-green-200 bg-clip-text text-transparent">Save the Planet</span>
                </h2>
                <p class="text-xl md:text-2xl mb-12 opacity-90 max-w-3xl mx-auto leading-relaxed">Every purchase counts. Join thousands making a difference with eco-friendly products that reduce carbon footprint.</p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="btn-modern bg-white text-emerald-600 px-10 py-4 rounded-full font-bold text-lg shadow-2xl hover:shadow-emerald-500/25 transform hover:scale-105 transition-all duration-300">
                            <span class="mr-2">🛍️</span>Browse Products
                        </a>
                    <?php else: ?>
                        <a href="auth.php" class="btn-modern bg-white text-emerald-600 px-10 py-4 rounded-full font-bold text-lg shadow-2xl hover:shadow-emerald-500/25 transform hover:scale-105 transition-all duration-300">
                            <span class="mr-2">🌟</span>Join Us Today
                        </a>
                    <?php endif; ?>
                    <a href="leaderboard.php" class="glass-card border-2 border-white text-white px-10 py-4 rounded-full font-bold text-lg backdrop-blur-sm hover:bg-white hover:text-emerald-600 transition-all duration-300 transform hover:scale-105">
                        <span class="mr-2">📊</span>See Your Impact
                    </a>
                </div>
                
                <!-- Floating Stats -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="glass-card p-6 rounded-2xl text-center floating" style="animation-delay: 0.5s;">
                        <div class="text-3xl font-bold counter" data-target="15847">0</div>
                        <div class="text-sm opacity-80">kg CO₂ Saved</div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl text-center floating" style="animation-delay: 1s;">
                        <div class="text-3xl font-bold counter" data-target="2847">0</div>
                        <div class="text-sm opacity-80">Happy Customers</div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl text-center floating" style="animation-delay: 1.5s;">
                        <div class="text-3xl font-bold counter" data-target="156">0</div>
                        <div class="text-sm opacity-80">Eco Products</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-24 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #10b981 1px, transparent 0); background-size: 20px 20px;"></div>
        </div>
        
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 reveal">
                <h3 class="text-5xl font-bold gradient-text mb-4">Shop by Category</h3>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Discover our carefully curated collection of sustainable products</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 stagger-container">
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=reusables' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="stagger-item">
                    <div class="glass-card bg-gradient-to-br from-emerald-50 to-green-100 p-8 rounded-2xl text-center cursor-pointer group card-stack">
                        <div class="text-5xl mb-6 block floating">♻️</div>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-emerald-600 transition-colors">Reusables</h4>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors">Bottles, bags, containers</p>
                        <div class="mt-4 inline-flex items-center text-emerald-600 font-semibold group-hover:translate-x-2 transition-transform">
                            Explore <span class="ml-2">→</span>
                        </div>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=energy' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="stagger-item">
                    <div class="glass-card bg-gradient-to-br from-yellow-50 to-amber-100 p-8 rounded-2xl text-center cursor-pointer group card-stack">
                        <div class="text-5xl mb-6 block floating" style="animation-delay: 0.5s;">⚡</div>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-amber-600 transition-colors">Green Energy</h4>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors">Solar, wind, eco gadgets</p>
                        <div class="mt-4 inline-flex items-center text-amber-600 font-semibold group-hover:translate-x-2 transition-transform">
                            Explore <span class="ml-2">→</span>
                        </div>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=home' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="stagger-item">
                    <div class="glass-card bg-gradient-to-br from-blue-50 to-cyan-100 p-8 rounded-2xl text-center cursor-pointer group card-stack">
                        <div class="text-5xl mb-6 block floating" style="animation-delay: 1s;">🏠</div>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-cyan-600 transition-colors">Home & Cleaning</h4>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors">Natural cleaners, organics</p>
                        <div class="mt-4 inline-flex items-center text-cyan-600 font-semibold group-hover:translate-x-2 transition-transform">
                            Explore <span class="ml-2">→</span>
                        </div>
                    </div>
                </a>
                <?php $categoryLink = $_SESSION['user']['logged_in'] ? 'products.php?category=personal' : 'auth.php'; ?>
                <a href="<?php echo $categoryLink; ?>" class="stagger-item">
                    <div class="glass-card bg-gradient-to-br from-purple-50 to-pink-100 p-8 rounded-2xl text-center cursor-pointer group card-stack">
                        <div class="text-5xl mb-6 block floating" style="animation-delay: 1.5s;">💚</div>
                        <h4 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-purple-600 transition-colors">Personal Care</h4>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors">Organic beauty, wellness</p>
                        <div class="mt-4 inline-flex items-center text-purple-600 font-semibold group-hover:translate-x-2 transition-transform">
                            Explore <span class="ml-2">→</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Top Carbon Savers -->
    <section class="py-24 bg-gradient-to-br from-gray-50 to-emerald-50 relative">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 reveal">
                <h3 class="text-5xl font-bold gradient-text mb-4">Top Carbon Savers</h3>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Products making the biggest environmental impact</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 stagger-container">
                <?php foreach (array_slice($products, 0, 4) as $product): ?>
                    <?php $badge = getCarbonBadge($product['co2_saved']); ?>
                    <?php $productLink = $_SESSION['user']['logged_in'] ? 'product.php?id=' . $product['id'] : 'auth.php'; ?>
                    <a href="<?php echo $productLink; ?>" class="stagger-item">
                        <div class="product-card glass-card bg-white rounded-2xl overflow-hidden group">
                            <div class="h-56 bg-gradient-to-br from-emerald-200 via-green-300 to-teal-200 relative flex items-center justify-center overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-transparent to-black/5"></div>
                                <span class="text-6xl floating relative z-10"><?php echo getProductEmoji($product['category']); ?></span>
                                <div class="absolute top-4 right-4 carbon-badge bg-gradient-to-r from-emerald-500 to-green-600 text-white px-3 py-2 rounded-full text-sm font-bold pulse-glow">
                                    <?php echo $badge['emoji']; ?> <?php echo $product['co2_saved']; ?>kg CO₂
                                </div>
                                <!-- Hover overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            <div class="p-6">
                                <h4 class="font-bold text-xl text-gray-800 mb-3 group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p class="text-emerald-600 font-bold text-2xl mb-2">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="text-gray-600 mb-4">Saves <?php echo $product['co2_saved']; ?> kg CO₂ per year</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">By <?php echo htmlspecialchars($product['seller']); ?></span>
                                    <div class="flex items-center text-yellow-500">
                                        <span class="text-sm">⭐⭐⭐⭐⭐</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Call to Action -->
            <div class="text-center mt-16 reveal">
                <?php if ($_SESSION['user']['logged_in']): ?>
                    <a href="products.php" class="btn-modern px-12 py-4 text-lg rounded-full glow-on-hover">
                        <span class="mr-2">🌟</span>View All Products
                    </a>
                <?php else: ?>
                    <a href="auth.php" class="btn-modern px-12 py-4 text-lg rounded-full glow-on-hover">
                        <span class="mr-2">🚀</span>Start Your Eco Journey
                    </a>
                <?php endif; ?>
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