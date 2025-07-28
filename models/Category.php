<?php
require_once 'config/database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function getBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = :slug";
        return $this->db->fetchOne($sql, [':slug' => $slug]);
    }
    
    public function getWithProductCount() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = true
                GROUP BY c.id, c.name, c.slug, c.emoji, c.description, c.created_at
                ORDER BY c.name ASC";
        
        return $this->db->fetchAll($sql);
    }
}
?>