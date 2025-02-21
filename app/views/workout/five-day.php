<?php

$pageTitle = '5-Day Workout Plan - Fitness Tracker';

// In a real application, you would fetch this data from a database
$workout_plan = [
    'Monday' => [
        ['name' => 'Bench Press', 'sets' => 4, 'reps' => '8-10'],
        ['name' => 'Incline Dumbbell Press', 'sets' => 3, 'reps' => '10-12'],
        ['name' => 'Chest Flyes', 'sets' => 3, 'reps' => '12-15'],
        ['name' => 'Tricep Pushdowns', 'sets' => 3, 'reps' => '12-15'],
        ['name' => 'Overhead Tricep Extensions', 'sets' => 3, 'reps' => '12-15']
    ],
    'Tuesday' => [
        ['name' => 'Deadlifts', 'sets' => 4, 'reps' => '6-8'],
        ['name' => 'Pull-ups', 'sets' => 3, 'reps' => '8-10'],
        ['name' => 'Barbell Rows', 'sets' => 3, 'reps' => '8-10'],
        ['name' => 'Face Pulls', 'sets' => 3, 'reps' => '12-15'],
        ['name' => 'Hammer Curls', 'sets' => 3, 'reps' => '12-15']
    ],
    'Wednesday' => [
        ['name' => 'Squats', 'sets' => 4, 'reps' => '8-10'],
        ['name' => 'Leg Press', 'sets' => 3, 'reps' => '10-12'],
        ['name' => 'Leg Extensions', 'sets' => 3, 'reps' => '12-15'],
        ['name' => 'Leg Curls', 'sets' => 3, 'reps' => '12-15'],
        ['name' => 'Calf Raises', 'sets' => 4, 'reps' => '15-20']
    ],
    'Thursday' => [
        ['name' => 'Overhead Press', 'sets' => 4, 'reps' => '8-10'],
        ['name' => 'Lateral Raises', 'sets' => 3, 'reps' => '12-15'],
        ['name' => 'Front Raises', 'sets' => 3, 'reps' => '12-15'],
        ['name' => 'Upright Rows', 'sets' => 3, 'reps' => '10-12'],
        ['name' => 'Shrugs', 'sets' => 3, 'reps' => '12-15']
    ],
    'Friday' => [
        ['name' => 'Barbell Curls', 'sets' => 3, 'reps' => '8-10'],
        ['name' => 'Preacher Curls', 'sets' => 3, 'reps' => '10-12'],
        ['name' => 'Skull Crushers', 'sets' => 3, 'reps' => '10-12'],
        ['name' => 'Dips', 'sets' => 3, 'reps' => '10-12'],
        ['name' => 'Cable Curls', 'sets' => 3, 'reps' => '12-15']
    ]
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
</head>

<body class="bg-gray-100 font-sans">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold text-gray-800">Fitness Tracker</a>
                <div class="space-x-4">
                    <a href="dashboard.php" class="text-gray-600 hover:text-blue-500">Dashboard</a>
                    <a href="workout_index.php" class="text-blue-500 font-semibold">Workouts</a>
                    <a href="nutrition_index.php" class="text-gray-600 hover:text-blue-500">Nutrition</a>
                    <a href="progress_index.php" class="text-gray-600 hover:text-blue-500">Progress</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">5-Day Workout Plan</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($workout_plan as $day => $exercises): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4"><?php echo $day; ?></h2>
                <ul class="space-y-2">
                    <?php foreach ($exercises as $exercise): ?>
                    <li class="flex justify-between items-center">
                        <span class="text-gray-700"><?php echo $exercise['name']; ?></span>
                        <span class="text-gray-500"><?php echo $exercise['sets']; ?> x
                            <?php echo $exercise['reps']; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>