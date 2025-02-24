<?php
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../app/models/WorkoutModel.php';
require_once __DIR__ . '/../../../app/models/NutritionModel.php';
require_once __DIR__ . '/../../../app/models/UserModel.php';
require_once __DIR__ . '/../../../app/controllers/BaseController.php';

// Handle theme toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_theme'])) {
    $_SESSION['dark_mode'] = !($_SESSION['dark_mode'] ?? false);
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $workoutModel = new WorkoutModel($pdo);
    $nutritionModel = new NutritionModel($pdo);
    $userModel = new UserModel($pdo);
    $baseController = new BaseController($pdo);

    $workoutStats = $workoutModel->getOverallWorkoutStatistics() ?? ['total_workouts' => 0, 'avg_duration' => 0, 'total_calories' => 0];
    $nutritionStats = $nutritionModel->getOverallNutritionStatistics() ?? ['avg_calories_per_meal' => 0, 'total_calories' => 0, 'total_meals' => 0];
    $categoryTrends = $workoutModel->getAllCategories() ?? [];
    $registrationTrends = $userModel->getRegistrationStatistics() ?? [];
    $activeUserStats = $userModel->getActiveUserStatistics() ?? [];

    $registrationLabels = json_encode(array_keys($registrationTrends));
    $registrationData = json_encode(array_values($registrationTrends));
    $activeUserLabels = json_encode(array_keys($activeUserStats));
    $activeUserData = json_encode(array_values($activeUserStats));

    $csrf_token = $baseController->generateCsrfToken();
    $start_time = microtime(true);
} catch (Exception $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Statistics - Fitness Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f0f2f5;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .header {
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
    }

    .sidebar {
        position: fixed;
        top: 70px;
        left: 0;
        width: 250px;
        height: calc(100vh - 70px);
        background: #ffffff;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        overflow-y: auto;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .sidebar-logo {
        font-size: 1.5rem;
        margin-right: 10px;
    }

    .nav-link {
        padding: 10px;
        color: #4a90e2;
        display: block;
        transition: background 0.3s, color 0.3s;
    }

    .nav-link.active,
    .nav-link:hover {
        background: #4a90e2;
        color: white;
        border-radius: 8px;
    }

    .dashboard-content {
        margin-left: 270px;
        padding: 90px 20px 20px;
        overflow-y: auto;
        height: 100vh;
    }

    .dashboard-header {
        background: #4a90e2;
        color: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .stats-card,
    .chart-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 20px;
    }

    .chart-container canvas {
        max-height: 300px;
    }

    .execution-time {
        font-size: 0.9rem;
        color: #6c757d;
        text-align: right;
    }

    .dark-mode {
        background: #1a202c;
        color: #e2e8f0;
    }

    .dark-mode .header,
    .dark-mode .sidebar,
    .dark-mode .stats-card,
    .dark-mode .chart-container {
        background: #2d3748;
    }

    .dark-mode .dashboard-header {
        background: #2c5282;
    }

    .logout-btn {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.3s;
    }

    .logout-btn:hover {
        color: #c82333;
    }

    .dark-mode .logout-btn {
        color: #ff6b6b;
    }

    .dark-mode .logout-btn:hover {
        color: #ff4040;
    }
    </style>
</head>

<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">
    <header class="header">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-gray-800 dark:text-white">Fitness Tracker</a>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/user/profile"
                        class="text-gray-600 hover:text-blue-500 dark:text-gray-300 dark:hover:text-blue-400 flex items-center">
                        <i
                            class="fas fa-user mr-2"></i><?php echo htmlspecialchars($_SESSION['username'] ?? 'Profile'); ?>
                    </a>
                    <form method="POST" action="/auth/logout">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i></button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="toggle_theme" value="1">
                        <button type="submit"
                            class="text-gray-600 hover:text-blue-500 dark:text-gray-300 dark:hover:text-blue-400">
                            <i
                                class="fas <?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'fa-sun' : 'fa-moon'; ?>"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-dumbbell sidebar-logo"></i>
            <h4>Admin Panel</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="/admin/dashboard"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-tachometer-alt mr-2"></i> Dashboard</a></li>
            <li class="nav-item"><a href="/admin/users"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-users mr-2"></i> User Management</a></li>
            <li class="nav-item"><a href="/statistics"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/statistics') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-chart-line mr-2"></i> Statistics</a></li>
            <li class="nav-item"><a href="/admin/settings"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-cog mr-2"></i> Settings</a></li>
            <li class="nav-item"><a href="/admin/notifications"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/notifications') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-bell mr-2"></i> Notifications</a></li>
            <li class="nav-item"><a href="/admin/logout" class="nav-link"><i class="fas fa-sign-out-alt mr-2"></i>
                    Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1 class="text-3xl font-bold text-center">Statistics Overview</h1>
            <p class="text-center">Monitor fitness tracking platform statistics</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h2 class="text-lg font-semibold mb-3">User Registrations</h2>
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h2 class="text-lg font-semibold mb-3">Active Users</h2>
                    <canvas id="activeUserChart"></canvas>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <h2 class="text-lg font-semibold mb-3">Workout Statistics</h2>
            <p>Total Workouts: <?php echo htmlspecialchars($workoutStats['total_workouts']); ?></p>
            <p>Average Duration: <?php echo number_format($workoutStats['avg_duration'], 1); ?> min</p>
            <p>Total Calories Burned: <?php echo number_format($workoutStats['total_calories'], 0); ?> kcal</p>
        </div>

        <div class="stats-card">
            <h2 class="text-lg font-semibold mb-3">Nutrition Statistics</h2>
            <p>Average Calories per Meal: <?php echo number_format($nutritionStats['avg_calories_per_meal'], 1); ?> kcal
            </p>
            <p>Total Calories: <?php echo number_format($nutritionStats['total_calories'], 0); ?> kcal</p>
            <p>Total Meals: <?php echo htmlspecialchars($nutritionStats['total_meals']); ?></p>
        </div>

        <div class="stats-card">
            <h2 class="text-lg font-semibold mb-3">Category Trends</h2>
            <ul class="list-disc pl-5">
                <?php foreach ($categoryTrends as $trend): ?>
                <li><?php echo htmlspecialchars($trend['name'] ?? 'Unknown'); ?>:
                    <?php echo htmlspecialchars($trend['count'] ?? 0); ?> workouts</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php $execution_time = microtime(true) - $start_time; ?>
        <p class="execution-time">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
    </div>

    <footer class="bg-gray-800 text-white py-4 dark:bg-gray-900">
        <div class="container mx-auto text-center">
            <p>© <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const regCtx = document.getElementById('registrationChart').getContext('2d');
        new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $registrationLabels; ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo $registrationData; ?>,
                    backgroundColor: '#4a90e2',
                    borderColor: '#4a90e2',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Registrations'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });

        const activeCtx = document.getElementById('activeUserChart').getContext('2d');
        new Chart(activeCtx, {
            type: 'line',
            data: {
                labels: <?php echo $activeUserLabels; ?>,
                datasets: [{
                    label: 'Active Users',
                    data: <?php echo $activeUserData; ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Active Users'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time Period'
                        }
                    }
                }
            }
        });
    });
    </script>
</body>

</html>