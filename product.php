<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    header('Location: auth.php');
    exit;
}

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
        'description' => 'Harness the power of the sun with our high-capacity solar power bank. Features dual USB ports, 20,000mAh capacity, and built-in solar panels for emergency charging. Perfect for outdoor adventures and reducing your carbon footprint. Made with recycled materials and packaged in biodegradable packaging.',
        'seller' => 'EcoTech Solutions',
        'sales' => 12,
        'features' => [
            '20,000mAh high-capacity battery',
            'Built-in solar panels for renewable charging',
            'Dual USB-A and USB-C ports',
            'Waterproof and dustproof design',
            'Made from 60% recycled materials'
        ]
    ],
    [
        'id' => 2,
        'name' => 'Bamboo Water Bottle',
        'price' => 24.99,
        'co2_saved' => 1.8,
        'category' => 'reusables',
        'description' => 'Sustainable bamboo water bottle with leak-proof design.',
        'seller' => 'Green Living Co.',
        'sales' => 18,
        'features' => [
            'Made from sustainable bamboo',
            'Leak-proof design',
            'BPA-free materials',
            '500ml capacity',
            'Easy to clean'
        ]
    ],
    [
        'id' => 3,
        'name' => 'Eco Detergent Set',
        'price' => 34.99,
        'co2_saved' => 2.5,
        'category' => 'home',
        'description' => 'Natural cleaning products made from organic ingredients.',
        'seller' => 'Pure Clean',
        'sales' => 25,
        'features' => [
            'Made from organic ingredients',
            'Biodegradable formula',
            'Concentrated for efficiency',
            'Plastic-free packaging',
            'Safe for sensitive skin'
        ]
    ]
];

// Handle purchase
$purchaseSuccess = false;
$carbonSaved = 0;

if ($_POST['action'] === 'purchase' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    $product = array_filter($products, function($p) use ($productId) {
        return $p['id'] === $productId;
    });
    
    if ($product) {
        $product = array_values($product)[0];
        $carbonSaved = $product['co2_saved'] * $quantity;
        $_SESSION['user']['co2_saved'] += $carbonSaved;
        $purchaseSuccess = true;
        
        // Initialize orders if not exists
        if (!isset($_SESSION['orders'])) {
            $_SESSION['orders'] = [];
        }
        
        // Add order
        $_SESSION['orders'][] = [
            'id' => 'ECO-' . str_pad(count($_SESSION['orders']) + 1, 3, '0', STR_PAD_LEFT),
            'product_name' => $product['name'],
            'quantity' => $quantity,
            'price' => $product['price'] * $quantity,
            'co2_saved' => $carbonSaved,
            'date' => date('Y-m-d'),
            'status' => 'Processing'
        ];
    }
}

// Get product details
$productId = $_GET['id'] ?? 1;
$product = array_filter($products, function($p) use ($productId) {
    return $p['id'] == $productId;
});

if (empty($product)) {
    header('Location: products.php');
    exit;
}

$product = array_values($product)[0];

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

