<?php
session_start();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Handle login/register
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Check for hardcoded admin account
        if ($email === 'admin@ecostore.com' && $password === 'admin123') {
            $_SESSION['user'] = [
                'name' => 'Administrator',
                'email' => $email,
                'role' => 'admin',
                'co2_saved' => 0,
                'logged_in' => true
            ];
            header('Location: admin_dashboard.php');
            exit;
        }
        // Regular user login
        elseif ($email && $password) {
            $_SESSION['user'] = [
                'name' => 'John Doe',
                'email' => $email,
                'role' => 'buyer',
                'co2_saved' => 20.2,
                'logged_in' => true
            ];
            header('Location: user_dashboard.php');
            exit;
        } else {
            $error = 'Please fill in all fields.';
        }
    } elseif ($_POST['action'] === 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'buyer';
        $businessName = $_POST['business_name'] ?? '';
        
        // Simple validation
        if ($name && $email && $password) {
            $_SESSION['user'] = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'business_name' => $businessName,
                'co2_saved' => 0,
                'logged_in' => true
            ];
            // Redirect based on role
            if ($role === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: user_dashboard.php');
            }
            exit;
        } else {
            $error = 'Please fill in all required fields.';
        }
    }
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['user']) && $_SESSION['user']['logged_in']) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: user_dashboard.php');
    }
    exit;
}

