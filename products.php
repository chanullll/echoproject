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

// Filter and sort products
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'relevance';

$filteredProducts = $products;

// Apply search filter
if ($search) {
    $filteredProducts = array_filter($filteredProducts, function($product) use ($search) {
        return stripos($product['name'], $search) !== false || 
               stripos($product['description'], $search) !== false;
    });
}

// Apply category filter
if ($category) {
    $filteredProducts = array_filter($filteredProducts, function($product) use ($category) {
        return $product['category'] === $category;
    });
}

// Apply sorting
switch ($sort) {
    case 'price-low':
        usort($filteredProducts, function($a, $b) { return $a['price'] <=> $b['price']; });
        break;
    case 'price-high':
        usort($filteredProducts, function($a, $b) { return $b['price'] <=> $a['price']; });
        break;
    case 'carbon-high':
        usort($filteredProducts, function($a, $b) { return $b['co2_saved'] <=> $a['co2_saved']; });
        break;
}

// Pagination
$page = $_GET['page'] ?? 1;
$perPage = 8;
$totalProducts = count($filteredProducts);
$totalPages = ceil($totalProducts / $perPage);
$offset = ($page - 1) * $perPage;
$currentProducts = array_slice($filteredProducts, $offset, $perPage);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Eco Store</title>
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
        .product-card { @apply transition-all duration-300 ease-in-out; }
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
                    <a href="products.php" class="nav-link active">Products</a>
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
                    <a href="products.php" class="nav-link active py-2 px-4 rounded-lg">Products</a>
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

    <!-- Search and Filter Section -->
    <section class="bg-white shadow-sm py-6" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <form method="GET" class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <!-- Search Bar -->
                <div class="w-full lg:w-1/2">
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search eco-friendly products..." 
                               class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <select name="category" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                        <option value="">All Categories</option>
                        <option value="reusables" <?php echo $category === 'reusables' ? 'selected' : ''; ?>>Reusables</option>
                        <option value="energy" <?php echo $category === 'energy' ? 'selected' : ''; ?>>Green Energy</option>
                        <option value="home" <?php echo $category === 'home' ? 'selected' : ''; ?>>Home & Cleaning</option>
                        <option value="personal" <?php echo $category === 'personal' ? 'selected' : ''; ?>>Personal Care</option>
                    </select>
                    
                    <select name="sort" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                        <option value="relevance" <?php echo $sort === 'relevance' ? 'selected' : ''; ?>>Relevance</option>
                        <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="carbon-high" <?php echo $sort === 'carbon-high' ? 'selected' : ''; ?>>Carbon Saved: High to Low</option>
                    </select>
                    
                    <button type="submit" class="bg-eco-green text-white px-6 py-3 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="py-12" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <?php if (empty($currentProducts)): ?>
                <div class="text-center py-12" data-animate="fade-up">
                    <span class="text-6xl block mb-4">🔍</span>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No products found</h3>
                    <p class="text-gray-600">Try adjusting your search or filter criteria.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" data-animate="fade-up">
                    <?php foreach ($currentProducts as $product): ?>
                        <?php $badge = getCarbonBadge($product['co2_saved']); ?>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="product-card" data-animate="fade-up" data-delay="<?php echo $index * 0.1; ?>s">
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                <div class="h-48 bg-gradient-to-br from-green-200 to-green-300 relative flex items-center justify-center">
                                    <span class="text-4xl"><?php echo getProductEmoji($product['category']); ?></span>
                                    <div class="absolute top-3 right-3 carbon-badge <?php echo $badge['class']; ?> text-white px-2 py-1 rounded-full text-xs font-semibold animate-pulse-eco">
                                        <?php echo $badge['emoji']; ?> <?php echo $product['co2_saved']; ?>kg CO₂
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h4 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <p class="text-eco-green font-bold text-lg">$<?php echo number_format($product['price'], 2); ?></p>
                                    <p class="text-sm text-gray-600">Saves <?php echo $product['co2_saved']; ?> kg CO₂ per year</p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center mt-12">
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                            <?php endif; ?>
                            
                            <span class="px-4 py-2 bg-eco-green text-white rounded-lg">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
   <script src="assets/js/animations.js"></script>
</body>
</html>