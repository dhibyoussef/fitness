<?php
// C:\xampp\htdocs\fitness-app\app\views\admin\dashboard.php

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\NutritionModel;
use App\Models\ProgressModel;
use App\Models\UserModel;
use App\Models\WorkoutModel;


require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../app/models/AdminModel.php';
require_once __DIR__ . '/../../../app/models/UserModel.php';
require_once __DIR__ . '/../../../app/models/WorkoutModel.php';
require_once __DIR__ . '/../../../app/models/NutritionModel.php';
require_once __DIR__ . '/../../../app/models/ProgressModel.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', ''); // Replace with your actual credentials
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $adminModel = new AdminModel($pdo);
    $userModel = new UserModel($pdo);
    $workoutModel = new WorkoutModel($pdo);
    $nutritionModel = new NutritionModel($pdo);
    $progressModel = new ProgressModel($pdo);

    $totalUsers = $userModel->getUserCount();
    $activeUsers = count($userModel->getActiveUserStatistics());
    $realTimeUsers = $userModel->getRealTimeUsers();
    $workoutStats = $workoutModel->getOverallWorkoutStatistics();
    $nutritionData = $nutritionModel->getOverallNutritionStatistics();
    $registrationTrends = $userModel->getRegistrationStatistics();

    // Ensure progress data is structured correctly
    $averageProgress = $progressModel->getOverallProgressStatistics();
    if (!isset($averageProgress['avg_weight'], $averageProgress['avg_body_fat'], $averageProgress['avg_muscle_mass'])) {
        $averageProgress = [
            'avg_weight' => $progressModel->getAverageProgress('weight'),
            'avg_body_fat' => $progressModel->getAverageProgress('body_fat'),
            'avg_muscle_mass' => $progressModel->getAverageProgress('muscle_mass')
        ];
    }

    // Start timing execution
    $start_time = microtime(true);
    $execution_time = microtime(true) - $start_time; // Calculate execution time

    // Generate CSRF token (assuming BaseController is included or adapted)
    require_once __DIR__ . '/../../../app/controllers/BaseController.php';
    $baseController = new BaseController($pdo);
    $csrf_token = $baseController->generateCsrfToken();
} catch (PDOException $e) {
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
} catch (Exception $e) {
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f0f2f5, #e9ecef);
        min-height: 100vh;
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }

    .header {
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 2000;
        transition: background 0.3s ease;
    }

    .dark-mode .header {
        background: #1a202c;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .sidebar {
        height: 100%;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        background: linear-gradient(135deg, #2d3748, #1a202c);
        padding-top: 60px;
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        overflow-y: auto;
    }

    .sidebar .nav-link {
        color: #e2e8f0;
        padding: 15px 20px;
        font-size: 1.1rem;
        transition: 0.3s ease, color 0.3s ease;
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar .nav-link:hover {
        color: #63b3ed;
        background: rgba(255, 255, 255, 0.05);
        padding-left: 25px;
    }

    .sidebar .nav-item.active .nav-link {
        background: #4a90e2;
        color: white;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 2px solid #4a90e2;
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-logo {
        font-size: 2.5rem;
        color: #4a90e2;
        margin-right: 15px;
        transition: color 0.3s ease;
    }

    .sidebar-header h4 {
        color: white;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
    }

    .dashboard-content {
        margin-left: 250px;
        padding: 80px 20px 20px;
        /* Increased top padding to account for fixed header */
        transition: margin-left 0.3s ease;
    }

    .dashboard-header {
        background: linear-gradient(45deg, #4a90e2, #50c878);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        animation: pulse 2s infinite ease-in-out;
        margin-top: 20px;
    }

    .stats-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .stats-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        filter: brightness(110%);
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
        z-index: 0;
        pointer-events: none;
    }

    .stats-card .card-body {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        padding: 1.8rem;
        position: relative;
        z-index: 1;
    }

    .stats-icon {
        font-size: 3rem;
        color: #4a90e2;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .stats-card:hover .stats-icon {
        color: #63b3ed;
        transform: scale(1.15);
    }

    .chart-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        max-height: 450px;
        overflow: hidden;
        position: relative;
    }

    .chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
        z-index: 0;
        pointer-events: none;
    }

    .execution-time {
        font-size: 1rem;
        color: #6c757d;
        text-align: right;
        margin-top: 1.5rem;
        animation: fadeIn 1.5s ease-in;
        font-weight: 500;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .dark-mode {
        background: linear-gradient(135deg, #1a202c, #2d3748);
        color: #e2e8f0;
    }

    .dark-mode .header {
        background: #1a202c;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .dark-mode .dashboard-header {
        background: linear-gradient(45deg, #2c5282, #276749);
        color: #e2e8f0;
    }

    .dark-mode .sidebar {
        background: linear-gradient(135deg, #1a202c, #2d3748);
    }

    .dark-mode .sidebar .nav-link {
        color: #e2e8f0;
    }

    .dark-mode .sidebar .nav-link:hover {
        color: #63b3ed;
        background: rgba(255, 255, 255, 0.05);
    }

    .dark-mode .sidebar .nav-item.active .nav-link {
        background: #2c5282;
        color: white;
    }

    .dark-mode .sidebar-header {
        border-bottom: 2px solid #63b3ed;
        background: rgba(255, 255, 255, 0.05);
    }

    .dark-mode .sidebar-logo {
        color: #63b3ed;
    }

    .dark-mode .stats-card,
    .dark-mode .chart-container {
        background: #2d3748;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .dark-mode .stats-card .card-body {
        background: linear-gradient(135deg, #2d3748, #4a5568);
    }

    .dark-mode .stats-icon {
        color: #63b3ed;
    }

    .dark-mode .execution-time {
        color: #a0aec0;
    }

    .glitch-effect {
        position: relative;
        animation: glitch 4s linear infinite;
    }

    @keyframes glitch {

        2%,
        64% {
            transform: translate(1px, 0) skew(0deg);
            filter: hue-rotate(5deg);
        }

        4%,
        60% {
            transform: translate(-1px, 0) skew(0deg);
            filter: hue-rotate(-5deg);
        }

        62% {
            transform: translate(0, 0) skew(2deg);
            filter: hue-rotate(10deg);
        }
    }

    .stats-card.glitch-effect::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: repeating-linear-gradient(45deg, rgba(255, 0, 0, 0.05), rgba(0, 255, 0, 0.05) 2px, rgba(0, 0, 255, 0.05) 4px);
        z-index: 2;
        pointer-events: none;
        opacity: 0;
        animation: glitchBlink 5s linear infinite;
    }

    @keyframes glitchBlink {

        0%,
        20%,
        40%,
        60%,
        80%,
        100% {
            opacity: 0;
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            opacity: 0.2;
        }
    }

    .logout-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        z-index: 1000;
        background: transparent;
        border: none;
        padding: 10px;
        color: #dc3545;
        cursor: pointer;
        transition: color 0.3s ease, transform 0.3s ease;
        font-size: 1.5rem;
    }

    .logout-btn:hover {
        color: #c82333;
        transform: scale(1.1);
    }

    .logout-btn i {
        transition: color 0.3s ease;
    }

    .dark-mode .logout-btn {
        color: #ff6b6b;
    }

    .dark-mode .logout-btn:hover {
        color: #ff4040;
    }
    </style>
</head>

<body
    class="bg-gray-100 flex flex-col <?php echo (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : ''; ?>">
    <!-- Header (Fixed and Sticky) -->
    <header class="header">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-gray-800 dark-mode:text-white">Fitness Tracker</a>
                <div class="flex items-center space-x-4">

                    <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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

    <!-- Sidebar (Static, Visible by Default) -->
    <div class="sidebar">
        <div class="sidebar-header glitch-effect">
            <i class="fas fa-dumbbell sidebar-logo"></i>
            <h4>Admin Panel</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/admin/dashboard"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/user_management"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users mr-2"></i> User Management
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/statistics"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/statistics') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line mr-2"></i> Statistics
                </a>
            </li>

            <li class="nav-item">
                <a href="/auth/logout" class="nav-link">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="dashboard-header animate__animated animate__bounceIn glitch-effect">
            <h1 class="text-4xl font-bold text-center mb-4 glitch-effect">Admin Dashboard</h1>
            <p class="text-center text-lg text-white">Manage and monitor your fitness tracking platform effectively</p>
        </div>

        <div class="row g-4 mt-6">
            <div class="col-md-3">
                <div class="stats-card glitch-effect animate__animated animate__flipInX">
                    <div class="card-body text-center">
                        <i class="fas fa-users stats-icon mb-3"></i>
                        <h5 class="card-title text-xl font-semibold">Total Users</h5>
                        <p class="card-text text-4xl font-bold text-blue-500">
                            <?php echo htmlspecialchars($totalUsers); ?></p>
                        <p class="text-sm text-gray-600 dark-mode:text-gray-400">All registered users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card glitch-effect animate__animated animate__flipInX" style="animation-delay: 0.2s;">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check stats-icon mb-3"></i>
                        <h5 class="card-title text-xl font-semibold">Active Users</h5>
                        <p class="card-text text-4xl font-bold text-green-500">
                            <?php echo htmlspecialchars($activeUsers); ?></p>
                        <p class="text-sm text-gray-600 dark-mode:text-gray-400">Active in last 24 hours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card glitch-effect animate__animated animate__flipInX" style="animation-delay: 0.4s;">
                    <div class="card-body text-center">
                        <i class="fas fa-dumbbell stats-icon mb-3"></i>
                        <h5 class="card-title text-xl font-semibold">Workouts Logged</h5>
                        <p class="card-text text-4xl font-bold text-purple-500">
                            <?php echo htmlspecialchars($workoutStats['total_workouts']); ?></p>
                        <p class="text-sm text-gray-600 dark-mode:text-gray-400">Total workout sessions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card glitch-effect animate__animated animate__flipInX" style="animation-delay: 0.6s;">
                    <div class="card-body text-center">
                        <i class="fas fa-clock stats-icon mb-3"></i>
                        <h5 class="card-title text-xl font-semibold">Real-Time Users</h5>
                        <p class="card-text text-4xl font-bold text-orange-500">
                            <?php echo htmlspecialchars($realTimeUsers); ?></p>
                        <p class="text-sm text-gray-600 dark-mode:text-gray-400">Active in last 5 minutes</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2
                class="text-2xl font-bold mb-4 text-gray-800 dark-mode:text-white animate__animated animate__fadeInLeft glitch-effect">
                Nutrition Insights</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Average Calories per Meal</h5>
                            <p class="card-text text-4xl font-bold text-green-600">
                                <?php echo number_format($nutritionData['avg_calories_per_meal'], 1); ?> kcal</p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Per meal logged</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn"
                        style="animation-delay: 0.2s;">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Total Calories Logged</h5>
                            <p class="card-text text-4xl font-bold text-red-600">
                                <?php echo number_format($nutritionData['total_calories'], 0); ?> kcal</p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Across all meals</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn"
                        style="animation-delay: 0.4s;">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Total Meals</h5>
                            <p class="card-text text-4xl font-bold text-blue-600">
                                <?php echo htmlspecialchars($nutritionData['total_meals']); ?></p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Meals tracked</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2
                class="text-2xl font-bold mb-4 text-gray-800 dark-mode:text-white animate__animated animate__fadeInRight glitch-effect">
                Workout Performance</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Average Workout Duration</h5>
                            <p class="card-text text-4xl font-bold text-purple-600">
                                <?php echo number_format($workoutStats['avg_duration'], 1); ?> min</p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Per session</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn"
                        style="animation-delay: 0.2s;">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Total Calories Burned</h5>
                            <p class="card-text text-4xl font-bold text-orange-600">
                                <?php echo number_format($workoutStats['total_calories'] ?? 0, 0); ?> kcal</p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Across all workouts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2
                class="text-2xl font-bold mb-4 text-gray-800 dark-mode:text-white animate__animated animate__fadeInLeft glitch-effect">
                Registration Trends</h2>
            <div class="chart-container animate__animated animate__fadeInUp">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>

        <div class="mt-8">
            <h2
                class="text-2xl font-bold mb-4 text-gray-800 dark-mode:text-white animate__animated animate__fadeInRight glitch-effect">
                Progress Overview</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Average Weight</h5>
                            <p class="card-text text-4xl font-bold text-blue-600">
                                <?php echo number_format((float) $averageProgress['avg_weight'], 1); ?> kg</p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Across users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn"
                        style="animation-delay: 0.2s;">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Average Body Fat</h5>
                            <p class="card-text text-4xl font-bold text-red-600">
                                <?php echo number_format((float) $averageProgress['avg_body_fat'], 1); ?> %</p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Across users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card glitch-effect animate__animated animate__zoomIn"
                        style="animation-delay: 0.4s;">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-semibold">Average Muscle Mass</h5>
                            <p class="card-text text-4xl font-bold text-green-600">
                                <?php echo number_format($averageProgress['avg_muscle_mass'] ?? 0.0, 1); ?> kg</p>
                            <p class="text-sm text-gray-600 dark-mode:text-gray-400">Across users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="execution-time mt-4 text-right">Page loaded in <?php echo number_format($execution_time, 4); ?>
            seconds</p>
    </div>

    <footer class="bg-gray-800 text-white py-4 mt-auto dark-mode:bg-gray-900">
        <div class="container mx-auto text-center">
            <p class="text-lg font-medium">© <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Registration Trends Chart
        const ctx = document.getElementById('registrationChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($registrationTrends)); ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo json_encode(array_values($registrationTrends)); ?>,
                    borderColor: '#4a90e2',
                    backgroundColor: 'rgba(74, 144, 226, 0.3)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointBackgroundColor: '#4a90e2',
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 16,
                                weight: 'bold',
                                family: 'Poppins'
                            },
                            color: '#6c757d'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#4a90e2',
                        borderWidth: 1,
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Registrations',
                            color: '#6c757d',
                            font: {
                                size: 16,
                                weight: 'bold',
                                family: 'Poppins'
                            }
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 14,
                                family: 'Poppins'
                            }
                        },
                        grid: {
                            color: 'rgba(108, 117, 125, 0.15)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month',
                            color: '#6c757d',
                            font: {
                                size: 16,
                                weight: 'bold',
                                family: 'Poppins'
                            }
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 14,
                                family: 'Poppins'
                            },
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            color: 'rgba(108, 117, 125, 0.15)'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    intersect: false
                }
            }
        });

        // GSAP and ScrollTrigger animations
        gsap.registerPlugin(ScrollTrigger);

        gsap.from('.stats-card, .dashboard-header', {
            opacity: 0,
            y: 50,
            stagger: 0.3,
            duration: 1.5,
            ease: 'power3.out'
        });

        gsap.to('.stats-card', {
            scrollTrigger: {
                trigger: '.stats-card',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            scale: 1.05,
            duration: 0.6,
            ease: 'power2.inOut',
            repeat: -1,
            yoyo: true
        });

        // Glitch effect on hover for cards (static glitch, no movement)
        const cards = document.querySelectorAll('.stats-card, .dashboard-header, .sidebar-header');
        cards.forEach(card => {
            card.addEventListener('mouseover', () => {
                card.classList.add('glitch-effect');
            });
            card.addEventListener('mouseout', () => {
                card.classList.remove('glitch-effect');
            });
        });
    });
    </script>
</body>

</html>