<?php
require_once 'config/database.php';

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $cartItems, $shippingAddress = null) {
        try {
            $this->db->beginTransaction();
            
            // Calculate totals
            $totalAmount = 0;
            $totalCO2Saved = 0;
            
            foreach ($cartItems as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
                $totalCO2Saved += $item['co2_saved'] * $item['quantity'];
            }
            
            // Generate order number
            $orderNumber = 'ECO-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Create order
            $orderSql = "INSERT INTO orders (user_id, order_number, total_amount, total_co2_saved, shipping_address) 
                         VALUES (:user_id, :order_number, :total_amount, :total_co2_saved, :shipping_address)";
            
            $this->db->query($orderSql, [
                ':user_id' => $userId,
                ':order_number' => $orderNumber,
                ':total_amount' => $totalAmount,
                ':total_co2_saved' => $totalCO2Saved,
                ':shipping_address' => $shippingAddress ? json_encode($shippingAddress) : null
            ]);
            
            $orderId = $this->db->lastInsertId();
            
            // Create order items
            $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price, co2_saved) 
                        VALUES (:order_id, :product_id, :quantity, :price, :co2_saved)";
            
            foreach ($cartItems as $item) {
                $this->db->query($itemSql, [
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price'],
                    ':co2_saved' => $item['co2_saved']
                ]);
            }
            
            // Update user's CO2 saved
            $userModel = new User();
            $userModel->updateCO2Saved($userId, $totalCO2Saved);
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getByUserId($userId, $limit = null) {
        $sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        
        $params = [':user_id' => $userId];
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($orderId) {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = :id";
        
        return $this->db->fetchOne($sql, [':id' => $orderId]);
    }
    
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.name as product_name, p.image_url
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id";
        
        return $this->db->fetchAll($sql, [':order_id' => $orderId]);
    }
    
    public function updateStatus($orderId, $status) {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        return $this->db->query($sql, [':status' => $status, ':id' => $orderId]);
    }
    
    public function getRecentOrders($limit = 10) {
        $sql = "SELECT o.*, u.name as user_name
                FROM orders o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':limit' => $limit]);
    }
    
    public function getTotalStats() {
        $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                SUM(total_co2_saved) as total_co2_saved,
                AVG(total_amount) as avg_order_value
                FROM orders";
        
        return $this->db->fetchOne($sql);
    }
}
?>