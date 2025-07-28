<?php
// API endpoint for cart operations
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        // Get cart contents
        echo json_encode([
            'cart' => $_SESSION['cart'],
            'itemCount' => array_sum($_SESSION['cart']),
            'timestamp' => time()
        ]);
        break;
        
    case 'POST':
        // Add item to cart
        if (!isset($input['productId']) || !isset($input['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing productId or quantity']);
            exit;
        }
        
        $productId = (int)$input['productId'];
        $quantity = (int)$input['quantity'];
        
        if ($quantity <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Quantity must be greater than 0']);
            exit;
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Item added to cart',
            'cart' => $_SESSION['cart'],
            'itemCount' => array_sum($_SESSION['cart'])
        ]);
        break;
        
    case 'PUT':
        // Update cart item quantity
        if (!isset($input['productId']) || !isset($input['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing productId or quantity']);
            exit;
        }
        
        $productId = (int)$input['productId'];
        $quantity = (int)$input['quantity'];
        
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            $message = 'Item removed from cart';
        } else {
            $_SESSION['cart'][$productId] = $quantity;
            $message = 'Cart updated';
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'cart' => $_SESSION['cart'],
            'itemCount' => array_sum($_SESSION['cart'])
        ]);
        break;
        
    case 'DELETE':
        if (isset($_GET['productId'])) {
            // Remove specific item
            $productId = (int)$_GET['productId'];
            unset($_SESSION['cart'][$productId]);
            $message = 'Item removed from cart';
        } else {
            // Clear entire cart
            $_SESSION['cart'] = [];
            $message = 'Cart cleared';
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'cart' => $_SESSION['cart'],
            'itemCount' => array_sum($_SESSION['cart'])
        ]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>