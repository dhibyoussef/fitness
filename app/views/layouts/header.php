<?php
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php'; // Add this line
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/controllers/BaseController.php';

$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', ''); // Update with your DB credentials
$baseController = new BaseController($pdo);
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'en'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="/assets/js/app.js"></script>
    <style>
    body {
        background: linear-gradient(135deg, #1a202c, #2d3748);
        color: white;
        font-family: 'Arial', sans-serif;
    }

    .glass {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
    }

    .hover-lift {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
    }

    .three-container {
        height: 300px;
        width: 100%;
    }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <header class="bg-gray-900 p-4 shadow-lg">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-white hover:text-green-400">Fitness Tracker</a>
            <div class="flex items-center space-x-4">
                <?php if ($baseController->isAuthenticated()): ?>
                <a href="/user/profile" class="text-white hover:text-green-400">Profile</a>
                <a href="/fitness/index" class="text-white hover:text-green-400">Fitness</a>

                <?php if ($baseController->isAdmin()): ?>
                <a href="/admin/dashboard" class="text-white hover:text-green-400">Admin</a>
                <?php endif; ?>
                <form action="/auth/logout" method="POST" class="inline">
                    <input type="hidden" name="csrf_token" value="<?php echo $baseController->generateCsrfToken(); ?>">
                    <button type="submit" class="text-white hover:text-red-400">Logout</button>
                </form>
                <?php else: ?>
                <a href="/auth/login" class="text-white hover:text-green-400">Login</a>
                <a href="/auth/signup" class="text-white hover:text-green-400">Signup</a>
                <?php endif; ?>
                <!-- Language Switcher -->
                <form action="/language/change" method="POST" class="inline">
                    <input type="hidden" name="csrf_token" value="<?php echo $baseController->generateCsrfToken(); ?>">
                    <select name="lang" onchange="this.form.submit()" class="bg-gray-800 text-white p-1 rounded">
                        <option value="en" <?php echo ($_SESSION['lang'] ?? 'en') === 'en' ? 'selected' : ''; ?>>English
                        </option>
                        <option value="fr" <?php echo ($_SESSION['lang'] ?? 'en') === 'fr' ? 'selected' : ''; ?>>
                            Français</option>
                        <option value="ar" <?php echo ($_SESSION['lang'] ?? 'en') === 'ar' ? 'selected' : ''; ?>>العربية
                        </option>
                        <option value="es" <?php echo ($_SESSION['lang'] ?? 'en') === 'es' ? 'selected' : ''; ?>>Español
                        </option>
                    </select>
                </form>
            </div>
        </nav>
    </header>
    <main class="flex-grow container mx-auto p-6">
        <!-- Flash Messages -->
        <?php if ($msg = $baseController->getFlashMessage('success')): ?>
        <div class="bg-green-500 p-4 rounded mb-6 glass"><?php echo htmlspecialchars($msg); ?></div>
        <?php elseif ($msg = $baseController->getFlashMessage('error')): ?>
        <div class="bg-red-500 p-4 rounded mb-6 glass"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>