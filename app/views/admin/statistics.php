<?php

$pageTitle = 'Admin Statistics - Fitness Tracker';
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
                    <a href="user_management.php" class="text-gray-600 hover:text-blue-500">User Management</a>
                    <a href="statistics.php" class="text-blue-500 font-semibold">Statistics</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Application Statistics Overview</h1>
            <div class="relative">
                <select id="timePeriod"
                    class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-blue-500">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">User Growth Trends</h2>
                <div class="h-64">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">User Activity Distribution</h2>
                <div class="h-64">
                    <canvas id="activityDistributionChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Workout Program Popularity</h2>
                <div class="h-64">
                    <canvas id="workoutPopularityChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Nutrition Plan Adoption</h2>
                <div class="h-64">
                    <canvas id="nutritionPlanUsageChart"></canvas>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Key Metrics</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 border-l-4 border-blue-500">
                    <p class="text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold">1,250</p>
                </div>
                <div class="p-4 border-l-4 border-green-500">
                    <p class="text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold">850</p>
                </div>
                <div class="p-4 border-l-4 border-purple-500">
                    <p class="text-gray-600">Workouts Logged</p>
                    <p class="text-2xl font-bold">4,200</p>
                </div>
                <div class="p-4 border-l-4 border-yellow-500">
                    <p class="text-gray-600">Nutrition Plans</p>
                    <p class="text-2xl font-bold">1,150</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const timePeriodSelect = document.getElementById('timePeriod');
        const charts = {};

        const initCharts = () => {
            charts.userGrowth = new Chart(document.getElementById('userGrowthChart'), {
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
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            charts.activityDistribution = new Chart(document.getElementById('activityDistributionChart'), {
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
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            charts.workoutPopularity = new Chart(document.getElementById('workoutPopularityChart'), {
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

            charts.nutritionPlanUsage = new Chart(document.getElementById('nutritionPlanUsageChart'), {
                type: 'doughnut',
                data: {
                    labels: ['High Protein', 'Low Carb', 'Balanced', 'Vegan', 'Keto'],
                    datasets: [{
                        data: [30, 25, 20, 15, 10],
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                            '#8B5CF6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        };

        const updateCharts = (timePeriod) => {
            // Here you would typically fetch new data from the server
            // and update the charts accordingly
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
            })
        };

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
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                        '#8B5CF6'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
    </script>
</body>

</html>