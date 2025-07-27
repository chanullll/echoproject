<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    header('Location: auth.php');
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Sample product data (same as products.php)
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
    ],
    [
        'id' => 5,
        'name' => 'Solar LED Lights',
        'price' => 39.99,
        'co2_saved' => 2.1,
        'category' => 'energy',
        'description' => 'Energy-efficient solar-powered LED lighting system.',
        'seller' => 'Bright Green',
        'sales' => 32
    ],
    [
        'id' => 6,
        'name' => 'Organic Shampoo Bar',
        'price' => 16.99,
        'co2_saved' => 1.2,
        'category' => 'personal',
        'description' => 'Zero-waste shampoo bar with natural ingredients.',
        'seller' => 'Natural Beauty',
        'sales' => 67
    ]
];

// Handle cart actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_quantity':
                $productId = (int)$_POST['product_id'];
                $quantity = (int)$_POST['quantity'];
                
                if ($quantity > 0) {
                    $_SESSION['cart'][$productId] = $quantity;
                    $message = 'Cart updated successfully!';
                    $messageType = 'success';
                } else {
                    unset($_SESSION['cart'][$productId]);
                    $message = 'Item removed from cart!';
                    $messageType = 'success';
                }
                break;
                
            case 'remove_item':
                $productId = (int)$_POST['product_id'];
                unset($_SESSION['cart'][$productId]);
                $message = 'Item removed from cart!';
                $messageType = 'success';
                break;
                
            case 'checkout':
                if (!empty($_SESSION['cart'])) {
                    // Calculate total CO2 saved
                    $totalCO2Saved = 0;
                    $totalAmount = 0;
                    
                    foreach ($_SESSION['cart'] as $productId => $quantity) {
                        $product = array_filter($products, function($p) use ($productId) {
                            return $p['id'] === $productId;
                        });
                        
                        if ($product) {
                            $product = array_values($product)[0];
                            $totalCO2Saved += $product['co2_saved'] * $quantity;
                            $totalAmount += $product['price'] * $quantity;
                        }
                    }
                    
                    // Add to user's CO2 saved
                    $_SESSION['user']['co2_saved'] += $totalCO2Saved;
                    
                    // Initialize orders if not exists
                    if (!isset($_SESSION['orders'])) {
                        $_SESSION['orders'] = [];
                    }
                    
                    // Create order record
                    $_SESSION['orders'][] = [
                        'id' => 'ECO-' . str_pad(count($_SESSION['orders']) + 1, 3, '0', STR_PAD_LEFT),
                        'items' => $_SESSION['cart'],
                        'total_amount' => $totalAmount,
                        'co2_saved' => $totalCO2Saved,
                        'date' => date('Y-m-d H:i:s'),
                        'status' => 'Processing'
                    ];
                    
                    // Clear cart
                    $_SESSION['cart'] = [];
                    
                    $message = "Order placed successfully! You saved {$totalCO2Saved} kg CO₂!";
                    $messageType = 'success';
                } else {
                    $message = 'Your cart is empty!';
                    $messageType = 'error';
                }
                break;
        }
    }
}

// Calculate cart totals
$cartTotal = 0;
$totalCO2 = 0;
$cartItems = [];

