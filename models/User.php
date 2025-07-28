<?php
require_once 'config/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (name, email, password_hash, role, business_name) 
                VALUES (:name, :email, :password_hash, :role, :business_name)";
        
        $params = [
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'] ?? 'buyer',
            ':business_name' => $data['business_name'] ?? null
        ];
        
        try {
            $this->db->query($sql, $params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->db->fetchOne($sql, [':email' => $email]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    public function updateCO2Saved($userId, $amount) {
        $sql = "UPDATE users SET co2_saved = co2_saved + :amount WHERE id = :id";
        return $this->db->query($sql, [':amount' => $amount, ':id' => $userId]);
    }
    
    public function getLeaderboard($limit = 10) {
        $sql = "SELECT name, co2_saved, 
                CASE 
                    WHEN co2_saved >= 100 THEN 'Eco Legend'
                    WHEN co2_saved >= 50 THEN 'Climate Hero'
                    WHEN co2_saved >= 25 THEN 'Planet Protector'
                    WHEN co2_saved >= 10 THEN 'Eco Warrior'
                    ELSE 'Green Beginner'
                END as badge
                FROM users 
                WHERE role != 'admin' 
                ORDER BY co2_saved DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':limit' => $limit]);
    }
    
    public function getTotalStats() {
        $sql = "SELECT 
                COUNT(*) as total_users,
                SUM(co2_saved) as total_co2_saved
                FROM users 
                WHERE role != 'admin'";
        
        return $this->db->fetchOne($sql);
    }
}
?>