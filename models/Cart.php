<?php
require_once 'config/database.php';

class Cart {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function addItem($userId, $productId, $quantity = 1) {
        // Check if item already exists in cart
        $existingItem = $this->getItem($userId, $productId);
        
        if ($existingItem) {
            // Update quantity
            $sql = "UPDATE cart_items SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id";
        } else {
            // Insert new item
            $sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        }
        
        return $this->db->query($sql, [
            ':user_id' => $userId,
            ':product_id' => $productId,
            ':quantity' => $quantity
        ]);
    }
    
    public function getItem($userId, $productId) {
        $sql = "SELECT * FROM cart_items WHERE user_id = :user_id AND product_id = :product_id";
        return $this->db->fetchOne($sql, [':user_id' => $userId, ':product_id' => $productId]);
    }
    
    public function getItems($userId) {
        $sql = "SELECT ci.*, p.name, p.price, p.co2_saved, p.image_url, c.slug as category
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE ci.user_id = :user_id
                ORDER BY ci.created_at DESC";
        
        return $this->db->fetchAll($sql, [':user_id' => $userId]);
    }
    
    public function updateQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $productId);
        }
        
        $sql = "UPDATE cart_items SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        return $this->db->query($sql, [
            ':quantity' => $quantity,
            ':user_id' => $userId,
            ':product_id' => $productId
        ]);
    }
    
    public function removeItem($userId, $productId) {
        $sql = "DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id";
        return $this->db->query($sql, [':user_id' => $userId, ':product_id' => $productId]);
    }
    
    public function clearCart($userId) {
        $sql = "DELETE FROM cart_items WHERE user_id = :user_id";
        return $this->db->query($sql, [':user_id' => $userId]);
    }
    
    public function getCartTotal($userId) {
        $sql = "SELECT 
                COUNT(*) as item_count,
                SUM(ci.quantity * p.price) as total_amount,
                SUM(ci.quantity * p.co2_saved) as total_co2_saved
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = :user_id";
        
        return $this->db->fetchOne($sql, [':user_id' => $userId]);
    }
}
?>