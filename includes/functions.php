<?php
// Common functions used across the Eco Store application

require_once 'config.php';

/**
 * Get carbon impact badge information
 */
function getCarbonBadge($co2Amount) {
    if ($co2Amount < 1) {
        return [
            'emoji' => '🟢',
            'text' => 'Low Impact',
            'class' => 'bg-green-500',
            'textClass' => 'text-green-800',
            'bgClass' => 'bg-green-100'
        ];
    }
    if ($co2Amount < 2) {
        return [
            'emoji' => '🟡',
            'text' => 'Medium Impact',
            'class' => 'bg-yellow-500',
            'textClass' => 'text-yellow-800',
            'bgClass' => 'bg-yellow-100'
        ];
    }
    return [
        'emoji' => '🔴',
        'text' => 'High Impact',
        'class' => 'bg-red-500',
        'textClass' => 'text-red-800',
        'bgClass' => 'bg-red-100'
    ];
}

/**
 * Get product emoji based on category
 */
function getProductEmoji($category) {
    global $CATEGORIES;
    return $CATEGORIES[$category]['emoji'] ?? '🌱';
}

/**
 * Get logo link based on user role
 */
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

/**
 * Generate navigation HTML
 */
function generateNavigation($currentPage = '', $user = null) {
    if (!$user) {
        global $_SESSION;
        $user = $_SESSION['user'] ?? ['logged_in' => false];
    }
    
    $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    
    $nav = '<div class="hidden md:flex items-center space-x-6">';
    
    // Common navigation items
    $nav .= '<a href="index.php" class="nav-link' . ($currentPage === 'home' ? ' active' : '') . '">Home</a>';
    
    if ($user['logged_in']) {
        $nav .= '<a href="products.php" class="nav-link' . ($currentPage === 'products' ? ' active' : '') . '">Products</a>';
        $nav .= '<a href="cart.php" class="nav-link' . ($currentPage === 'cart' ? ' active' : '') . '">Cart (' . $cartCount . ')</a>';
    }
    
    $nav .= '<a href="leaderboard.php" class="nav-link' . ($currentPage === 'leaderboard' ? ' active' : '') . '">Leaderboard</a>';
    $nav .= '<a href="contact.php" class="nav-link' . ($currentPage === 'contact' ? ' active' : '') . '">Contact</a>';
    
    if ($user['logged_in']) {
        if ($user['role'] === 'admin') {
            $nav .= '<a href="admin_dashboard.php" class="nav-link' . ($currentPage === 'admin' ? ' active' : '') . '">Admin</a>';
        } else {
            $nav .= '<a href="user_dashboard.php" class="nav-link' . ($currentPage === 'dashboard' ? ' active' : '') . '">Dashboard</a>';
        }
        $nav .= '<a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">Logout</a>';
    } else {
        $nav .= '<a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors">Login</a>';
    }
    
    $nav .= '</div>';
    
    return $nav;
}

/**
 * Generate mobile navigation HTML
 */
function generateMobileNavigation($currentPage = '', $user = null) {
    if (!$user) {
        global $_SESSION;
        $user = $_SESSION['user'] ?? ['logged_in' => false];
    }
    
    $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    
    $nav = '<div class="mobile-menu hidden md:hidden mt-4 pb-4" id="mobileMenu">';
    $nav .= '<div class="flex flex-col space-y-2">';
    
    // Mobile navigation items
    $nav .= '<a href="index.php" class="nav-link' . ($currentPage === 'home' ? ' active' : '') . ' py-2 px-4 rounded-lg">Home</a>';
    
    if ($user['logged_in']) {
        $nav .= '<a href="products.php" class="nav-link' . ($currentPage === 'products' ? ' active' : '') . ' py-2 px-4 rounded-lg">Products</a>';
        $nav .= '<a href="cart.php" class="nav-link' . ($currentPage === 'cart' ? ' active' : '') . ' py-2 px-4 rounded-lg">Cart (' . $cartCount . ')</a>';
    }
    
    $nav .= '<a href="leaderboard.php" class="nav-link' . ($currentPage === 'leaderboard' ? ' active' : '') . ' py-2 px-4 rounded-lg">Leaderboard</a>';
    $nav .= '<a href="contact.php" class="nav-link' . ($currentPage === 'contact' ? ' active' : '') . ' py-2 px-4 rounded-lg">Contact</a>';
    
    if ($user['logged_in']) {
        if ($user['role'] === 'admin') {
            $nav .= '<a href="admin_dashboard.php" class="nav-link' . ($currentPage === 'admin' ? ' active' : '') . ' py-2 px-4 rounded-lg">Admin</a>';
        } else {
            $nav .= '<a href="user_dashboard.php" class="nav-link' . ($currentPage === 'dashboard' ? ' active' : '') . ' py-2 px-4 rounded-lg">Dashboard</a>';
        }
        $nav .= '<a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>';
    } else {
        $nav .= '<a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors text-center">Login</a>';
    }
    
    $nav .= '</div></div>';
    
    return $nav;
}

