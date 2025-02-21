<?php


// In a real application, you would fetch the workout data from the database
// For this example, we'll use mock data
$workout = [
    'id' => 1,
    'name' => 'Full Body Blast',
    'exercises' => [
        [
            'name' => 'Squats',
            'sets' => 3,
            'reps' => 12,
            'rest' => 60
        ],
        [
            'name' => 'Push-ups',
            'sets' => 3,
            'reps' => 15,
            'rest' => 45
        ]
    ]
];

$pageTitle = 'Edit Workout - Fitness Tracker';
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
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Edit Workout</h1>

        <form action="workout_update.php" method="POST" class="bg-white rounded-lg shadow-md p-6">
            <input type="hidden" name="workout_id" value="<?php echo $workout['id']; ?>">
            <div class="mb-6">
                <label for="workout_name" class="block text-gray-700 text-sm font-bold mb-2">Workout Name:</label>
                <input type="text" id="workout_name" name="workout_name" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="<?php echo htmlspecialchars($workout['name']); ?>">
            </div>

            <div id="exercises" class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Exercises</h2>
                <?php foreach ($workout['exercises'] as $index => $exercise): ?>
                <div class="exercise-entry mb-4 p-4 border rounded">
                    <div class="mb-4">
                        <label for="exercise_name_<?php echo $index + 1; ?>"
                            class="block text-gray-700 text-sm font-bold mb-2">Exercise Name:</label>
                        <input type="text" id="exercise_name_<?php echo $index + 1; ?>"
                            name="exercises[<?php echo $index + 1; ?>][name]" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value="<?php echo htmlspecialchars($exercise['name']); ?>">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="exercise_sets_<?php echo $index + 1; ?>"
                                class="block text-gray-700 text-sm font-bold mb-2">Sets:</label>
                            <input type="number" id="exercise_sets_<?php echo $index + 1; ?>"
                                name="exercises[<?php echo $index + 1; ?>][sets]" required min="1"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="<?php echo $exercise['sets']; ?>">
                        </div>
                        <div>
                            <label for="exercise_reps_<?php echo $index + 1; ?>"
                                class="block text-gray-700 text-sm font-bold mb-2">Reps:</label>
                            <input type="number" id="exercise_reps_<?php echo $index + 1; ?>"
                                name="exercises[<?php echo $index + 1; ?>][reps]" required min="1"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="<?php echo $exercise['reps']; ?>">
                        </div>
                        <div>
                            <label for="exercise_rest_<?php echo $index + 1; ?>"
                                class="block text-gray-700 text-sm font-bold mb-2">Rest (seconds):</label>
                            <input type="number" id="exercise_rest_<?php echo $index + 1; ?>"
                                name="exercises[<?php echo $index + 1; ?>][rest]" required min="0"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="<?php echo $exercise['rest']; ?>">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="add_exercise"
                class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 mb-6">Add Exercise</button>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Update
                    Workout</button>
                <a href="workout_index.php" class="text-gray-600 hover:text-blue-500">Cancel</a>
            </div>
        </form>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script>
    let exerciseCount = <?php echo count($workout['exercises']); ?>;

    document.getElementById('add_exercise').addEventListener('click', function() {
        exerciseCount++;
        const exerciseHtml = `
                <div class="exercise-entry mb-4 p-4 border rounded">
                    <div class="mb-4">
                        <label for="exercise_name_${exerciseCount}" class="block text-gray-700 text-sm font-bold mb-2">Exercise Name:</label>
                        <input type="text" id="exercise_name_${exerciseCount}" name="exercises[${exerciseCount}][name]" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="exercise_sets_${exerciseCount}" class="block text-gray-700 text-sm font-bold mb-2">Sets:</label>
                            <input type="number" id="exercise_sets_${exerciseCount}" name="exercises[${exerciseCount}][sets]" required min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label for="exercise_reps_${exerciseCount}" class="block text-gray-700 text-sm font-bold mb-2">Reps:</label>
                            <input type="number" id="exercise_reps_${exerciseCount}" name="exercises[${exerciseCount}][reps]" required min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label for="exercise_rest_${exerciseCount}" class="block text-gray-700 text-sm font-bold mb-2">Rest (seconds):</label>
                            <input type="number" id="exercise_rest_${exerciseCount}" name="exercises[${exerciseCount}][rest]" required min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                </div>
            `;
        document.getElementById('exercises').insertAdjacentHTML('beforeend', exerciseHtml);
    });
    </script>
</body>

</html>