// Function to get logo link based on user role
function getLogoLink($user) {
    if (!$user['logged_in']) {
        return 'index.php';
    }
    
    switch ($user['role']) {
        case 'admin':
            return 'admin_dashboard.php';
        case 'buyer':
        default:
            return 'products.php';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Eco Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        .auth-tab { @apply transition-all duration-300 ease-in-out; }
        .auth-tab.active { @apply bg-eco-green text-white shadow-lg; }
        .auth-form { display: none; }
        .auth-form.active { display: block; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="index.php" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                    <span class="text-2xl">🌱</span>
                    <h1 class="text-2xl font-bold text-eco-green">Eco Store</h1>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                    <a href="leaderboard.php" class="nav-link">Leaderboard</a>
                    <a href="auth.php" class="nav-link active bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors">Login</a>
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
                    <a href="contact.php" class="nav-link py-2 px-4 rounded-lg">Contact</a>
                    <a href="leaderboard.php" class="nav-link py-2 px-4 rounded-lg">Leaderboard</a>
                    <a href="auth.php" class="nav-link active bg-eco-green text-white px-4 py-2 rounded-lg hover:bg-eco-dark transition-colors text-center">Login</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Auth Section -->
    <section class="py-12 min-h-screen flex items-center" data-animate="fade-up">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                <!-- Error/Success Messages -->
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

                <!-- Tab Navigation -->
                <div class="flex mb-6 bg-gray-200 rounded-lg p-1" data-animate="fade-up" data-delay="0.2s">
                    <button id="loginTab" onclick="switchToLogin()" class="flex-1 py-2 px-4 rounded-md font-semibold transition-all duration-300 auth-tab active transform hover:scale-105">
                        Login
                    </button>
                    <button id="registerTab" onclick="switchToRegister()" class="flex-1 py-2 px-4 rounded-md font-semibold transition-all duration-300 auth-tab transform hover:scale-105">
                        Register
                    </button>
                </div>

                <!-- Login Form -->
                <div id="loginForm" class="auth-form active" data-animate="fade-up" data-delay="0.4s">
                    <div class="bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl">
                        <div class="text-center mb-8">
                            <span class="text-4xl block mb-4 animate-wiggle">🌱</span>
                            <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
                            <p class="text-gray-600">Sign in to your eco-friendly account</p>
                        </div>

                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="login">
                            
                            <div>
                                <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <div class="relative">
                                    <input type="email" name="email" id="loginEmail" required 
                                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <div class="relative">
                                    <input type="password" name="password" id="loginPassword" required 
                                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <input type="checkbox" class="w-4 h-4 text-eco-green border-gray-300 rounded focus:ring-eco-green">
                                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                </label>
                                <a href="#" class="text-sm text-eco-green hover:text-eco-dark">Forgot password?</a>
                            </div>

                            <button type="submit" class="w-full bg-eco-green text-white py-3 rounded-lg font-semibold hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                Sign In
                            </button>
                        </form>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                New to Eco Store? 
                                <button class="text-eco-green hover:text-eco-dark font-semibold" onclick="switchToRegister()">Create account</button>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Register Form -->
                <div id="registerForm" class="auth-form">
                    <div class="bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl">
                        <div class="text-center mb-8">
                            <span class="text-4xl block mb-4 animate-wiggle">🌱</span>
                            <h2 class="text-2xl font-bold text-gray-800">Join Eco Store</h2>
                            <p class="text-gray-600">Start your sustainable journey today</p>
                        </div>

                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="register">
                            
                            <div>
                                <label for="registerName" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <div class="relative">
                                    <input type="text" name="name" id="registerName" required 
                                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <div class="relative">
                                    <input type="email" name="email" id="registerEmail" required 
                                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <div class="relative">
                                    <input type="password" name="password" id="registerPassword" required 
                                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <label for="userRole" class="block text-sm font-medium text-gray-700 mb-1">I want to</label>
                                <select name="role" id="userRole" onchange="toggleBusinessField()" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <option value="buyer">Buy eco-friendly products</option>
                                    <option value="seller">Sell sustainable products</option>
                                </select>
                            </div>

                            <div id="businessNameField" class="hidden">
                                <label for="businessName" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                <div class="relative">
                                    <input type="text" name="business_name" id="businessName" 
                                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-eco-green focus:border-transparent transition-all duration-300">
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" required class="w-4 h-4 text-eco-green border-gray-300 rounded focus:ring-eco-green">
                                <span class="ml-2 text-sm text-gray-600">I agree to the <a href="#" class="text-eco-green hover:text-eco-dark">Terms of Service</a> and <a href="#" class="text-eco-green hover:text-eco-dark">Privacy Policy</a></span>
                            </div>

                            <button type="submit" class="w-full bg-eco-green text-white py-3 rounded-lg font-semibold hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                                Create Account
                            </button>
                        </form>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Already have an account? 
                                <button class="text-eco-green hover:text-eco-dark font-semibold" onclick="switchToLogin()">Sign in</button>
                            </p>
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
                        <a href="contact.php" class="block text-gray-400 hover:text-white">Contact</a>
                        <a href="leaderboard.php" class="block text-gray-400 hover:text-white">Leaderboard</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Categories</h4>
                    <div class="space-y-2">
                        <span class="block text-gray-500">Login to browse products</span>
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
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.classList.add('animate-slide-down');
            } else {
                menu.classList.add('animate-slide-up');
                setTimeout(() => {
                    menu.classList.add('hidden');
                    menu.classList.remove('animate-slide-up', 'animate-slide-down');
                }, 300);
            }
        }

        function switchToLogin() {
            document.getElementById('loginTab').classList.add('active');
            document.getElementById('registerTab').classList.remove('active');
            document.getElementById('loginForm').classList.add('active', 'animate-fade-in');
            document.getElementById('registerForm').classList.remove('active');
        }

        function switchToRegister() {
            document.getElementById('registerTab').classList.add('active');
            document.getElementById('loginTab').classList.remove('active');
            document.getElementById('registerForm').classList.add('active', 'animate-fade-in');
            document.getElementById('loginForm').classList.remove('active');
        }

        function toggleBusinessField() {
            const roleSelect = document.getElementById('userRole');
            const businessField = document.getElementById('businessNameField');
            const businessInput = document.getElementById('businessName');
            
            if (roleSelect.value === 'seller') {
                businessField.classList.remove('hidden');
                businessField.classList.add('animate-slide-down');
                businessInput.setAttribute('required', 'required');
            } else {
                businessField.classList.add('animate-slide-up');
                setTimeout(() => businessField.classList.add('hidden'), 300);
                businessInput.removeAttribute('required');
            }
        }
    </script>
    <script src="assets/js/animations.js"></script>
</body>
</html>