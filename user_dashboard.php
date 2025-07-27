<?php
session_start();

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in'] || $_SESSION['user']['role'] !== 'buyer') {
    header('Location: auth.php');
    exit;
}

// Initialize orders if not exists
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [
        [
            'id' => 'ECO-001',
            'items' => [1 => 1, 2 => 1], // product_id => quantity
            'total_amount' => 74.98,
            'co2_saved' => 5.0,
            'date' => '2025-01-15 14:30:00',
            'status' => 'Delivered'
        ],
        [
            'id' => 'ECO-002',
            'items' => [3 => 2],
            'total_amount' => 69.98,
            'co2_saved' => 5.0,
            'date' => '2025-01-12 10:15:00',
            'status' => 'Shipping'
        ]
    ];
}

// Sample product data for order details
$products = [
    1 => ['name' => 'Solar Power Bank', 'price' => 49.99, 'co2_saved' => 3.2],
    2 => ['name' => 'Bamboo Water Bottle', 'price' => 24.99, 'co2_saved' => 1.8],
    3 => ['name' => 'Eco Detergent Set', 'price' => 34.99, 'co2_saved' => 2.5],
    4 => ['name' => 'Reusable Food Wraps', 'price' => 19.99, 'co2_saved' => 0.8],
    5 => ['name' => 'Solar LED Lights', 'price' => 39.99, 'co2_saved' => 2.1],
    6 => ['name' => 'Organic Shampoo Bar', 'price' => 16.99, 'co2_saved' => 1.2]
];

// Calculate user stats
$totalOrders = count($_SESSION['orders']);
$totalSpent = array_sum(array_column($_SESSION['orders'], 'total_amount'));
$co2Saved = $_SESSION['user']['co2_saved'];
$treesEquivalent = ceil($co2Saved / 3.2);

// Function to get achievement badge
function getAchievementBadge($co2Saved) {
    if ($co2Saved >= 100) return ['icon' => '👑', 'name' => 'Eco Legend', 'class' => 'border-yellow-300 bg-yellow-50'];
    if ($co2Saved >= 50) return ['icon' => '🏆', 'name' => 'Climate Hero', 'class' => 'border-purple-300 bg-purple-50'];
    if ($co2Saved >= 25) return ['icon' => '🌍', 'name' => 'Planet Protector', 'class' => 'border-blue-300 bg-blue-50'];
    if ($co2Saved >= 10) return ['icon' => '🌿', 'name' => 'Eco Warrior', 'class' => 'border-green-300 bg-green-50'];
    if ($co2Saved >= 1) return ['icon' => '🌱', 'name' => 'Green Beginner', 'class' => 'border-green-200 bg-green-50'];
    return ['icon' => '🌱', 'name' => 'Getting Started', 'class' => 'border-gray-200 bg-gray-50'];
}

