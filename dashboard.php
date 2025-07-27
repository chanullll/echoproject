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

// Redirect to login if not logged in
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    header('Location: auth.php');
    exit;
}

// Initialize orders if not exists
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [
        [
            'id' => 'ECO-001',
            'product_name' => 'Solar Power Bank',
            'quantity' => 1,
            'price' => 49.99,
            'co2_saved' => 3.2,
            'date' => '2025-01-15',
            'status' => 'Delivered'
        ],
        [
            'id' => 'ECO-002',
            'product_name' => 'Bamboo Water Bottle',
            'quantity' => 1,
            'price' => 24.99,
            'co2_saved' => 1.8,
            'date' => '2025-01-12',
            'status' => 'Shipping'
        ]
    ];
}

// Sample data for different roles
$buyerStats = [
    'total_orders' => count($_SESSION['orders']),
    'co2_saved' => $_SESSION['user']['co2_saved'],
    'trees_equivalent' => ceil($_SESSION['user']['co2_saved'] / 3.2)
];

$sellerStats = [
    'products_listed' => 8,
    'total_sales' => 2450,
    'customer_co2_saved' => 156.4,
    'orders' => 47
];

$adminStats = [
    'total_users' => 2847,
    'products_listed' => 1256,
    'global_co2_saved' => 15847,
    'monthly_revenue' => 45230
];

// Handle role switching
if (isset($_POST['switch_role'])) {
    $_SESSION['user']['role'] = $_POST['switch_role'];
}

$currentRole = $_SESSION['user']['role'];

// Function to get achievement badge
function getAchievementBadge($co2Saved) {
    if ($co2Saved >= 100) return ['icon' => '👑', 'name' => 'Eco Legend', 'class' => 'border-yellow-300 bg-yellow-50'];
    if ($co2Saved >= 50) return ['icon' => '🏆', 'name' => 'Climate Hero', 'class' => 'border-purple-300 bg-purple-50'];
    if ($co2Saved >= 25) return ['icon' => '🌍', 'name' => 'Planet Protector', 'class' => 'border-blue-300 bg-blue-50'];
    if ($co2Saved >= 10) return ['icon' => '🌿', 'name' => 'Eco Warrior', 'class' => 'border-green-300 bg-green-50'];
    if ($co2Saved >= 1) return ['icon' => '🌱', 'name' => 'Green Beginner', 'class' => 'border-green-200 bg-green-50'];
    return ['icon' => '🌱', 'name' => 'Getting Started', 'class' => 'border-gray-200 bg-gray-50'];
}

