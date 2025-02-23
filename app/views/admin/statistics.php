<?php
// app/views/admin/statistics.php
session_start();
require_once __DIR__ . '/../../app/models/WorkoutModel.php';
require_once __DIR__ . '/../../app/models/NutritionModel.php';
if (!isset($_SESSION['admin'])) {
    header('Location: /admin/login');
    exit;
}

$workoutModel = new WorkoutModel($pdo);
$nutritionModel = new NutritionModel($pdo);

$workoutStats = $workoutModel->getOverallWorkoutStatistics();
$nutritionStats = $nutritionModel->getOverallNutritionStatistics();
$categoryTrends = $workoutModel->getAllCategories();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .chart-container {
        max-width: 600px;
        margin: auto;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-center mb-6 animate__animated animate__fadeIn">Statistics Overview</h1>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
                    <h2 class="text-xl font-semibold mb-3">User Registrations</h2>
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm p-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <h2 class="text-xl font-semibold mb-3">Active Users</h2>
                    <canvas id="activeUserChart"></canvas>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Workout Statistics</h2>
            <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
                <p>Total Workouts: <?php echo htmlspecialchars($workoutStats['total_workouts']); ?></p>
                <p>Average Duration: <?php echo number_format($workoutStats['avg_duration'], 1); ?> min</p>
                <p>Total Calories Burned: <?php echo number_format($workoutStats['total_calories']); ?> kcal</p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Nutrition Statistics</h2>
            <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
                <p>Average Calories per Meal: <?php echo number_format($nutritionStats['avg_calories_per_meal'], 1); ?>
                    kcal</p>
                <p>Total Calories: <?php echo number_format($nutritionStats['total_calories']); ?> kcal</p>
                <p>Total Meals: <?php echo htmlspecialchars($nutritionStats['total_meals']); ?></p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Category Trends</h2>
            <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
                <ul>
                    <?php foreach ($categoryTrends as $trend): ?>
                    <li><?php echo htmlspecialchars($trend['name']); ?>: <?php echo $trend['count']; ?> workouts</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const regCtx = document.getElementById('registrationChart').getContext('2d');
        new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($registrationLabels); ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo json_encode($registrationData); ?>,
                    backgroundColor: '#007bff'
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

        const activeCtx = document.getElementById('activeUserChart').getContext('2d');
        new Chart(activeCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($activeUserLabels); ?>,
                datasets: [{
                    label: 'Active Users',
                    data: <?php echo json_encode($activeUserData); ?>,
                    borderColor: '#28a745',
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
    });
    </script>
</body>

</html>