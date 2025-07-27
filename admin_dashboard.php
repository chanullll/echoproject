<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in'] || $_SESSION['user']['role'] !== 'admin') {
    header('Location: auth.php');
    exit;
}

// Sample data for admin dashboard
$adminStats = [
    'total_users' => 2847,
    'total_orders' => 1256,
    'total_revenue' => 45230.50,
    'global_co2_saved' => 15847.3,
    'products_listed' => 156,
    'active_sellers' => 23
];

// Sample user data
$recentUsers = [
    ['name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'buyer', 'co2_saved' => 15.2, 'join_date' => '2025-01-15', 'status' => 'Active'],
    ['name' => 'EcoTech Solutions', 'email' => 'contact@ecotech.com', 'role' => 'seller', 'co2_saved' => 156.4, 'join_date' => '2024-12-20', 'status' => 'Active'],
    ['name' => 'Sarah Green', 'email' => 'sarah@example.com', 'role' => 'buyer', 'co2_saved' => 28.7, 'join_date' => '2025-01-10', 'status' => 'Active'],
    ['name' => 'Green Living Co.', 'email' => 'info@greenliving.com', 'role' => 'seller', 'co2_saved' => 298.1, 'join_date' => '2024-11-15', 'status' => 'Active']
];

// Sample recent orders
$recentOrders = [
    ['id' => 'ECO-1256', 'user' => 'John Doe', 'total' => 74.98, 'co2_saved' => 5.0, 'date' => '2025-01-15', 'status' => 'Processing'],
    ['id' => 'ECO-1255', 'user' => 'Sarah Green', 'total' => 49.99, 'co2_saved' => 3.2, 'date' => '2025-01-15', 'status' => 'Shipped'],
    ['id' => 'ECO-1254', 'user' => 'Mike Johnson', 'total' => 129.97, 'co2_saved' => 8.1, 'date' => '2025-01-14', 'status' => 'Delivered'],
    ['id' => 'ECO-1253', 'user' => 'Emma Wilson', 'total' => 34.99, 'co2_saved' => 2.5, 'date' => '2025-01-14', 'status' => 'Processing']
];

// Handle admin actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_user_status':
                $message = 'User status updated successfully!';
                $messageType = 'success';
                break;
            case 'delete_user':
                $message = 'User deleted successfully!';
                $messageType = 'success';
                break;
            case 'update_order_status':
                $message = 'Order status updated successfully!';
                $messageType = 'success';
                break;
        }
    }
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
    <title>Admin Dashboard - Eco Store</title>
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
                    <a href="leaderboard.php" class="nav-link">Leaderboard</a>
                    <a href="admin_dashboard.php" class="nav-link active">Admin Dashboard</a>
                    <div class="flex items-center space-x-2 bg-red-100 px-3 py-1 rounded-lg">
                        <span class="text-red-600 text-sm">👑 Admin</span>
                    </div>
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
                    <a href="admin_dashboard.php" class="nav-link active py-2 px-4 rounded-lg">Admin Dashboard</a>
                    <div class="flex items-center justify-center space-x-2 bg-red-100 px-3 py-1 rounded-lg mx-4">
                        <span class="text-red-600 text-sm">👑 Admin</span>
                    </div>
                    <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Dashboard Header -->
    <section class="bg-gradient-to-r from-red-600 to-red-700 text-white py-8" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold mb-2">Admin Dashboard</h2>
                    <p class="opacity-90">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>! Manage your Eco Store</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 px-4 py-2 rounded-lg">
                        <span class="text-2xl">👑</span>
                        <span class="ml-2 font-semibold">Administrator</span>
                    </div>
                </div>
            </div>
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

    <!-- Admin Stats -->
    <section class="py-8" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8" data-animate="fade-up" data-delay="0.2s">
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Users</p>
                            <p class="text-2xl font-bold text-gray-800 carbon-counter" data-target="<?php echo $adminStats['total_users']; ?>"><?php echo number_format($adminStats['total_users']); ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <span class="text-2xl">👥</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-800 carbon-counter" data-target="<?php echo $adminStats['total_orders']; ?>"><?php echo number_format($adminStats['total_orders']); ?></p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <span class="text-2xl">📦</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Revenue</p>
                            <p class="text-2xl font-bold text-green-600">$<?php echo number_format($adminStats['total_revenue'], 0); ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <span class="text-2xl">💰</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Global CO₂ Saved</p>
                            <p class="text-2xl font-bold text-eco-green carbon-counter" data-target="<?php echo $adminStats['global_co2_saved']; ?>"><?php echo number_format($adminStats['global_co2_saved'], 0); ?> kg</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <span class="text-2xl">🌍</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Products Listed</p>
                            <p class="text-2xl font-bold text-gray-800 carbon-counter" data-target="<?php echo $adminStats['products_listed']; ?>"><?php echo $adminStats['products_listed']; ?></p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <span class="text-2xl">🛍️</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Active Sellers</p>
                            <p class="text-2xl font-bold text-gray-800 carbon-counter" data-target="<?php echo $adminStats['active_sellers']; ?>"><?php echo $adminStats['active_sellers']; ?></p>
                        </div>
                        <div class="bg-indigo-100 p-3 rounded-full">
                            <span class="text-2xl">🏪</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- User Management -->
                <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800">Recent Users</h3>
                        <button class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                            Add User
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CO₂ Saved</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recentUsers as $user): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="<?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : ($user['role'] === 'seller' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?> px-2 py-1 rounded-full text-xs font-semibold">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-eco-green font-semibold">
                                        <?php echo number_format($user['co2_saved'], 1); ?> kg
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button class="text-eco-green hover:text-eco-dark">Edit</button>
                                        <button class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Management -->
                <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg" data-animate="fade-up" data-delay="0.6s">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800">Recent Orders</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recentOrders as $order): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($order['id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($order['user']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-600">$<?php echo number_format($order['total'], 2); ?></div>
                                        <div class="text-xs text-eco-green"><?php echo $order['co2_saved']; ?> kg CO₂</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select class="text-sm border border-gray-300 rounded px-2 py-1 <?php echo $order['status'] === 'Delivered' ? 'bg-green-100 text-green-800' : ($order['status'] === 'Shipped' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                            <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo $order['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Environmental Impact Summary -->
    <section class="py-8" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="bg-gradient-to-r from-green-500 to-eco-green rounded-xl shadow-lg p-8 text-white" data-animate="fade-up" data-delay="0.8s">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold mb-2">Global Environmental Impact</h3>
                    <p class="opacity-90">See the collective impact of our Eco Store community</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-4xl mb-2">🌍</div>
                        <div class="text-3xl font-bold mb-1"><?php echo number_format($adminStats['global_co2_saved'], 0); ?> kg</div>
                        <div class="text-sm opacity-80">Total CO₂ Saved</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-4xl mb-2">🌳</div>
                        <div class="text-3xl font-bold mb-1"><?php echo number_format($adminStats['global_co2_saved'] / 3.2, 0); ?></div>
                        <div class="text-sm opacity-80">Trees Equivalent</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-4xl mb-2">🚗</div>
                        <div class="text-3xl font-bold mb-1"><?php echo number_format($adminStats['global_co2_saved'] * 2.5, 0); ?></div>
                        <div class="text-sm opacity-80">Miles Not Driven</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-4xl mb-2">⚡</div>
                        <div class="text-3xl font-bold mb-1"><?php echo number_format($adminStats['global_co2_saved'] * 1.8, 0); ?></div>
                        <div class="text-sm opacity-80">kWh Saved</div>
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
                    <h4 class="font-semibold mb-4">Admin Links</h4>
                    <div class="space-y-2">
                        <a href="admin_dashboard.php" class="block text-gray-400 hover:text-white">Dashboard</a>
                        <a href="products.php" class="block text-gray-400 hover:text-white">Products</a>
                        <a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Management</h4>
                    <div class="space-y-2">
                        <span class="block text-gray-400">User Management</span>
                        <span class="block text-gray-400">Order Management</span>
                        <span class="block text-gray-400">Product Management</span>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>📧 admin@ecostore.com</p>
                        <p>📞 +1 (555) 123-4567</p>
                        <p>🌍 Making Earth Greener</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Eco Store. All rights reserved. 🌱 Admin Panel</p>
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