<?php

$pageTitle = 'Workouts - Fitness Tracker';
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
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Workout Plans</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">4-Day Split</h2>
                <p class="text-gray-600 mb-4">A balanced workout plan for efficient training.</p>
                <a href="workout_four_day.php"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">View Plan</a>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">5-Day Split</h2>
                <p class="text-gray-600 mb-4">Intensive plan for maximum muscle growth.</p>
                <a href="workout_five_day.php"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">View Plan</a>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Custom Workout</h2>
                <p class="text-gray-600 mb-4">Create your own personalized workout plan.</p>
                <a href="workout_create.php"
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Create Plan</a>
            </div>
        </div>

        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Custom Workouts</h2>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Workout Name</th>
                            <th class="py-3 px-6 text-left">Created Date</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="font-medium">Full Body Blast</span>
                                </div>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <span>2023-06-15</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <a href="workout_edit.php?id=1"
                                        class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <a href="workout_delete.php?id=1"
                                        class="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Add more rows for other custom workouts -->
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
</body>

</html>