/**
 * Generate footer HTML
 */
function generateFooter($user = null) {
    if (!$user) {
        global $_SESSION;
        $user = $_SESSION['user'] ?? ['logged_in' => false];
    }
    
    global $CONTACT_INFO;
    
    $footer = '<footer class="bg-gray-800 text-white py-12">';
    $footer .= '<div class="container mx-auto px-4">';
    $footer .= '<div class="grid grid-cols-1 md:grid-cols-4 gap-8">';
    
    // Brand section
    $footer .= '<div>';
    $footer .= '<div class="flex items-center space-x-2 mb-4">';
    $footer .= '<span class="text-2xl">🌱</span>';
    $footer .= '<h1 class="text-2xl font-bold text-eco-green">' . SITE_NAME . '</h1>';
    $footer .= '</div>';
    $footer .= '<p class="text-gray-400">' . SITE_TAGLINE . '</p>';
    $footer .= '</div>';
    
    // Quick Links
    $footer .= '<div>';
    $footer .= '<h4 class="font-semibold mb-4">Quick Links</h4>';
    $footer .= '<div class="space-y-2">';
    $footer .= '<a href="contact.php" class="block text-gray-400 hover:text-white">Contact</a>';
    $footer .= '<a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>';
    
    if ($user['logged_in']) {
        $footer .= '<a href="products.php" class="block text-gray-400 hover:text-white">Products</a>';
        if ($user['role'] === 'admin') {
            $footer .= '<a href="admin_dashboard.php" class="block text-gray-400 hover:text-white">Admin</a>';
        } else {
            $footer .= '<a href="user_dashboard.php" class="block text-gray-400 hover:text-white">Dashboard</a>';
        }
    }
    
    $footer .= '</div></div>';
    
    // Categories
    $footer .= '<div>';
    $footer .= '<h4 class="font-semibold mb-4">Categories</h4>';
    $footer .= '<div class="space-y-2">';
    
    if ($user['logged_in']) {
        global $CATEGORIES;
        foreach ($CATEGORIES as $key => $category) {
            $footer .= '<a href="products.php?category=' . $key . '" class="block text-gray-400 hover:text-white">' . $category['name'] . '</a>';
        }
    } else {
        $footer .= '<span class="block text-gray-500">Login to browse products</span>';
    }
    
    $footer .= '</div></div>';
    
    // Contact Info
    $footer .= '<div>';
    $footer .= '<h4 class="font-semibold mb-4">Contact</h4>';
    $footer .= '<div class="space-y-2 text-gray-400">';
    $footer .= '<p>📧 ' . $CONTACT_INFO['email'] . '</p>';
    $footer .= '<p>📞 ' . $CONTACT_INFO['phone'] . '</p>';
    $footer .= '<p>🌍 Making Earth Greener</p>';
    $footer .= '</div></div>';
    
    $footer .= '</div>';
    
    // Copyright
    $footer .= '<div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">';
    $footer .= '<p>&copy; 2025 ' . SITE_NAME . '. All rights reserved. 🌱 Carbon-neutral shipping available.</p>';
    $footer .= '</div>';
    
    $footer .= '</div></footer>';
    
    return $footer;
}

/**
 * Sanitize and validate input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate random order ID
 */
function generateOrderId() {
    return 'ECO-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Format date for display
 */
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

/**
 * Calculate progress percentage
 */
function calculateProgress($current, $target) {
    return min(100, ($current / $target) * 100);
}

/**
 * Get user's current achievement level
 */
function getUserAchievement($co2Saved) {
    return getAchievementForCO2($co2Saved);
}

/**
 * Get user's next achievement target
 */
function getNextAchievementTarget($co2Saved) {
    return getNextAchievement($co2Saved);
}

/**
 * Log user activity (for future database implementation)
 */
function logActivity($userId, $action, $details = []) {
    // Placeholder for activity logging
    // In a real application, this would write to a database
    error_log("User Activity: $userId - $action - " . json_encode($details));
}
?>