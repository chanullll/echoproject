<?php
session_start();

// Initialize user session if not exists
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'name' => 'Guest',
        'role' => 'buyer',
        'co2_saved' => 0,
        'logged_in' => false
    ];
}

// Handle contact form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if ($name && $email && $subject && $message) {
        // In a real application, you would send an email or save to database
        $success = 'Thank you for your message! We\'ll get back to you soon.';
    } else {
        $error = 'Please fill in all fields before submitting the form.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Eco Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-green': '#059669',
                        'eco-light': '#10b981',
                        'eco-dark': '#047857'
                    }
                }
            }
        }
    </script>
    <style>
        .nav-link { @apply text-gray-600 hover:text-eco-green transition-colors font-medium; }
        .nav-link.active { @apply text-eco-green font-semibold; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">🌱</span>
                    <h1 class="text-2xl font-bold text-eco-green">Eco Store</h1>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="contact.php" class="nav-link active">Contact</a>
                    <a href="leaderboard.php" class="nav-link">Leaderboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="nav-link">Products</a>
                        <a href="dashboard.php" class="nav-link">Dashboard</a>
                        <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">Logout</a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors">Login</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Toggle mobile menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Navigation -->
            <div class="mobile-menu hidden md:hidden mt-4 pb-4" id="mobileMenu">
                <div class="flex flex-col space-y-2">
                    <a href="index.php" class="nav-link py-2 px-4 rounded-lg">Home</a>
                    <a href="contact.php" class="nav-link active py-2 px-4 rounded-lg">Contact</a>
                    <a href="leaderboard.php" class="nav-link py-2 px-4 rounded-lg">Leaderboard</a>
                    <?php if ($_SESSION['user']['logged_in']): ?>
                        <a href="products.php" class="nav-link py-2 px-4 rounded-lg">Products</a>
                        <a href="dashboard.php" class="nav-link py-2 px-4 rounded-lg">Dashboard</a>
                        <a href="auth.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-center">Logout</a>
                    <?php else: ?>
                        <a href="auth.php" class="bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors text-center">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-eco-green to-eco-light text-white py-16" data-animate="fade-up">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Get in Touch</h2>
            <p class="text-xl opacity-90">We'd love to hear from you about your eco-friendly journey!</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div class="bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl" data-animate="fade-up" data-delay="0.2s">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h3>
                        
                        <?php if ($error): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" id="name" required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" name="email" id="email" required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                </div>
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <select name="subject" id="subject" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="product">Product Question</option>
                                    <option value="order">Order Support</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="feedback">Feedback</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea name="message" id="message" rows="5" required 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300"
                                          placeholder="Tell us how we can help you..."></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-eco-green text-white py-3 rounded-lg font-semibold hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                Send Message
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-8" data-animate="fade-up" data-delay="0.4s">
                        <div class="bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6">Contact Information</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4 transition-all duration-300 hover:scale-105">
                                    <div class="bg-eco-green bg-opacity-10 p-3 rounded-full animate-pulse-eco">
                                        <span class="text-2xl">📧</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Email</h4>
                                        <p class="text-gray-600">hello@ecostore.com</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 transition-all duration-300 hover:scale-105">
                                    <div class="bg-eco-green bg-opacity-10 p-3 rounded-full animate-pulse-eco">
                                        <span class="text-2xl">📞</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Phone</h4>
                                        <p class="text-gray-600">+1 (555) 123-4567</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 transition-all duration-300 hover:scale-105">
                                    <div class="bg-eco-green bg-opacity-10 p-3 rounded-full animate-pulse-eco">
                                        <span class="text-2xl">📍</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Address</h4>
                                        <p class="text-gray-600">123 Green Street<br>Eco City, EC 12345</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 transition-all duration-300 hover:scale-105">
                                    <div class="bg-eco-green bg-opacity-10 p-3 rounded-full animate-pulse-eco">
                                        <span class="text-2xl">🕒</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Business Hours</h4>
                                        <p class="text-gray-600">Mon - Fri: 9:00 AM - 6:00 PM<br>Sat - Sun: 10:00 AM - 4:00 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6">Why Choose Eco Store?</h3>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3 transition-all duration-300 hover:scale-105">
                                    <span class="text-eco-green text-xl">🌱</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">100% Sustainable</h4>
                                        <p class="text-gray-600 text-sm">All our products are eco-friendly and sustainably sourced.</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 transition-all duration-300 hover:scale-105">
                                    <span class="text-eco-green text-xl">🚚</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Carbon-Neutral Shipping</h4>
                                        <p class="text-gray-600 text-sm">We offset all shipping emissions to protect our planet.</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 transition-all duration-300 hover:scale-105">
                                    <span class="text-eco-green text-xl">💚</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Impact Tracking</h4>
                                        <p class="text-gray-600 text-sm">See exactly how much CO₂ you're saving with each purchase.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-2xl">🌱</span>
                        <h1 class="text-2xl font-bold text-eco-green">Eco Store</h1>
                    </div>
                    <p class="text-gray-400">Making sustainable shopping accessible for everyone.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <div class="space-y-2">
                        <a href="index.php" class="block text-gray-400 hover:text-white">Home</a>
                        <a href="contact.php" class="block text-gray-400 hover:text-white">Contact</a>
                        <a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>
                        <?php if ($_SESSION['user']['logged_in']): ?>
                            <a href="products.php" class="block text-gray-400 hover:text-white">Products</a>
                            <a href="dashboard.php" class="block text-gray-400 hover:text-white">Dashboard</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Categories</h4>
                    <div class="space-y-2">
                        <?php if ($_SESSION['user']['logged_in']): ?>
                            <a href="products.php?category=reusables" class="block text-gray-400 hover:text-white">Reusables</a>
                            <a href="products.php?category=energy" class="block text-gray-400 hover:text-white">Green Energy</a>
                            <a href="products.php?category=home" class="block text-gray-400 hover:text-white">Home & Cleaning</a>
                        <?php else: ?>
                            <span class="block text-gray-500">Login to browse products</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>📧 hello@ecostore.com</p>
                        <p>📞 +1 (555) 123-4567</p>
                        <p>🌍 Making Earth Greener</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Eco Store. All rights reserved. 🌱 Carbon-neutral shipping available.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
<script src="assets/js/animations.js"></script>
</body>
</html>