$userBadge = getAchievementBadge($co2Saved);

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
    <title>User Dashboard - Eco Store</title>
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
                    <a href="cart.php" class="nav-link">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
                    <a href="leaderboard.php" class="nav-link">Leaderboard</a>
                    <a href="user_dashboard.php" class="nav-link active">Dashboard</a>
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
                    <a href="cart.php" class="nav-link py-2 px-4 rounded-lg">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
                    <a href="leaderboard.php" class="nav-link py-2 px-4 rounded-lg">Leaderboard</a>
                    <a href="user_dashboard.php" class="nav-link active py-2 px-4 rounded-lg">Dashboard</a>
                    <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Dashboard Header -->
    <section class="bg-gradient-to-r from-eco-green to-eco-light text-white py-8" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h2>
                    <p class="opacity-90">Your eco-friendly journey continues</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="inline-block p-4 <?php echo $userBadge['class']; ?> rounded-lg border-2 transition-all duration-300 hover:scale-110">
                        <span class="text-3xl block mb-1 animate-bounce-eco"><?php echo $userBadge['icon']; ?></span>
                        <p class="text-sm font-semibold text-gray-800"><?php echo $userBadge['name']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Cards -->
    <section class="py-8" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" data-animate="fade-up" data-delay="0.2s">
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-800 carbon-counter" data-target="<?php echo $totalOrders; ?>"><?php echo $totalOrders; ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <span class="text-2xl">📦</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Spent</p>
                            <p class="text-2xl font-bold text-green-600">$<?php echo number_format($totalSpent, 2); ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <span class="text-2xl">💰</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">CO₂ Saved</p>
                            <p class="text-2xl font-bold text-eco-green carbon-counter" data-target="<?php echo $co2Saved; ?>"><?php echo number_format($co2Saved, 1); ?> kg</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <span class="text-2xl">🌱</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Trees Equivalent</p>
                            <p class="text-2xl font-bold text-green-600 carbon-counter" data-target="<?php echo $treesEquivalent; ?>"><?php echo $treesEquivalent; ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <span class="text-2xl">🌳</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md mb-8 transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800">Recent Orders</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CO₂ Saved</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach (array_slice($_SESSION['orders'], -5) as $order): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['id']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('M j, Y', strtotime($order['date'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold"><?php echo number_format($order['co2_saved'], 1); ?> kg</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="<?php echo $order['status'] === 'Delivered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?> px-2 py-1 rounded-full text-xs font-semibold">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Environmental Impact -->
                    <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.6s">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800">Your Environmental Impact</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="text-4xl font-bold text-eco-green mb-2"><?php echo number_format($co2Saved, 1); ?> kg</div>
                                <p class="text-gray-600">Total CO₂ Saved</p>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl">🌳</span>
                                        <span class="text-sm text-gray-700">Trees planted equivalent</span>
                                    </div>
                                    <span class="font-semibold text-green-600"><?php echo $treesEquivalent; ?></span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl">🚗</span>
                                        <span class="text-sm text-gray-700">Miles not driven</span>
                                    </div>
                                    <span class="font-semibold text-blue-600"><?php echo round($co2Saved * 2.5); ?></span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl">⚡</span>
                                        <span class="text-sm text-gray-700">kWh saved</span>
                                    </div>
                                    <span class="font-semibold text-purple-600"><?php echo round($co2Saved * 1.8); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.8s">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="products.php" class="block w-full bg-eco-green text-white text-center py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                    Browse Products
                                </a>
                                <a href="cart.php" class="block w-full border border-eco-green text-eco-green text-center py-2 rounded-lg hover:bg-eco-green hover:text-white transition-all duration-300 transform hover:scale-105">
                                    View Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)
                                </a>
                                <a href="leaderboard.php" class="block w-full border border-gray-300 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                    View Leaderboard
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Achievement Progress -->
                    <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="1.0s">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Next Achievement</h3>
                            <?php
                            $nextThreshold = 25; // Next achievement threshold
                            if ($co2Saved >= 25) $nextThreshold = 50;
                            if ($co2Saved >= 50) $nextThreshold = 100;
                            if ($co2Saved >= 100) $nextThreshold = 200;
                            
                            $progress = min(100, ($co2Saved / $nextThreshold) * 100);
                            $remaining = max(0, $nextThreshold - $co2Saved);
                            ?>
                            <div class="text-center mb-4">
                                <div class="text-2xl mb-2">🏆</div>
                                <p class="text-sm text-gray-600">
                                    <?php if ($remaining > 0): ?>
                                        <?php echo number_format($remaining, 1); ?> kg CO₂ until next badge
                                    <?php else: ?>
                                        Achievement unlocked!
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-eco-green h-3 rounded-full transition-all duration-500" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span><?php echo number_format($co2Saved, 1); ?> kg</span>
                                <span><?php echo $nextThreshold; ?> kg</span>
                            </div>
                        </div>
                    </div>
                </div>
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