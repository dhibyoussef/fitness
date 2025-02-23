<?php
session_start();
// app/views/admin/dashboard.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/models/AdminModel.php';
require_once __DIR__ . '/../../app/models/UserModel.php';
require_once __DIR__ . '/../../app/models/WorkoutModel.php';
require_once __DIR__ . '/../../app/models/NutritionModel.php';
require_once __DIR__ . '/../../app/models/ProgressModel.php';

if (!isset($_SESSION['admin'])) {
    header('Location: /admin/login');
    exit;
}

$adminModel = new AdminModel($pdo);
$userModel = new UserModel($pdo);
$workoutModel = new WorkoutModel($pdo);
$nutritionModel = new NutritionModel($pdo);
$progressModel = new ProgressModel($pdo);

$totalUsers = $userModel->getUserCount();
$activeUsers = $userModel->getActiveUserStatistics();
$workoutStats = $workoutModel->getOverallWorkoutStatistics();
$nutritionData = $nutritionModel->getOverallNutritionStatistics(); // Assuming this method exists or is correctly named in the actual model
$registrationTrends = $adminModel->getRegistrationTrends();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }

    .card {
        transition: transform 0.3s;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .execution-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-center mb-6 animate__animated animate__fadeIn">Admin Dashboard</h1>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm animate__animated animate__zoomIn">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text text-4xl font-semibold"><?php echo htmlspecialchars($totalUsers); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.2s;">
                    <div class="card-body text-center">
                        <h5 class="card-title">Active Users</h5>
                        <p class="card-text text-4xl font-semibold">
                            <?php echo htmlspecialchars(json_encode($activeUsers)); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.4s;">
                    <div class="card-body text-center">
                        <h5 the card-title">Workouts Logged</h5>
                        <p class="card-text text-4xl font-semibold">
                            <?php echo htmlspecialchars($workoutStats['total_workouts']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.6s;">
                    <div class="card-body text-center">
                        <h5 class="card-title">Real-Time Users</h5>
                        <p class="card-text text-4xl font-semibold"><?php echo htmlspecialchars($realTimeUsers); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Nutrition Overview</h2>
            <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
                <p>Average Calories per Meal: <?php echo number_format($nutritionData['avg_calories_per_meal'], 1); ?>
                    kcal</p>
                <p>Total Calories Logged: <?php echo number_format($nutritionData['total_calories']); ?> kcal</p>
                <p>Total Meals: <?php echo htmlspecialchars($nutritionData['total_meals']); ?></p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Registration Trends</h2>
            <canvas id="registrationChart" class="animate__animated animate__fadeInUp"
                style="max-height: 300px;"></canvas>
        </div>

        <p class="execution-time mt-4 text-center">Page loaded in <?php echo number_format($execution_time, 4); ?>
            seconds</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('registrationChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($registrationTrends, 'month')); ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo json_encode(array_column($registrationTrends, 'registrations')); ?>,
                    borderColor: '#007bff',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        gsap.from('.card', {
            opacity: 0,
            y: 50,
            stagger: 0.2,
            duration: 1
        });
    });
    </script>
</body>

</html>