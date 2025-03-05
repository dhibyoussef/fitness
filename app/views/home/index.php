<?php
// File: C:\xampp\htdocs\fitness-app\public\index.php
use App\Controllers\BaseController;

require_once 'config/config.php'; // Load config for BASE_URL and constants
require_once 'config/database.php'; // Load database connection
require_once 'app/controllers/BaseController.php'; // Load BaseController for rendering

$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', ''); // Update with your DB credentials
$baseController = new BaseController($pdo);
$pageTitle = 'Fitness Tracker - Home';

// Check for flash messages set by BaseController
$successMessage = $baseController->getFlashMessage('success');
$errorMessage = $baseController->getFlashMessage('error');

// Handle theme toggle via POST (if submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_theme']) && $baseController->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['dark_mode'] = !($_SESSION['dark_mode'] ?? false);
    // Set a cookie to remember the theme preference across pages
    setcookie('dark_mode', $_SESSION['dark_mode'] ? '1' : '0', time() + (86400 * 30), "/"); // 30 days
}

// Check cookie for dark mode preference
if (isset($_COOKIE['dark_mode'])) {
    $_SESSION['dark_mode'] = $_COOKIE['dark_mode'] === '1';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f0f2f5, #e9ecef);
        transition: background 0.3s ease, color 0.3s ease;
        min-height: 100vh;
    }

    .dark-mode {
        background: linear-gradient(135deg, #1a202c, #2d3748);
        color: #e2e8f0;
    }

    .hero-section {
        background-image: url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
        height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
    }

    .dark-mode .hero-section {
        color: #e2e8f0;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    .dark-mode .hero-overlay {
        background: rgba(0, 0, 0, 0.7);
    }

    .hero-content {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    .dark-mode .hero-content {
        background: rgba(45, 55, 72, 0.2);
    }

    .feature-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.3);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .dark-mode .feature-card {
        background: #2d3748;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3), inset 0 0 0 1px rgba(255, 255, 255, 0.1);
    }

    .get-started-btn {
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .get-started-btn:hover {
        transform: scale(1.05);
        background-color: #2563eb;
    }

    .fade-in-section {
        opacity: 0;
        transition: opacity 0.5s ease-in;
    }

    .fade-in-section.visible {
        opacity: 1;
    }
    </style>
</head>

<body
    class="bg-gray-100 flex flex-col <?php echo (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : ''; ?>">
    <!-- Header -->
    <header class="bg-white shadow-md dark-mode:bg-gray-800">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-gray-800 dark-mode:text-white">Fitness Tracker</a>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/user/profile"
                        class="text-gray-600 hover:text-blue-500 dark-mode:text-gray-300 dark-mode:hover:text-blue-400">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Profile'); ?>
                    </a>
                    <form method="POST" action="/auth/logout" class="inline">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($baseController->generateCsrfToken()); ?>">
                        <button type="submit"
                            class="text-gray-600 hover:text-red-500 dark-mode:text-gray-300 dark-mode:hover:text-red-400 bg-transparent border-0 p-0">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="/" class="inline">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($baseController->generateCsrfToken()); ?>">
                        <input type="hidden" name="toggle_theme" value="1">
                        <button type="submit" id="theme-toggle"
                            class="text-gray-600 hover:text-blue-500 dark-mode:text-gray-300 dark-mode:hover:text-blue-400 bg-transparent border-0 p-0">
                            <i
                                class="fas <?php echo (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'fa-sun' : 'fa-moon'; ?>"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section animate__animated animate__fadeIn">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="text-5xl font-bold mb-4">Transform Your Body, Transform Your Life</h1>
            <p class="text-xl mb-8">Track your fitness journey with our comprehensive tools</p>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/dashboard"
                class="get-started-btn bg-blue-500 text-white px-8 py-3 rounded-md text-lg font-semibold">Go to
                Dashboard</a>
            <?php else: ?>
            <a href="auth/signup"
                class="get-started-btn bg-blue-500 text-white px-8 py-3 rounded-md text-lg font-semibold">Get
                Started</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Flash Messages -->
    <?php if ($successMessage): ?>
    <div class="container mx-auto mt-4">
        <div class="alert alert-success animate__animated animate__fadeIn">
            <?php echo htmlspecialchars($successMessage); ?></div>
    </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
    <div class="container mx-auto mt-4">
        <div class="alert alert-danger animate__animated animate__shakeX">
            <?php echo htmlspecialchars($errorMessage); ?></div>
    </div>
    <?php endif; ?>

    <!-- Features Section -->
    <section class="py-16 bg-gray-100 fade-in-section dark-mode:bg-gray-900" id="features">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 dark-mode:text-white">Why Choose Us</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-6 animate__animated animate__zoomIn">
                        <div class="text-4xl text-blue-500 mb-4">💪</div>
                        <h3 class="text-xl font-semibold mb-2">Personalized Workouts</h3>
                        <p class="text-gray-600 dark-mode:text-gray-300">Get custom workout plans tailored to your
                            fitness goals and experience level.</p>
                        <a href="/workout/index" class="btn btn-outline-primary mt-3">Explore Workouts</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-6 animate__animated animate__zoomIn" style="animation-delay: 0.2s;">
                        <div class="text-4xl text-blue-500 mb-4">🥗</div>
                        <h3 class="text-xl font-semibold mb-2">Nutrition Tracking</h3>
                        <p class="text-gray-600 dark-mode:text-gray-300">Log your meals and track your macros with our
                            easy-to-use tools.</p>
                        <a href="/nutrition/index" class="btn btn-outline-primary mt-3">Track Nutrition</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-6 animate__animated animate__zoomIn" style="animation-delay: 0.4s;">
                        <div class="text-4xl text-blue-500 mb-4">📊</div>
                        <h3 class="text-xl font-semibold mb-2">Progress Monitoring</h3>
                        <p class="text-gray-600 dark-mode:text-gray-300">Visualize your fitness journey with detailed
                            analytics.</p>
                        <a href="/progress/index" class="btn btn-outline-primary mt-3">View Progress</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 text-gray-800 py-8 mt-auto dark-mode:bg-gray-800 dark-mode:text-gray-300">
        <div class="container mx-auto px-6">
            <div class="row">
                <div class="col-md-4 mb-6">
                    <h3 class="text-lg font-semibold mb-2">Fitness Tracker</h3>
                    <p class="text-gray-600 dark-mode:text-gray-300">Your partner in achieving your fitness goals.</p>
                </div>
                <div class="col-md-4 mb-6">
                    <h3 class="text-lg font-semibold mb-2">Quick Links</h3>
                    <ul class="text-gray-600 dark-mode:text-gray-300">
                        <li><a href="#" class="hover:text-gray-800 dark-mode:hover:text-white"
                                onclick="alert('Coming Soon!')">About Us</a></li>
                        <li><a href="#" class="hover:text-gray-800 dark-mode:hover:text-white"
                                onclick="alert('Coming Soon!')">Contact</a></li>
                        <li><a href="#" class="hover:text-gray-800 dark-mode:hover:text-white"
                                onclick="alert('Coming Soon!')">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-6">
                    <h3 class="text-lg font-semibold mb-2">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="text-gray-600 dark-mode:text-gray-300 hover:text-gray-800 dark-mode:hover:text-white"
                            onclick="alert('Coming Soon!')">Facebook</a>
                        <a href="#"
                            class="text-gray-600 dark-mode:text-gray-300 hover:text-gray-800 dark-mode:hover:text-white"
                            onclick="alert('Coming Soon!')">Twitter</a>
                        <a href="#"
                            class="text-gray-600 dark-mode:text-gray-300 hover:text-gray-800 dark-mode:hover:text-white"
                            onclick="alert('Coming Soon!')">Instagram</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-300 mt-8 pt-8 text-sm text-gray-600 text-center dark-mode:text-gray-300">
                © <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script>
    // Animations
    window.addEventListener('scroll', function() {
        const featuresSection = document.getElementById('features');
        const rect = featuresSection.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            featuresSection.classList.add('visible');
        }
    });

    gsap.from('.hero-content', {
        opacity: 0,
        y: 50,
        duration: 1
    });
    gsap.from('.feature-card', {
        opacity: 0,
        y: 20,
        stagger: 0.2,
        duration: 1,
        delay: 0.5
    });
    </script>
</body>

</html>