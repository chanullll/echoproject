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

// Sample leaderboard data
$leaderboard = [
    ['rank' => 1, 'name' => 'EcoWarrior2024', 'co2_saved' => 156.4, 'badge' => 'Planet Protector'],
    ['rank' => 2, 'name' => 'GreenLiving', 'co2_saved' => 134.7, 'badge' => 'Planet Protector'],
    ['rank' => 3, 'name' => 'SustainableSarah', 'co2_saved' => 89.3, 'badge' => 'Planet Protector'],
    ['rank' => 4, 'name' => 'EcoMike', 'co2_saved' => 67.2, 'badge' => 'Planet Protector'],
    ['rank' => 5, 'name' => 'ClimateChampion', 'co2_saved' => 45.8, 'badge' => 'Planet Protector'],
    ['rank' => 6, 'name' => 'GreenThumb', 'co2_saved' => 38.9, 'badge' => 'Planet Protector'],
    ['rank' => 7, 'name' => 'EarthLover', 'co2_saved' => 24.8, 'badge' => 'Eco Warrior'],
    ['rank' => 8, 'name' => 'You (' . $_SESSION['user']['name'] . ')', 'co2_saved' => $_SESSION['user']['co2_saved'], 'badge' => 'Eco Warrior']
];

// Handle sorting
$sort = $_GET['sort'] ?? 'co2';
switch ($sort) {
    case 'purchases':
        // Simulate purchase count sorting
        usort($leaderboard, function($a, $b) { return ($b['co2_saved'] * 2) <=> ($a['co2_saved'] * 2); });
        break;
    case 'recent':
        // Simulate recent activity sorting
        $leaderboard = array_reverse($leaderboard);
        break;
    default:
        // CO2 sorting (default)
        usort($leaderboard, function($a, $b) { return $b['co2_saved'] <=> $a['co2_saved']; });
        break;
}

// Function to get achievement badge info
function getAchievementBadge($co2Saved) {
    if ($co2Saved >= 100) return ['icon' => '👑', 'name' => 'Eco Legend', 'class' => 'border-yellow-300 bg-yellow-50', 'unlocked' => true];
    if ($co2Saved >= 50) return ['icon' => '🏆', 'name' => 'Climate Hero', 'class' => 'border-purple-300 bg-purple-50', 'unlocked' => true];
    if ($co2Saved >= 25) return ['icon' => '🌍', 'name' => 'Planet Protector', 'class' => 'border-blue-300 bg-blue-50', 'unlocked' => true];
    if ($co2Saved >= 10) return ['icon' => '🌿', 'name' => 'Eco Warrior', 'class' => 'border-green-300 bg-green-50', 'unlocked' => true];
    if ($co2Saved >= 1) return ['icon' => '🌱', 'name' => 'Green Beginner', 'class' => 'border-green-200 bg-green-50', 'unlocked' => true];
    return ['icon' => '🌱', 'name' => 'Getting Started', 'class' => 'border-gray-200 bg-gray-50', 'unlocked' => false];
}

$userBadge = getAchievementBadge($_SESSION['user']['co2_saved']);

