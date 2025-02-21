<?php

$pageTitle = 'Nutrition - Fitness Tracker';

// In a real application, you would fetch this data from a database
$meal_plans = [
    ['id' => 1, 'name' => 'High Protein Plan', 'calories' => 2500, 'protein' => 200, 'carbs' => 250, 'fat' => 70],
    ['id' => 2, 'name' => 'Low Carb Plan', 'calories' => 2000, 'protein' => 150, 'carbs' => 100, 'fat' => 133],
    ['id' => 3, 'name' => 'Balanced Plan', 'calories' => 2200, 'protein' => 110, 'carbs' => 275, 'fat' => 73],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Nutrition Plans</h1>

        <div class="mb-8">
            <a href="nutrition_create.php"
                class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Create New Meal Plan</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($meal_plans as $plan): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4"><?php echo $plan['name']; ?></h2>
                <div class="mb-4">
                    <p class="text-gray-600">Calories: <?php echo $plan['calories']; ?></p>
                    <p class="text-gray-600">Protein: <?php echo $plan['protein']; ?>g</p>
                    <p class="text-gray-600">Carbs: <?php echo $plan['carbs']; ?>g</p>
                    <p class="text-gray-600">Fat: <?php echo $plan['fat']; ?>g</p>
                </div>
                <canvas id="macroChart<?php echo $plan['id']; ?>" width="200" height="200"></canvas>
                <div class="mt-4 flex justify-between">
                    <a href="nutrition_edit.php?id=<?php echo $plan['id']; ?>"
                        class="text-blue-500 hover:text-blue-700">Edit</a>
                    <a href="nutrition_delete.php?id=<?php echo $plan['id']; ?>" class="text-red-500 hover:text-red-700"
                        onclick="return confirm('Are you sure you want to delete this meal plan?')">Delete</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script>
    <?php foreach ($meal_plans as $plan): ?>
    new Chart(document.getElementById('macroChart<?php echo $plan['id']; ?>'), {
        type: 'pie',
        data: {
            labels: ['Protein', 'Carbs', 'Fat'],
            datasets: [{
                data: [<?php echo $plan['protein']; ?>, <?php echo $plan['carbs']; ?>,
                    <?php echo $plan['fat']; ?>
                ],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
    <?php endforeach; ?>
    </script>
</body>

</html>