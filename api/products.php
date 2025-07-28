<?php
// API endpoint for product data
header('Content-Type: application/json');
session_start();

// Check if user is logged in for API access
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Sample product data (in a real app, this would come from a database)
$products = [
    [
        'id' => 1,
        'name' => 'Solar Power Bank',
        'price' => 49.99,
        'co2_saved' => 3.2,
        'category' => 'energy',
        'description' => 'High-capacity solar power bank with renewable charging capability.',
        'seller' => 'EcoTech Solutions',
        'sales' => 12,
        'image' => 'solar-power-bank.jpg',
        'stock' => 25,
        'rating' => 4.8,
        'reviews' => 156
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
        'image' => 'bamboo-bottle.jpg',
        'stock' => 42,
        'rating' => 4.6,
        'reviews' => 89
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
        'image' => 'eco-detergent.jpg',
        'stock' => 18,
        'rating' => 4.9,
        'reviews' => 203
    ],
    [
        'id' => 4,
        'name' => 'Reusable Food Wraps',
        'price' => 19.99,
        'co2_saved' => 0.8,
        'category' => 'reusables',
        'description' => 'Beeswax food wraps to replace plastic wrap.',
        'seller' => 'Bee Sustainable',
        'sales' => 45,
        'image' => 'food-wraps.jpg',
        'stock' => 67,
        'rating' => 4.4,
        'reviews' => 124
    ],
    [
        'id' => 5,
        'name' => 'Solar LED Lights',
        'price' => 39.99,
        'co2_saved' => 2.1,
        'category' => 'energy',
        'description' => 'Energy-efficient solar-powered LED lighting system.',
        'seller' => 'Bright Green',
        'sales' => 32,
        'image' => 'solar-lights.jpg',
        'stock' => 31,
        'rating' => 4.7,
        'reviews' => 78
    ],
    [
        'id' => 6,
        'name' => 'Organic Shampoo Bar',
        'price' => 16.99,
        'co2_saved' => 1.2,
        'category' => 'personal',
        'description' => 'Zero-waste shampoo bar with natural ingredients.',
        'seller' => 'Natural Beauty',
        'sales' => 67,
        'image' => 'shampoo-bar.jpg',
        'stock' => 89,
        'rating' => 4.5,
        'reviews' => 167
    ]
];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Get single product
            $id = (int)$_GET['id'];
            $product = array_filter($products, function($p) use ($id) {
                return $p['id'] === $id;
            });
            
            if (empty($product)) {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
            } else {
                echo json_encode(array_values($product)[0]);
            }
        } else {
            // Get all products with optional filtering
            $filteredProducts = $products;
            
            // Filter by category
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category = $_GET['category'];
                $filteredProducts = array_filter($filteredProducts, function($p) use ($category) {
                    return $p['category'] === $category;
                });
            }
            
            // Search by name or description
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = strtolower($_GET['search']);
                $filteredProducts = array_filter($filteredProducts, function($p) use ($search) {
                    return strpos(strtolower($p['name']), $search) !== false ||
                           strpos(strtolower($p['description']), $search) !== false;
                });
            }
            
            // Sort products
            $sort = $_GET['sort'] ?? 'name';
            switch ($sort) {
                case 'price-low':
                    usort($filteredProducts, function($a, $b) {
                        return $a['price'] <=> $b['price'];
                    });
                    break;
                case 'price-high':
                    usort($filteredProducts, function($a, $b) {
                        return $b['price'] <=> $a['price'];
                    });
                    break;
                case 'co2-high':
                    usort($filteredProducts, function($a, $b) {
                        return $b['co2_saved'] <=> $a['co2_saved'];
                    });
                    break;
                case 'rating':
                    usort($filteredProducts, function($a, $b) {
                        return $b['rating'] <=> $a['rating'];
                    });
                    break;
                default:
                    // Sort by name (default)
                    usort($filteredProducts, function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });
            }
            
            echo json_encode([
                'products' => array_values($filteredProducts),
                'total' => count($filteredProducts),
                'filters' => [
                    'category' => $_GET['category'] ?? null,
                    'search' => $_GET['search'] ?? null,
                    'sort' => $sort
                ]
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>