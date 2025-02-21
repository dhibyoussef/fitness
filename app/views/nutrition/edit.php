<?php


// In a real application, you would fetch the meal plan data from the database
// For this example, we'll use mock data
$meal_plan = [
    'id' => 1,
    'name' => 'High Protein Plan',
    'calories' => 2500,
    'protein' => 200,
    'carbs' => 250,
    'fat' => 70
];

$pageTitle = 'Edit Meal Plan - Fitness Tracker';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold text-gray-800">Fitness Tracker</a>
                <div class="space-x-4">
                    <a href="dashboard.php" class="text-gray-600 hover:text-blue-500">Dashboard</a>
                    <a href="workout_index.php" class="text-gray-600 hover:text-blue-500">Workouts</a>
                    <a href="nutrition_index.php" class="text-blue-500 font-semibold">Nutrition</a>
                    <a href="progress_index.php" class="text-gray-600 hover:text-blue-500">Progress</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">


        <!DOCTYPE html>
        <html lang=" en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $pageTitle; ?></title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
                rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>

        <body class="bg-gray-100 font-sans">
            <header class="bg-white shadow-md">
                <nav class="container mx-auto px-6 py-3">
                    <div class="flex justify-between items-center">
                        <a href="index.php" class="text-2xl font-bold text-gray-800">Fitness Tracker</a>
                        <div class="space-x-4">
                            <a href="dashboard.php" class="text-gray-600 hover:text-blue-500">Dashboard</a>
                            <a href="user_management.php" class="text-gray-600 hover:text-blue-500">User Management</a>
                            <a href="statistics.php" class="text-blue-500 font-semibold">Statistics</a>
                            <a href="logout.php"
                                class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Logout</a>
                        </div>
                    </div>
                </nav>
            </header>

            <main class="container mx-auto px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">App Statistics</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">User Growth</h2>
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Activity Distribution</h2>
                        <canvas id="activityDistributionChart"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Workout Popularity</h2>
                        <canvas id="workoutPopularityChart"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Nutrition Plan Usage</h2>
                        <canvas id="nutritionPlanUsageChart"></canvas>
                    </div>
                </div>
            </main>

            <footer class="bg-gray-800 text-white py-4 mt-8">
                <div class="container mx-auto px-6 text-center">
                    <p>&copy; 2025 Fitness Tracker. All rights reserved.</p>
                </div>
            </footer>

            <script>
            // User Growth Chart
            new Chart(document.getElementById('userGrowthChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'New Users',
                        data: [50, 80, 120, 160, 200, 250],
                        borderColor: 'rgb(59, 130, 246)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            // Activity Distribution Chart
            new Chart(document.getElementById('activityDistributionChart'), {
                type: 'pie',
                data: {
                    labels: ['Workouts', 'Nutrition Tracking', 'Progress Tracking'],
                    datasets: [{
                        data: [45, 30, 25],
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            // Workout Popularity Chart
            new Chart(document.getElementById('workoutPopularityChart'), {
                type: 'bar',
                data: {
                    labels: ['Strength Training', 'Cardio', 'Yoga', 'HIIT', 'Pilates'],
                    datasets: [{
                        label: 'Popularity',
                        data: [300, 250, 200, 180, 150],
                        backgroundColor: '#3B82F6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Nutrition Plan Usage Chart
            new Chart(document.getElementById('nutritionPlanUsageChart'), {
                type: 'doughnut',
                data: {
                    labels: ['High Protein', 'Low Carb', 'Balanced', 'Vegan', 'Keto'],
                    datasets: [{
                        data: [30, 25, 20, 15, 10],
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
            </script>
        </body>

        </html>