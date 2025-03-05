<?php

global $registrationLabels, $registrationData;

use App\Models\NutritionModel;
use App\Models\ProgressModel;
use App\Models\WorkoutModel;

session_start();

$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
$userId = 3;
require_once __DIR__ . '/../../models/ProgressModel.php';
$progressModel = new ProgressModel($pdo);
try {
    $progress = $progressModel->getProgressById($userId);
} catch (Exception $e) {

}

require_once __DIR__ . '/../../models/WorkoutModel.php';
$workoutModel = new WorkoutModel($pdo);
try {
    $workoutStats = $workoutModel->getoWorkoutStats($userId);
} catch (Exception $e) {

}

require_once __DIR__ . '/../../models/NutritionModel.php';
$nutritionModel = new NutritionModel($pdo);
try {
    $nutritionStats = $nutritionModel->getNutritionStats($userId);
} catch (Exception $e) {

}
$averageCaloriesPerMeal = 40;
$totalMeals = 3;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
    .chart-container {
        max-width: 600px;
        margin: auto;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-center mb-6 animate__animated animate__fadeIn">Your Statistics</h1>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
                    <h2 class="text-xl font-semibold mb-3">Workout Stats</h2>
                    <p>Total Workouts: <?php echo htmlspecialchars($workoutStats['total_workouts']); ?></p>
                    <p>Average Duration: <?php echo number_format($workoutStats['avg_duration'], 1); ?> min</p>
                    <p>Total Calories: <?php echo number_format($workoutStats['total_calories']); ?> kcal</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm p-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <h2 class="text-xl font-semibold mb-3">Nutrition Stats</h2>
                    <p>Average Calories: <?php echo number_format($averageCaloriesPerMeal, 1); ?> kcal
                    </p>
                    <p>Total Meals: <?php echo htmlspecialchars($totalMeals); ?></p>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
                <h2 class="text-xl font-semibold mb-3">Registration Trends</h2>
                <canvas id="registrationChart" class="chart-container"></canvas>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('registrationChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($registrationLabels); ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo json_encode($registrationData); ?>,
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
    });
    </script>
</body>

</html>