<?php
// Configuration file for Eco Store

// Site Configuration
define('SITE_NAME', 'Eco Store');
define('SITE_TAGLINE', 'Making sustainable shopping accessible for everyone');
define('SITE_VERSION', '1.0.0');

// Default User Settings
define('DEFAULT_CO2_SAVED', 0);
define('DEFAULT_USER_ROLE', 'buyer');

// Product Categories
$CATEGORIES = [
    'reusables' => [
        'name' => 'Reusables',
        'emoji' => '♻️',
        'description' => 'Bottles, bags, containers'
    ],
    'energy' => [
        'name' => 'Green Energy',
        'emoji' => '⚡',
        'description' => 'Solar, wind, eco gadgets'
    ],
    'home' => [
        'name' => 'Home & Cleaning',
        'emoji' => '🏠',
        'description' => 'Natural cleaners, organics'
    ],
    'personal' => [
        'name' => 'Personal Care',
        'emoji' => '💚',
        'description' => 'Organic beauty, wellness'
    ]
];

// Achievement Levels
$ACHIEVEMENT_LEVELS = [
    [
        'threshold' => 1,
        'icon' => '🌱',
        'name' => 'Green Beginner',
        'description' => 'Save 1+ kg CO₂'
    ],
    [
        'threshold' => 10,
        'icon' => '🌿',
        'name' => 'Eco Warrior',
        'description' => 'Save 10+ kg CO₂'
    ],
    [
        'threshold' => 25,
        'icon' => '🌍',
        'name' => 'Planet Protector',
        'description' => 'Save 25+ kg CO₂'
    ],
    [
        'threshold' => 50,
        'icon' => '🏆',
        'name' => 'Climate Hero',
        'description' => 'Save 50+ kg CO₂'
    ],
    [
        'threshold' => 100,
        'icon' => '👑',
        'name' => 'Eco Legend',
        'description' => 'Save 100+ kg CO₂'
    ]
];

// Admin Credentials
$ADMIN_CREDENTIALS = [
    'email' => 'admin@ecostore.com',
    'password' => 'admin123',
    'name' => 'Administrator'
];

// Site Contact Information
$CONTACT_INFO = [
    'email' => 'hello@ecostore.com',
    'phone' => '+1 (555) 123-4567',
    'address' => '123 Green Street, Eco City, EC 12345',
    'business_hours' => [
        'weekdays' => 'Mon - Fri: 9:00 AM - 6:00 PM',
        'weekends' => 'Sat - Sun: 10:00 AM - 4:00 PM'
    ]
];

// Environmental Impact Calculations
function calculateTreesEquivalent($co2Saved) {
    return ceil($co2Saved / 3.2); // 1 tree absorbs ~3.2 kg CO₂ per year
}

function calculateMilesNotDriven($co2Saved) {
    return round($co2Saved * 2.5); // 1 kg CO₂ = ~2.5 miles not driven
}

function calculateKwhSaved($co2Saved) {
    return round($co2Saved * 1.8); // 1 kg CO₂ = ~1.8 kWh saved
}

// Utility Functions
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatCO2($amount) {
    return number_format($amount, 1) . ' kg CO₂';
}

function getAchievementForCO2($co2Saved) {
    global $ACHIEVEMENT_LEVELS;
    
    $currentAchievement = $ACHIEVEMENT_LEVELS[0];
    
    foreach ($ACHIEVEMENT_LEVELS as $level) {
        if ($co2Saved >= $level['threshold']) {
            $currentAchievement = $level;
        } else {
            break;
        }
    }
    
    return $currentAchievement;
}

function getNextAchievement($co2Saved) {
    global $ACHIEVEMENT_LEVELS;
    
    foreach ($ACHIEVEMENT_LEVELS as $level) {
        if ($co2Saved < $level['threshold']) {
            return $level;
        }
    }
    
    // If user has reached the highest level
    return end($ACHIEVEMENT_LEVELS);
}

// Session Helper Functions
function initializeUserSession() {
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = [
            'name' => 'Guest',
            'role' => DEFAULT_USER_ROLE,
            'co2_saved' => DEFAULT_CO2_SAVED,
            'logged_in' => false
        ];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user']) && $_SESSION['user']['logged_in'];
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: auth.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: auth.php');
        exit;
    }
}

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

initializeUserSession();
?>