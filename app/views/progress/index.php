<?php

$pageTitle = 'Progress Tracking - Fitness Tracker';

// In a real application, you would fetch this data from a database
$progress_data = [
    ['date' => '2023-01-01', 'weight' => 80, 'body_fat' => 20, 'muscle_mass' => 60],
    ['date' => '2023-02-01', 'weight' => 78, 'body_fat' => 19, 'muscle_mass' => 61],
    ['date' => '2023-03-01', 'weight' => 76, 'body_fat' => 18, 'muscle_mass' => 62],
    ['date' => '2023-04-01', 'weight' => 75, 'body_fat' => 17, 'muscle_mass' => 63],
    ['date' => '2023-05-01', 'weight' => 74, 'body_fat' => 16, 'muscle_mass' => 64],
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
                    <a href="nutrition_index.php" class="text-gray-600 hover:text-blue-500">Nutrition</a>
                    <a href="progress_index.php" class="text-blue-500 font-semibold">Progress</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Progress Tracking</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Enter New Progress Data</h2>
                <form action="progress_save.php" method="POST">
                    <div class="mb-4">
                        <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date:</label>
                        <input type="date" id="date" name="date" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="weight" class="block text-gray-700 text-sm font-bold mb-2">Weight (kg):</label>
                        <input type="number" id="weight" name="weight" step="0.1" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="body_fat" class="block text-gray-700 text-sm font-bold mb-2">Body Fat (%):</label>
                        <input type="number" id="body_fat" name="body_fat" step="0.1" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="muscle_mass" class="block text-gray-700 text-sm font-bold mb-2">Muscle Mass
                            (kg):</label>
                        <input type="number" id="muscle_mass" name="muscle_mass" step="0.1" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save Progress
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Progress Chart</h2>
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Progress History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Date</th>
                            <th class="py-3 px-6 text-left">Weight (kg)</th>
                            <th class="py-3 px-6 text-left">Body Fat (%)</th>
                            <th class="py-3 px-6 text-left">Muscle Mass (kg)</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php foreach ($progress_data as $entry): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap"><?php echo $entry['date']; ?></td>
                            <td class="py-3 px-6 text-left"><?php echo $entry['weight']; ?></td>
                            <td class="py-3 px-6 text-left"><?php echo $entry['body_fat']; ?></td>
                            <td class="py-3 px-6 text-left"><?php echo $entry['muscle_mass']; ?></td>
                            <td class="py-3 px-6 text-center">
                                <a href="progress_edit.php?date=<?php echo $entry['date']; ?>"
                                    class="text-blue-500 hover:text-blue-700 mr-4">Edit</a>
                                <a href="progress_delete.php?date=<?php echo $entry['date']; ?>"
                                    class="text-red-500 hover:text-red-700"
                                    onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($progress_data, 'date')); ?>,
            datasets: [{
                    label: 'Weight (kg)',
                    data: <?php echo json_encode(array_column($progress_data, 'weight')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                {
                    label: 'Body Fat (%)',
                    data: <?php echo json_encode(array_column($progress_data, 'body_fat')); ?>,
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                },
                {
                    label: 'Muscle Mass (kg)',
                    data: <?php echo json_encode(array_column($progress_data, 'muscle_mass')); ?>,
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
    </script>
</body>

</html>