// All achievement levels
$achievements = [
    ['threshold' => 1, 'icon' => '🌱', 'name' => 'Green Beginner', 'desc' => 'Save 1+ kg CO₂'],
    ['threshold' => 10, 'icon' => '🌿', 'name' => 'Eco Warrior', 'desc' => 'Save 10+ kg CO₂'],
    ['threshold' => 25, 'icon' => '🌍', 'name' => 'Planet Protector', 'desc' => 'Save 25+ kg CO₂'],
    ['threshold' => 50, 'icon' => '🏆', 'name' => 'Climate Hero', 'desc' => 'Save 50+ kg CO₂'],
    ['threshold' => 100, 'icon' => '👑', 'name' => 'Eco Legend', 'desc' => 'Save 100+ kg CO₂']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard & Achievements - Eco Store</title>
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
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">🌱</span>
                    <h1 class="text-2xl font-bold text-eco-green">Eco Store</h1>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                    <a href="leaderboard.php" class="nav-link active">Leaderboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="nav-link">Products</a>
                        <a href="dashboard.php" class="nav-link">Dashboard</a>
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
                    <a href="contact.php" class="nav-link py-2 px-4 rounded-lg">Contact</a>
                    <a href="leaderboard.php" class="nav-link active py-2 px-4 rounded-lg">Leaderboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="nav-link py-2 px-4 rounded-lg">Products</a>
                        <a href="dashboard.php" class="nav-link py-2 px-4 rounded-lg">Dashboard</a>
                        <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors text-center">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-eco-green to-eco-light text-white py-12" data-animate="fade-up">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Eco Champions Leaderboard</h2>
            <p class="text-xl opacity-90">See how you rank among our carbon-saving community!</p>
        </div>
    </section>

    <!-- Global Impact Stats -->
    <section class="py-8 bg-white" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-green-50 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg" data-animate="fade-up" data-delay="0.1s">
                    <span class="text-3xl block mb-2">🌍</span>
                    <p class="text-2xl font-bold text-eco-green carbon-counter" data-target="15847">15,847 kg</p>
                    <p class="text-gray-600">Total CO₂ Saved</p>
                </div>
                <div class="text-center p-6 bg-blue-50 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg" data-animate="fade-up" data-delay="0.2s">
                    <span class="text-3xl block mb-2">🌳</span>
                    <p class="text-2xl font-bold text-green-600 carbon-counter" data-target="792">792</p>
                    <p class="text-gray-600">Trees Equivalent</p>
                </div>
                <div class="text-center p-6 bg-purple-50 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg" data-animate="fade-up" data-delay="0.3s">
                    <span class="text-3xl block mb-2">👥</span>
                    <p class="text-2xl font-bold text-purple-600 carbon-counter" data-target="2847">2,847</p>
                    <p class="text-gray-600">Active Users</p>
                </div>
                <div class="text-center p-6 bg-orange-50 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg" data-animate="fade-up" data-delay="0.4s">
                    <span class="text-3xl block mb-2">📦</span>
                    <p class="text-2xl font-bold text-orange-600 carbon-counter" data-target="8456">8,456</p>
                    <p class="text-gray-600">Eco Products Sold</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Leaderboard Section -->
    <section class="py-12" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Leaderboard Table -->
                <div class="lg:col-span-2" data-animate="fade-up" data-delay="0.2s">
                    <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-2xl font-semibold text-gray-800">Top Carbon Savers</h3>
                            <form method="GET" class="inline">
                                <select name="sort" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <option value="co2" <?php echo $sort === 'co2' ? 'selected' : ''; ?>>CO₂ Saved</option>
                                    <option value="purchases" <?php echo $sort === 'purchases' ? 'selected' : ''; ?>>Total Purchases</option>
                                    <option value="recent" <?php echo $sort === 'recent' ? 'selected' : ''; ?>>Most Recent</option>
                                </select>
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CO₂ Saved (kg)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Badge</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($leaderboard as $index => $user): ?>
                                        <?php
                                        $rowClass = '';
                                        $rankDisplay = '#' . ($index + 1);
                                        $crownEmoji = '';
                                        
                                        if ($index === 0) {
                                            $rowClass = 'bg-gradient-to-r from-yellow-50 to-yellow-100';
                                            $crownEmoji = '👑';
                                        } elseif ($index === 1) {
                                            $rowClass = 'bg-gradient-to-r from-gray-50 to-gray-100';
                                            $crownEmoji = '🥈';
                                        } elseif ($index === 2) {
                                            $rowClass = 'bg-gradient-to-r from-orange-50 to-orange-100';
                                            $crownEmoji = '🥉';
                                        }
                                        
                                        if (strpos($user['name'], 'You') !== false) {
                                            $rowClass .= ' border-2 border-eco-green bg-green-50';
                                        }
                                        ?>
                                        <tr class="<?php echo $rowClass; ?> transition-all duration-300 hover:bg-opacity-80" data-animate="fade-up" data-delay="<?php echo $index * 0.1; ?>s">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <?php if ($crownEmoji): ?>
                                                        <span class="text-2xl mr-2"><?php echo $crownEmoji; ?></span>
                                                    <?php endif; ?>
                                                    <span class="font-bold <?php echo $index <= 2 ? 'text-' . ($index === 0 ? 'yellow' : ($index === 1 ? 'gray' : 'orange')) . '-600' : 'text-gray-500'; ?>"><?php echo $rankDisplay; ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?php echo strpos($user['name'], 'You') !== false ? 'text-eco-green' : 'text-gray-900'; ?>">
                                                <?php echo htmlspecialchars($user['name']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xl font-bold text-eco-green">
                                                <span class="carbon-counter" data-target="<?php echo $user['co2_saved']; ?>"><?php echo number_format($user['co2_saved'], 1); ?></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold animate-pulse-eco">
                                                    🌍 <?php echo $user['badge']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Achievements Panel -->
                <div class="space-y-6" data-animate="fade-up" data-delay="0.4s">
                    <!-- Badge Collection -->
                    <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800">Achievement Badges</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <?php foreach ($achievements as $achievement): ?>
                                <?php
                                $unlocked = $_SESSION['user']['co2_saved'] >= $achievement['threshold'];
                                $progress = min(100, ($_SESSION['user']['co2_saved'] / $achievement['threshold']) * 100);
                                ?>
                                <div class="text-center p-4 <?php echo $unlocked ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'; ?> rounded-lg border-2 <?php echo !$unlocked ? 'opacity-60' : ''; ?> transition-all duration-300 hover:scale-105" data-animate="fade-up" data-delay="<?php echo array_search($achievement, $achievements) * 0.1; ?>s">
                                    <span class="text-4xl block mb-2 <?php echo $unlocked ? 'animate-bounce-eco' : 'opacity-50'; ?>"><?php echo $achievement['icon']; ?></span>
                                    <h4 class="font-semibold <?php echo $unlocked ? 'text-green-800' : 'text-gray-500'; ?>"><?php echo $achievement['name']; ?></h4>
                                    <p class="text-sm <?php echo $unlocked ? 'text-green-600' : 'text-gray-500'; ?>"><?php echo $achievement['desc']; ?></p>
                                    <?php if ($unlocked): ?>
                                        <p class="text-xs text-green-500 mt-1">✓ Unlocked</p>
                                    <?php else: ?>
                                        <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-eco-green h-2 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo number_format($_SESSION['user']['co2_saved'], 1); ?>/<?php echo $achievement['threshold']; ?> kg (<?php echo round($progress); ?>%)</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Your Progress -->
                    <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800">Your Progress</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-4">
                                <p class="text-3xl font-bold text-eco-green carbon-counter" data-target="<?php echo $_SESSION['user']['co2_saved']; ?>"><?php echo number_format($_SESSION['user']['co2_saved'], 1); ?> kg</p>
                                <p class="text-gray-600">Total CO₂ Saved</p>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span>Rank #8 of 2,847</span>
                                    <span class="text-eco-green">Top 1%</span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-eco-green h-2 rounded-full" style="width: 20%"></div>
                                </div>
                                <p class="text-xs text-gray-500 text-center">Keep going! <?php echo number_format(25 - $_SESSION['user']['co2_saved'], 1); ?> kg until Planet Protector badge</p>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <?php if ($_SESSION['user']['logged_in']): ?>
                                <a href="products.php" class="bg-eco-green text-white px-6 py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                    Shop Eco Products
                                </a>
                            <?php else: ?>
                                <a href="auth.php" class="bg-eco-green text-white px-6 py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                    Join the Community
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Boost Your Impact</h3>
                            <div class="space-y-3">
                                <?php if ($_SESSION['user']['logged_in']): ?>
                                    <a href="products.php" class="block w-full bg-eco-green text-white text-center py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                        Shop Eco Products
                                    </a>
                                    <a href="dashboard.php" class="block w-full border border-eco-green text-eco-green text-center py-2 rounded-lg hover:bg-eco-green hover:text-white transition-all duration-300 transform hover:scale-105">
                                        View Dashboard
                                    </a>
                                <?php else: ?>
                                    <a href="auth.php" class="block w-full bg-eco-green text-white text-center py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                        Join the Community
                                    </a>
                                    <a href="contact.php" class="block w-full border border-eco-green text-eco-green text-center py-2 rounded-lg hover:bg-eco-green hover:text-white transition-all duration-300 transform hover:scale-105">
                                        Contact Us
                                    </a>
                                <?php endif; ?>
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
                        <a href="contact.php" class="block text-gray-400 hover:text-white">Contact</a>
                        <a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>
                        <?php if ($_SESSION['user']['logged_in']): ?>
                            <a href="products.php" class="block text-gray-400 hover:text-white">Products</a>
                            <a href="dashboard.php" class="block text-gray-400 hover:text-white">Dashboard</a>
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