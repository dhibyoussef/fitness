<?php
$pageTitle = $pageTitle ?? 'User Dashboard';
$username = $_SESSION['username'] ?? 'User';
$totalWorkouts = $totalWorkouts ?? 0; // Integer
$progressPercentage = $progressPercentage ?? 0.0; // Float
$nutritionGoals = $nutritionGoals ?? [['name' => 'Protein Intake', 'progress' => 70], ['name' => 'Calories', 'progress' => 50]];
$recentWorkouts = $recentWorkouts ?? [['name' => 'Cardio', 'date' => '2023-10-01'], ['name' => 'Weightlifting', 'date' => '2023-10-03']];
$monthlyProgress = $monthlyProgress ?? array_fill(0, 12, ['week' => date('Y-m'), 'progress' => 0]);
$csrf_token = $csrf_token ?? '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Include Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef2f3, #ffffff);
            color: #2d3748;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #4a5568, #2d3748);
            z-index: 1000;
            padding: 20px;
        }
        .sidebar .nav-link {
            color: #e2e8f0;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left: 5px solid #f56565;
        }
        .dashboard-content {
            margin-left: 260px;
            padding: 80px 20px;
        }
        .stats-card, .nutrition-card, .workouts-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
        }
        .stats-card:hover, .nutrition-card:hover, .workouts-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        .circular-progress {
            width: 100px;
            height: 100px;
            position: relative;
            border-radius: 50%;
            background: conic-gradient(
            #3182ce <?php echo $progressPercentage; ?>%,
            #eaeaea 0
            );
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }
        .circular-progress span {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
        }
        .bar-progress {
            width: 100%;
            height: 20px;
            background: #eaeaea;
            border-radius: 10px;
            overflow: hidden;
        }
        .bar-progress span {
            height: 100%;
            display: block;
            background: #3182ce;
        }
        .workout-item {
            background: #f6f8fa;
            border-radius: 10px;
            padding: 10px 20px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar animate__animated animate__fadeInLeft">
    <h4 class="text-white mb-4 text-center">Dashboard</h4>
    <nav class="nav flex-column">
        <a href="/" class="nav-link"><i class="fas fa-home me-2"></i> Home</a>
        <a href="/user/profile" class="nav-link"><i class="fas fa-user me-2"></i> Profile</a>
        <a href="/progress/index" class="nav-link"><i class="fas fa-chart-bar me-2"></i> Progress</a>
        <a href="/nutrition/index" class="nav-link"><i class="fas fa-apple-alt me-2"></i> Nutrition</a>
        <a href="/workouts/index" class="nav-link"><i class="fas fa-dumbbell me-2"></i> Workouts</a>
        <a href="/auth/logout" class="nav-link"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </nav>
</div>

<!-- Main Content -->
<div class="dashboard-content">
    <div class="container">
        <!-- Welcome Header -->
        <h1 class="mb-5 text-4xl font-bold">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-dumbbell text-primary fa-3x mb-3"></i>
                    <h5>Total Workouts</h5>
                    <p class="fs-4 mb-0 text-primary font-bold">
                        <?php echo htmlspecialchars($totalWorkouts); ?>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="circular-progress">
                        <span><?php echo htmlspecialchars(number_format($progressPercentage, 2)); ?>%</span>
                    </div>
                    <h5 class="mt-3">Progress Achieved</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="nutrition-card">
                    <h5 class="mb-3">Nutrition Goals</h5>
                    <?php foreach ($nutritionGoals as $goal): ?>
                        <strong><?php echo htmlspecialchars($goal['name']); ?>:</strong>
                        <div class="bar-progress mb-2">
                            <span style="width: <?php echo $goal['progress']; ?>%"></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Workouts Section -->
        <div class="mt-5">
            <h5>Recent Workouts</h5>
            <div class="workouts">
                <?php foreach ($recentWorkouts as $workout): ?>
                    <div class="workout-item">
                        <i class="fas fa-running me-2 text-info"></i>
                        <strong><?php echo htmlspecialchars($workout['name']); ?></strong> (<?php echo htmlspecialchars($workout['date']); ?>)
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Monthly Progress Chart -->
        <div class="mt-5">
            <h5>Monthly Progress</h5>
            <div class="card p-3 shadow rounded">
                <canvas id="progressChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($monthlyProgress, 'week')); ?>,
            datasets: [{
                label: 'Progress (%)',
                data: <?php echo json_encode(array_column($monthlyProgress, 'progress')); ?>,
                borderColor: '#3182ce',
                backgroundColor: 'rgba(49, 130, 206, 0.3)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true },
                x: { title: { display: true, text: 'Month' } }
            }
        }
    });
</script>
</body>
</html>
