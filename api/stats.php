<?php
// API endpoint for site statistics
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Sample statistics (in a real app, this would come from a database)
$stats = [
    'global' => [
        'totalUsers' => 2847,
        'totalOrders' => 1256,
        'totalRevenue' => 45230.50,
        'globalCO2Saved' => 15847.3,
        'productsListed' => 156,
        'activeSellers' => 23,
        'treesEquivalent' => ceil(15847.3 / 3.2),
        'milesNotDriven' => round(15847.3 * 2.5),
        'kwhSaved' => round(15847.3 * 1.8)
    ],
    'user' => [
        'co2Saved' => $_SESSION['user']['co2_saved'] ?? 0,
        'totalOrders' => count($_SESSION['orders'] ?? []),
        'totalSpent' => array_sum(array_column($_SESSION['orders'] ?? [], 'total_amount')),
        'rank' => 8, // Simulated rank
        'achievement' => getUserAchievement($_SESSION['user']['co2_saved'] ?? 0),
        'nextAchievement' => getNextAchievementTarget($_SESSION['user']['co2_saved'] ?? 0)
    ]
];

// Add calculated environmental impact for user
$userCO2 = $_SESSION['user']['co2_saved'] ?? 0;
$stats['user']['treesEquivalent'] = calculateTreesEquivalent($userCO2);
$stats['user']['milesNotDriven'] = calculateMilesNotDriven($userCO2);
$stats['user']['kwhSaved'] = calculateKwhSaved($userCO2);

// Calculate progress to next achievement
$nextAchievement = $stats['user']['nextAchievement'];
$progress = 0;
if ($nextAchievement['threshold'] > 0) {
    $progress = min(100, ($userCO2 / $nextAchievement['threshold']) * 100);
}
$stats['user']['progressToNext'] = $progress;
$stats['user']['remainingToNext'] = max(0, $nextAchievement['threshold'] - $userCO2);

echo json_encode($stats);

// Helper functions (these should be in functions.php in a real app)
function getUserAchievement($co2Saved) {
    $levels = [
        ['threshold' => 1, 'icon' => '🌱', 'name' => 'Green Beginner'],
        ['threshold' => 10, 'icon' => '🌿', 'name' => 'Eco Warrior'],
        ['threshold' => 25, 'icon' => '🌍', 'name' => 'Planet Protector'],
        ['threshold' => 50, 'icon' => '🏆', 'name' => 'Climate Hero'],
        ['threshold' => 100, 'icon' => '👑', 'name' => 'Eco Legend']
    ];
    
    $current = $levels[0];
    foreach ($levels as $level) {
        if ($co2Saved >= $level['threshold']) {
            $current = $level;
        }
    }
    
    return $current;
}

function getNextAchievementTarget($co2Saved) {
    $levels = [
        ['threshold' => 1, 'icon' => '🌱', 'name' => 'Green Beginner'],
        ['threshold' => 10, 'icon' => '🌿', 'name' => 'Eco Warrior'],
        ['threshold' => 25, 'icon' => '🌍', 'name' => 'Planet Protector'],
        ['threshold' => 50, 'icon' => '🏆', 'name' => 'Climate Hero'],
        ['threshold' => 100, 'icon' => '👑', 'name' => 'Eco Legend']
    ];
    
    foreach ($levels as $level) {
        if ($co2Saved < $level['threshold']) {
            return $level;
        }
    }
    
    return end($levels);
}

function calculateTreesEquivalent($co2) {
    return ceil($co2 / 3.2);
}

function calculateMilesNotDriven($co2) {
    return round($co2 * 2.5);
}

function calculateKwhSaved($co2) {
    return round($co2 * 1.8);
}
?>