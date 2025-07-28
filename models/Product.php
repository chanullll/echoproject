<?php
require_once 'config/database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category, u.name as seller_name
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN users u ON p.seller_id = u.id 
                WHERE p.is_active = true";
        
        $params = [];
        
        // Add filters
        if (!empty($filters['category'])) {
            $sql .= " AND c.slug = :category";
            $params[':category'] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name ILIKE :search OR p.description ILIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Add sorting
        $sortBy = $filters['sort'] ?? 'created_at';
        switch ($sortBy) {
            case 'price-low':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price-high':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'co2-high':
                $sql .= " ORDER BY p.co2_saved DESC";
                break;
            default:
                $sql .= " ORDER BY p.created_at DESC";
        }
        
        // Add pagination
        if (isset($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $filters['limit'];
        }
        
        if (isset($filters['offset'])) {
            $sql .= " OFFSET :offset";
            $params[':offset'] = $filters['offset'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category, u.name as seller_name
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN users u ON p.seller_id = u.id 
                WHERE p.id = :id AND p.is_active = true";
        
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    public function getFeatured($limit = 4) {
        $sql = "SELECT p.*, c.slug as category, u.name as seller 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN users u ON p.seller_id = u.id 
                WHERE p.is_active = true 
                ORDER BY p.co2_saved DESC, p.created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':limit' => $limit]);
    }
    
    public function getTopCarbonSavers($limit = 8) {
        $sql = "SELECT p.*, c.slug as category, u.name as seller 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN users u ON p.seller_id = u.id 
                WHERE p.is_active = true 
                ORDER BY p.co2_saved DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':limit' => $limit]);
    }
    
    public function getByCategory($categorySlug, $limit = null) {
        $sql = "SELECT p.*, c.slug as category, u.name as seller 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN users u ON p.seller_id = u.id 
                WHERE p.is_active = true AND c.slug = :category
                ORDER BY p.created_at DESC";
        
        $params = [':category' => $categorySlug];
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function updateStock($productId, $quantity) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity - :quantity 
                WHERE id = :id AND stock_quantity >= :quantity";
        
        $result = $this->db->query($sql, [
            ':quantity' => $quantity,
            ':id' => $productId
        ]);
        
        return $result->rowCount() > 0;
    }
    
    public function getTotalStats() {
        $sql = "SELECT 
                COUNT(*) as total_products,
                SUM(co2_saved) as total_co2_potential,
                AVG(price) as avg_price
                FROM products 
                WHERE is_active = true";
        
        return $this->db->fetchOne($sql);
    }
}
?>