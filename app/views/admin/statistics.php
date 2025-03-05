<?php
$pageTitle = $pageTitle ?? 'Admin Statistics - Fitness Tracker';
$workoutStats = $workoutStats ?? ['total_workouts' => 0, 'avg_duration' => 0, 'total_calories' => 0];
$nutritionStats = $nutritionStats ?? ['avg_calories_per_meal' => 0, 'total_calories' => 0, 'total_meals' => 0];
$categoryTrends = $categoryTrends ?? [];
$registrationLabels = $registrationLabels ?? json_encode([]);
$registrationData = $registrationData ?? json_encode([]);
$activeUserLabels = $activeUserLabels ?? json_encode([]);
$activeUserData = $activeUserData ?? json_encode([]);
$csrf_token = $csrf_token ?? '';
$execution_time = $execution_time ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4a90e2;
            --secondary: #6c757d;
            --background: #f4f6f9;
            --text-color: #333333;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .dark-mode {
            --background: #1a202c;
            --text-color: #e2e8f0;
            --sidebar-bg: #2d3748;
            --card-bg: #2d3748;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background);
            color: var(--text-color);
            margin: 0;
            display: flex;
            overflow-x: hidden;
        }

        .header {
            background: var(--card-bg);
            height: 70px;
            width: 100%;
            box-shadow: var(--shadow);
            position: fixed;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .sidebar {
            background: var(--sidebar-bg);
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 70px;
            left: 0;
            box-shadow: var(--shadow);
            padding: 20px;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--primary);
        }

        .sidebar .nav-link {
            display: block;
            color: var(--text-color);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: var(--primary);
            color: #fff;
        }

        .dashboard-content {
            margin-left: 250px;
            padding: 90px 20px 20px;
            width: calc(100% - 250px);
        }

        .dashboard-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .chart-container, .stats-card {
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 20px;
            width: 100%;
        }

        .chart-container canvas {
            width: 100%;
            max-height: 300px;
        }

        .row .col-md-6 {
            flex: 0 0 48%;
        }

        footer {
            background: #343a40;
            color: #fff;
            padding: 15px;
            text-align: center;
            margin-left: 250px;
            position: fixed;
            bottom: 0;
            width: calc(100% - 250px);
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .dashboard-content {
                margin-left: 0;
                padding: 90px 15px;
            }

            footer {
                margin-left: 0;
                width: 100%;
            }

            .row .col-md-6 {
                flex: 0 0 100%;
            }
        }
    </style>
</head>
<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">

<header class="header">
    <a href="/" class="text-lg font-bold">Fitness Tracker</a>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <button type="submit" name="toggle_theme" value="1" class="btn btn-link text-decoration-none">
            <i class="fas <?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'fa-sun' : 'fa-moon'; ?>"></i>
        </button>
    </form>
</header>

<div class="sidebar">
    <div class="sidebar-header">Admin Panel</div>
    <a href="/admin/dashboard" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="/admin/user_management" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/user_management') !== false ? 'active' : ''; ?>"><i class="fas fa-users"></i> User Management</a>
    <a href="/admin/statistics" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/statistics') !== false ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Statistics</a>
    <a href="/auth/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <p>Track and manage user and platform statistics effectively.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <h2>User Registrations</h2>
                <canvas id="registrationChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h2>Active Users</h2>
                <canvas id="activeUserChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="stats-card">
            <h2>Workout Statistics</h2>
            <p>Total Workouts: <?php echo htmlspecialchars($workoutStats['total_workouts']); ?></p>
            <p>Average Duration: <?php echo number_format($workoutStats['avg_duration'], 1); ?> mins</p>
            <p>Total Calories Burned: <?php echo number_format($workoutStats['total_calories'], 0); ?> kcal</p>
        </div>

        <div class="stats-card">
            <h2>Nutrition Statistics</h2>
            <p>Avg Calories per Meal: <?php echo number_format($nutritionStats['avg_calories_per_meal'], 1); ?> kcal</p>
            <p>Total Meals: <?php echo htmlspecialchars($nutritionStats['total_meals']); ?></p>
        </div>
    </div>
</div>

<footer>
    <p>© <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const registrationCtx = document.getElementById('registrationChart').getContext('2d');
        new Chart(registrationCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $registrationLabels; ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo $registrationData; ?>,
                    backgroundColor: '#4a90e2',
                }]
            },
        });

        const activeUsersCtx = document.getElementById('activeUserChart').getContext('2d');
        new Chart(activeUsersCtx, {
            type: 'line',
            data: {
                labels: <?php echo $activeUserLabels; ?>,
                datasets: [{
                    label: 'Active Users',
                    data: <?php echo $activeUserData; ?>,
                    borderColor: '#28a745',
                    fill: true,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                }]
            },
        });
    });
</script>
</body>
</html>