$userBadge = getAchievementBadge($_SESSION['user']['co2_saved']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Eco Store</title>
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
        .role-tab { @apply transition-all duration-300 ease-in-out; }
        .role-tab.active { @apply bg-white text-eco-green shadow-lg; }
        .dashboard-view { display: none; }
        .dashboard-view.active { display: block; }
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
                    <a href="dashboard.php" class="nav-link active">Dashboard</a>
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
                    <a href="leaderboard.php" class="nav-link py-2 px-4 rounded-lg">Leaderboard</a>
                    <a href="dashboard.php" class="nav-link active py-2 px-4 rounded-lg">Dashboard</a>
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
                    <h2 class="text-3xl font-bold mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h2>
                    <p class="opacity-90">Your role: <span class="font-semibold"><?php echo ucfirst($currentRole); ?></span></p>
                </div>
                <div class="flex space-x-2 mt-4 md:mt-0">
                    <form method="POST" class="inline">
                        <button type="submit" name="switch_role" value="buyer" class="role-tab px-4 py-2 rounded-lg bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 transform hover:scale-105 <?php echo $currentRole === 'buyer' ? 'active' : ''; ?>">Buyer</button>
                    </form>
                    <form method="POST" class="inline">
                        <button type="submit" name="switch_role" value="seller" class="role-tab px-4 py-2 rounded-lg bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 transform hover:scale-105 <?php echo $currentRole === 'seller' ? 'active' : ''; ?>">Seller</button>
                    </form>
                    <form method="POST" class="inline">
                        <button type="submit" name="switch_role" value="admin" class="role-tab px-4 py-2 rounded-lg bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 transform hover:scale-105 <?php echo $currentRole === 'admin' ? 'active' : ''; ?>">Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Buyer Dashboard -->
    <?php if ($currentRole === 'buyer'): ?>
    <div class="dashboard-view active animate-fade-in">
        <section class="py-8" data-animate="fade-up">
            <div class="container mx-auto px-4">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" data-animate="fade-up" data-delay="0.2s">
                    <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-800 carbon-counter" data-target="<?php echo $buyerStats['total_orders']; ?>"><?php echo $buyerStats['total_orders']; ?></p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <span class="text-2xl">📦</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">CO₂ Saved</p>
                                <p class="text-2xl font-bold text-eco-green carbon-counter" data-target="<?php echo $buyerStats['co2_saved']; ?>"><?php echo number_format($buyerStats['co2_saved'], 1); ?> kg</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <span class="text-2xl">🌱</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Trees Equivalent</p>
                                <p class="text-2xl font-bold text-green-600 carbon-counter" data-target="<?php echo $buyerStats['trees_equivalent']; ?>"><?php echo $buyerStats['trees_equivalent']; ?></p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <span class="text-2xl">🌳</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Orders -->
                <div class="bg-white rounded-xl shadow-md mb-8 transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800">My Orders</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CO₂ Saved</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($_SESSION['orders'] as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['product_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $order['date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold"><?php echo $order['co2_saved']; ?> kg</td>
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

                <!-- Achievements -->
                <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.6s">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800">My Achievements</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="inline-block p-4 <?php echo $userBadge['class']; ?> rounded-lg border-2 transition-all duration-300 hover:scale-110">
                                <span class="text-4xl block mb-2 animate-bounce-eco"><?php echo $userBadge['icon']; ?></span>
                                <h4 class="font-semibold text-gray-800"><?php echo $userBadge['name']; ?></h4>
                                <p class="text-sm text-gray-600">Current Badge</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php
                            $achievements = [
                                ['threshold' => 1, 'icon' => '🌱', 'name' => 'Green Beginner', 'desc' => 'Save 1+ kg CO₂'],
                                ['threshold' => 10, 'icon' => '🌿', 'name' => 'Eco Warrior', 'desc' => 'Save 10+ kg CO₂'],
                                ['threshold' => 25, 'icon' => '🌍', 'name' => 'Planet Protector', 'desc' => 'Save 25+ kg CO₂'],
                                ['threshold' => 50, 'icon' => '🏆', 'name' => 'Climate Hero', 'desc' => 'Save 50+ kg CO₂'],
                                ['threshold' => 100, 'icon' => '👑', 'name' => 'Eco Legend', 'desc' => 'Save 100+ kg CO₂']
                            ];
                            
                            foreach ($achievements as $achievement):
                                $unlocked = $_SESSION['user']['co2_saved'] >= $achievement['threshold'];
                                $progress = min(100, ($_SESSION['user']['co2_saved'] / $achievement['threshold']) * 100);
                            ?>
                            <div class="text-center p-4 <?php echo $unlocked ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'; ?> rounded-lg border-2 transition-all duration-300 hover:scale-105" data-animate="fade-up" data-delay="<?php echo $index * 0.1; ?>s">
                                <span class="text-4xl block mb-2 <?php echo $unlocked ? 'animate-wiggle' : 'opacity-50'; ?>"><?php echo $achievement['icon']; ?></span>
                                <h4 class="font-semibold <?php echo $unlocked ? 'text-green-800' : 'text-gray-500'; ?>"><?php echo $achievement['name']; ?></h4>
                                <p class="text-sm <?php echo $unlocked ? 'text-green-600' : 'text-gray-500'; ?>"><?php echo $achievement['desc']; ?></p>
                                <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="<?php echo $unlocked ? 'bg-green-500' : 'bg-eco-green'; ?> h-2 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                                <?php if (!$unlocked): ?>
                                <p class="text-xs text-gray-500 mt-1"><?php echo number_format($_SESSION['user']['co2_saved'], 1); ?>/<?php echo $achievement['threshold']; ?> kg</p>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="leaderboard.php" class="bg-eco-green text-white px-6 py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                View Leaderboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php endif; ?>

    <!-- Seller Dashboard -->
    <?php if ($currentRole === 'seller'): ?>
    <div class="dashboard-view active animate-fade-in">
        <section class="py-8" data-animate="fade-up">
            <div class="container mx-auto px-4">
                <!-- Seller Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" data-animate="fade-up" data-delay="0.2s">
                    <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Products Listed</p>
                                <p class="text-2xl font-bold text-gray-800"><?php echo $sellerStats['products_listed']; ?></p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <span class="text-2xl">📦</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Sales</p>
                                <p class="text-2xl font-bold text-green-600">$<?php echo number_format($sellerStats['total_sales']); ?></p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <span class="text-2xl">💰</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Customer CO₂ Saved</p>
                                <p class="text-2xl font-bold text-eco-green"><?php echo $sellerStats['customer_co2_saved']; ?> kg</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <span class="text-2xl">🌱</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Orders</p>
                                <p class="text-2xl font-bold text-gray-800"><?php echo $sellerStats['orders']; ?></p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <span class="text-2xl">📈</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Management -->
                <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800">My Products</h3>
                        <button class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                            Add Product
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CO₂ Impact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Solar Power Bank</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$49.99</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold">3.2 kg/year</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">12 units</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button class="text-eco-green hover:text-eco-dark">Edit</button>
                                        <button class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Bamboo Water Bottle</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$24.99</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold">1.8 kg/year</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">18 units</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button class="text-eco-green hover:text-eco-dark">Edit</button>
                                        <button class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php endif; ?>

    <!-- Admin Dashboard -->
    <?php if ($currentRole === 'admin'): ?>
    <div class="dashboard-view active animate-fade-in">
        <section class="py-8" data-animate="fade-up">
            <div class="container mx-auto px-4">
                <!-- Admin Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" data-animate="fade-up" data-delay="0.2s">
                    <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Users</p>
                                <p class="text-2xl font-bold text-gray-800"><?php echo number_format($adminStats['total_users']); ?></p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <span class="text-2xl">👥</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Products Listed</p>
                                <p class="text-2xl font-bold text-gray-800"><?php echo number_format($adminStats['products_listed']); ?></p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <span class="text-2xl">📦</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Global CO₂ Saved</p>
                                <p class="text-2xl font-bold text-eco-green"><?php echo number_format($adminStats['global_co2_saved']); ?> kg</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <span class="text-2xl">🌍</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Monthly Revenue</p>
                                <p class="text-2xl font-bold text-green-600">$<?php echo number_format($adminStats['monthly_revenue']); ?></p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <span class="text-2xl">💰</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management -->
                <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800">User Management</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CO₂ Saved</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Admin</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold"><?php echo $_SESSION['user']['co2_saved']; ?> kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-12-15</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">EcoTech Solutions</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Seller</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold">156.4 kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-11-20</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Green Living Co.</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Seller</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold">298.7 kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-10-05</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Active</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
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
    </script>
    <script src="assets/js/animations.js"></script>
</body>
</html>