foreach ($_SESSION['cart'] as $productId => $quantity) {
    $product = array_filter($products, function($p) use ($productId) {
        return $p['id'] === $productId;
    });
    
    if ($product) {
        $product = array_values($product)[0];
        $product['quantity'] = $quantity;
        $product['subtotal'] = $product['price'] * $quantity;
        $cartItems[] = $product;
        $cartTotal += $product['subtotal'];
        $totalCO2 += $product['co2_saved'] * $quantity;
    }
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
    <title>Shopping Cart - Eco Store</title>
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
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="products.php" class="nav-link">Products</a>
                    <a href="cart.php" class="nav-link active">Cart (<?php echo array_sum($_SESSION['cart']); ?>)</a>
                    <a href="leaderboard.php" class="nav-link">Leaderboard</a>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php" class="nav-link">Admin</a>
                    <?php else: ?>
                        <a href="user_dashboard.php" class="nav-link">Dashboard</a>
                    <?php endif; ?>
                    <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">Logout</a>
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
                    <a href="cart.php" class="nav-link active py-2 px-4 rounded-lg">Cart (<?php echo array_sum($_SESSION['cart']); ?>)</a>
                    <a href="leaderboard.php" class="nav-link py-2 px-4 rounded-lg">Leaderboard</a>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php" class="nav-link py-2 px-4 rounded-lg">Admin</a>
                    <?php else: ?>
                        <a href="user_dashboard.php" class="nav-link py-2 px-4 rounded-lg">Dashboard</a>
                    <?php endif; ?>
                    <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Cart Header -->
    <section class="bg-gradient-to-r from-eco-green to-eco-light text-white py-12" data-animate="fade-up">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Your Shopping Cart</h2>
            <p class="text-xl opacity-90">Review your eco-friendly selections</p>
        </div>
    </section>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="container mx-auto px-4 mt-6">
            <div class="<?php echo $messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> px-4 py-3 rounded border animate-fade-in">
                <?php echo htmlspecialchars($message); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Cart Content -->
    <section class="py-12" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <?php if (empty($cartItems)): ?>
                <!-- Empty Cart -->
                <div class="text-center py-12" data-animate="fade-up">
                    <span class="text-6xl block mb-4">🛒</span>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h3>
                    <p class="text-gray-600 mb-6">Start shopping for eco-friendly products to save the planet!</p>
                    <a href="products.php" class="bg-eco-green text-white px-6 py-3 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                        Browse Products
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4" data-animate="fade-up" data-delay="0.2s">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-center space-x-4">
                                    <!-- Product Image -->
                                    <div class="w-20 h-20 bg-gradient-to-br from-green-200 to-green-300 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl"><?php echo getProductEmoji($item['category']); ?></span>
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <div class="flex items-center space-x-4">
                                            <span class="text-eco-green font-bold">$<?php echo number_format($item['price'], 2); ?></span>
                                            <span class="text-sm text-green-600">🌱 <?php echo $item['co2_saved']; ?> kg CO₂/year</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2">
                                        <form method="POST" class="flex items-center space-x-2">
                                            <input type="hidden" name="action" value="update_quantity">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <label for="quantity_<?php echo $item['id']; ?>" class="text-sm text-gray-600">Qty:</label>
                                            <select name="quantity" id="quantity_<?php echo $item['id']; ?>" onchange="this.form.submit()" 
                                                    class="px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-eco-green focus:border-transparent">
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <option value="<?php echo $i; ?>" <?php echo $item['quantity'] == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </form>
                                        
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="remove_item">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1" onclick="return confirm('Remove this item from cart?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Subtotal -->
                                    <div class="text-right">
                                        <p class="font-bold text-gray-800">$<?php echo number_format($item['subtotal'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="space-y-6" data-animate="fade-up" data-delay="0.4s">
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Order Summary</h3>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Items (<?php echo array_sum($_SESSION['cart']); ?>)</span>
                                    <span class="font-semibold">$<?php echo number_format($cartTotal, 2); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="font-semibold text-green-600">FREE 🌱</span>
                                </div>
                                <div class="border-t pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span class="text-eco-green">$<?php echo number_format($cartTotal, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Environmental Impact -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                                <h4 class="font-semibold text-green-800 mb-2">🌍 Environmental Impact</h4>
                                <p class="text-green-700 text-sm">This order will save <strong><?php echo number_format($totalCO2, 1); ?> kg CO₂</strong> annually!</p>
                                <p class="text-green-600 text-xs mt-1">Equivalent to planting <?php echo ceil($totalCO2 / 3.2); ?> tree(s) 🌳</p>
                            </div>
                            
                            <!-- Checkout Button -->
                            <form method="POST">
                                <input type="hidden" name="action" value="checkout">
                                <button type="submit" class="w-full bg-eco-green text-white py-3 rounded-lg font-semibold hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                    Place Order
                                </button>
                            </form>
                            
                            <div class="mt-4 text-center">
                                <a href="products.php" class="text-eco-green hover:text-eco-dark text-sm">← Continue Shopping</a>
                            </div>
                        </div>
                        
                        <!-- Security & Trust -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h4 class="font-semibold text-gray-800 mb-3">Why Choose Eco Store?</h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="text-green-500">✓</span>
                                    <span>Carbon-neutral shipping</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-green-500">✓</span>
                                    <span>30-day return policy</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-green-500">✓</span>
                                    <span>Sustainable packaging</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-green-500">✓</span>
                                    <span>Impact tracking</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
                        <a href="products.php" class="block text-gray-400 hover:text-white">Products</a>
                        <a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>
                        <a href="user_dashboard.php" class="block text-gray-400 hover:text-white">Dashboard</a>
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
    </script>
    <script src="assets/js/animations.js"></script>
</body>
</html>