$badge = getCarbonBadge($product['co2_saved']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Eco Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/animations.css">
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
        .thumbnail { @apply transition-all duration-300 ease-in-out; }
        .thumbnail.active { @apply border-eco-green transform scale-105; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">🌱</span>
                    <h1 class="text-2xl font-bold text-eco-green">Eco Store</h1>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="products.php" class="nav-link">Products</a>
                    <a href="leaderboard.php" class="nav-link">Leaderboard</a>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
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
                    <a href="index.php" class="nav-link py-2 px-4 rounded-lg">Home</a>
                    <a href="products.php" class="nav-link py-2 px-4 rounded-lg">Products</a>
                    <a href="leaderboard.php" class="nav-link py-2 px-4 rounded-lg">Leaderboard</a>
                    <a href="dashboard.php" class="nav-link py-2 px-4 rounded-lg">Dashboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors text-center">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-3">
            <nav class="text-sm text-gray-600">
                <a href="index.php" class="hover:text-eco-green">Home</a>
                <span class="mx-2">›</span>
                <a href="products.php" class="hover:text-eco-green">Products</a>
                <span class="mx-2">›</span>
                <span class="text-gray-800"><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>
        </div>
    </div>

    <!-- Product Detail -->
    <section class="py-12" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Image Gallery -->
                <div class="space-y-4" data-animate="fade-up" data-delay="0.2s">
                    <div class="bg-gradient-to-br from-green-200 to-green-400 rounded-xl h-96 flex items-center justify-center main-image transition-all duration-500">
                        <div class="text-center">
                            <span class="text-6xl block mb-4"><?php echo getProductEmoji($product['category']); ?></span>
                            <p class="text-gray-700 font-medium"><?php echo htmlspecialchars($product['name']); ?></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-lg h-20 flex items-center justify-center cursor-pointer border-2 border-eco-green thumbnail active hover:scale-110 transition-all duration-300">
                            <span class="text-2xl"><?php echo getProductEmoji($product['category']); ?></span>
                        <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-lg h-20 flex items-center justify-center cursor-pointer border-2 border-gray-200 hover:border-eco-green thumbnail hover:scale-110 transition-all duration-300">
                        <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-lg h-20 flex items-center justify-center cursor-pointer border-2 border-gray-200 hover:border-eco-green thumbnail hover:scale-110 transition-all duration-300">
                        <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-lg h-20 flex items-center justify-center cursor-pointer border-2 border-gray-200 hover:border-eco-green thumbnail hover:scale-110 transition-all duration-300">
                            <span class="text-2xl">🔋</span>
                        </div>
                        <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-lg h-20 flex items-center justify-center cursor-pointer border-2 border-gray-200 hover:border-eco-green thumbnail">
                            <span class="text-2xl">☀️</span>
                        </div>
                        <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-lg h-20 flex items-center justify-center cursor-pointer border-2 border-gray-200 hover:border-eco-green thumbnail">
                            <span class="text-2xl">📱</span>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-6" data-animate="fade-up" data-delay="0.4s">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p class="text-2xl font-bold text-eco-green mb-4">$<?php echo number_format($product['price'], 2); ?></p>
                        
                        <!-- Carbon Impact Badge -->
                        <div class="flex items-center space-x-4 mb-6" data-animate="fade-up" data-delay="0.6s">
                            <div class="<?php echo str_replace('bg-', 'bg-', $badge['class']); ?>-100 text-<?php echo str_replace('bg-', '', $badge['class']); ?>-800 px-4 py-2 rounded-full flex items-center space-x-2">
                                <span class="text-lg"><?php echo $badge['emoji']; ?></span>
                                <span class="font-semibold animate-wiggle">Saves <?php echo $product['co2_saved']; ?> kg CO₂ annually</span>
                            </div>
                            <div class="text-sm text-gray-600"><?php echo $badge['text']; ?></div>
                        </div>

                        <!-- Impact Statement -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 transition-all duration-300 hover:shadow-md" data-animate="fade-up" data-delay="0.8s">
                            <h3 class="font-semibold text-green-800 mb-2">🌱 Environmental Impact</h3>
                            <p class="text-green-700">This product saves <?php echo $product['co2_saved']; ?> kg CO₂ annually, equivalent to planting <?php echo ceil($product['co2_saved'] / 3.2); ?> tree(s) or removing a car from the road for <?php echo round($product['co2_saved'] * 2.5); ?> miles!</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-3">Description</h3>
                        <p class="text-gray-600 leading-relaxed">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                    </div>

                    <?php if (isset($product['features'])): ?>
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Key Features</h3>
                        <ul class="space-y-2 text-gray-600">
                            <?php foreach ($product['features'] as $feature): ?>
                            <li class="flex items-center space-x-2">
                                <span class="text-eco-green">✓</span>
                                <span><?php echo htmlspecialchars($feature); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Purchase Form -->
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="purchase">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <select name="quantity" id="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="submit" class="flex-1 bg-eco-green text-white px-6 py-3 rounded-lg font-semibold hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                Add to Cart - $<?php echo number_format($product['price'], 2); ?>
                            </button>
                            <button type="button" class="px-6 py-3 border border-eco-green text-eco-green rounded-lg font-semibold hover:bg-eco-green hover:text-white transition-colors">
                            <button type="button" class="px-6 py-3 border border-eco-green text-eco-green rounded-lg font-semibold hover:bg-eco-green hover:text-white transition-all duration-300 transform hover:scale-105">
                                ❤️
                            </button>
                        </div>
                    </form>

                    <!-- Seller Info -->
                    <div class="bg-gray-100 rounded-lg p-4">
                        <h3 class="font-semibold mb-2">Sold by <?php echo htmlspecialchars($product['seller']); ?></h3>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span>⭐ 4.8 (234 reviews)</span>
                            <span>📦 Fast shipping</span>
                            <span>♻️ Carbon neutral</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Carbon Impact Modal -->
    <?php if ($purchaseSuccess): ?>
    <div id="carbonModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fade-in">
        <div class="bg-white rounded-xl p-8 max-w-md w-full text-center animate-scale-in">
            <div class="mb-6">
                <span class="text-6xl block mb-4 animate-bounce-eco">🎉</span>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Purchase Successful!</h3>
                <div class="bg-green-100 border border-green-300 rounded-lg p-4">
                    <p class="text-green-800 font-semibold">You saved <?php echo $carbonSaved; ?> kg CO₂ with this purchase!</p>
                    <p class="text-green-600 text-sm mt-1">Equivalent to planting <?php echo ceil($carbonSaved / 3.2); ?> tree(s) 🌳</p>
                </div>
            </div>
            <button onclick="closeModal()" class="bg-eco-green text-white px-6 py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                Continue Shopping
            </button>
        </div>
    </div>
    <?php endif; ?>

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
                        <a href="products.php" class="block text-gray-400 hover:text-white">Products</a>
                        <a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>
                        <a href="dashboard.php" class="block text-gray-400 hover:text-white">Dashboard</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Categories</h4>
                    <div class="space-y-2">
                        <a href="products.php?category=reusables" class="block text-gray-400 hover:text-white">Reusables</a>
                        <a href="products.php?category=energy" class="block text-gray-400 hover:text-white">Green Energy</a>
                        <a href="products.php?category=home" class="block text-gray-400 hover:text-white">Home & Cleaning</a>
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

        function closeModal() {
            const modal = document.getElementById('carbonModal');
            if (modal) {
                modal.classList.add('animate-fade-out');
                setTimeout(() => modal.remove(), 300);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('carbonModal');
            if (modal && e.target === modal) {
                closeModal();
            }
        });
        
        // Show carbon savings notification
        <?php if ($purchaseSuccess): ?>
        setTimeout(() => {
            if (window.EcoAnimations) {
                window.EcoAnimations.showCarbonSavingsModal(<?php echo $carbonSaved; ?>);
            }
                window.EcoAnimations.showCarbonSavingsModal(<?php echo $carbonSaved; ?>);
            }
        }, 1000);
        <?php endif; ?>
    </script>
    <script src="assets/js/animations.js"></script>
    <script src="assets/js/animations.js"></script>
</body>
</html>