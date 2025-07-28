<?php
/**
 * Database Setup Script
 * Run this file once to set up your PostgreSQL database
 */

require_once 'config/database.php';
require_once 'models/User.php';

try {
    $db = Database::getInstance();
    
    echo "🌱 Setting up Eco Store Database...\n\n";
    
    // Read and execute schema
    $schema = file_get_contents('database/schema.sql');
    $db->getConnection()->exec($schema);
    
    echo "✅ Database schema created successfully!\n";
    
    // Create admin user
    $userModel = new User();
    
    // Check if admin already exists
    $existingAdmin = $userModel->findByEmail('admin@ecostore.com');
    
    if (!$existingAdmin) {
        $adminId = $userModel->create([
            'name' => 'Administrator',
            'email' => 'admin@ecostore.com',
            'password' => 'admin123',
            'role' => 'admin'
        ]);
        
        echo "✅ Admin user created successfully! (ID: $adminId)\n";
        echo "   Email: admin@ecostore.com\n";
        echo "   Password: admin123\n\n";
    } else {
        echo "ℹ️  Admin user already exists.\n\n";
    }
    
    // Create sample users
    $sampleUsers = [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'buyer'
        ],
        [
            'name' => 'EcoTech Solutions',
            'email' => 'contact@ecotech.com',
            'password' => 'password123',
            'role' => 'seller',
            'business_name' => 'EcoTech Solutions'
        ]
    ];
    
    foreach ($sampleUsers as $userData) {
        $existing = $userModel->findByEmail($userData['email']);
        if (!$existing) {
            $userId = $userModel->create($userData);
            echo "✅ Sample user created: {$userData['name']} (ID: $userId)\n";
        }
    }
    
    echo "\n🎉 Database setup completed successfully!\n";
    echo "\n📝 Next steps:\n";
    echo "1. Update your .env file with your PostgreSQL credentials\n";
    echo "2. Make sure PostgreSQL is running\n";
    echo "3. Visit your website and start shopping!\n\n";
    echo "🔐 Admin Login:\n";
    echo "   Email: admin@ecostore.com\n";
    echo "   Password: admin123\n\n";
    
} catch (Exception $e) {
    echo "❌ Error setting up database: " . $e->getMessage() . "\n";
    echo "\n💡 Make sure:\n";
    echo "1. PostgreSQL is installed and running\n";
    echo "2. Database 'eco_store' exists\n";
    echo "3. Your database credentials in config/database.php are correct\n";
}
?>