<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FilFitness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f0f2f5;
    }

    .sidebar {
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 3.5rem;
        background-color: #333;
        color: white;
    }

    .sidebar .nav-link {
        color: white;
        padding: 10px 20px;
        margin-bottom: 5px;
    }

    .sidebar .nav-link:hover {
        background-color: #4CAF50;
    }

    .sidebar .nav-link.active {
        background-color: #4CAF50;
    }

    .main-content {
        margin-left: 250px;
        padding: 20px;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #4CAF50;
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .progress {
        height: 10px;
        border-radius: 5px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FilFitness</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user"></i> John Doe</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="/dashboard">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/workout">
                                <i class="fas fa-dumbbell"></i> Workouts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/nutrition">
                                <i class="fas fa-utensils"></i> Nutrition
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/progress">
                                <i class="fas fa-chart-line"></i> Progress
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/settings">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <span data-feather="calendar"></span>
                            This week
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Workout Progress</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="workoutChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Nutrition Overview</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="nutritionChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Weight Progress</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="weightChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Workouts</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Upper Body Workout
                                        <span class="badge bg-primary rounded-pill">2 days ago</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Lower Body Workout
                                        <span class="badge bg-primary rounded-pill">4 days ago</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Cardio Session
                                        <span class="badge bg-primary rounded-pill">1 week ago</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Goal Progress</h5>
                            </div>
                            <div class="card-body">
                                <h6>Weight Loss Goal</h6>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%;"
                                        aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                                </div>
                                <h6>Muscle Gain Goal</h6>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 60%;"
                                        aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">60%</div>
                                </div>
                                <h6>Cardio Endurance Goal</h6>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 40%;"
                                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">40%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Workout Progress Chart
    var workoutCtx = document.getElementById('workoutChart').getContext('2d');
    var workoutChart = new Chart(workoutCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Workouts Completed',
                data: [3, 5, 4, 6],
                borderColor: '#4CAF50',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Nutrition Overview Chart
    var nutritionCtx = document.getElementById('nutritionChart').getContext('2d');
    var nutritionChart = new Chart(nutritionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Protein', 'Carbs', 'Fat'],
            datasets: [{
                data: [30, 50, 20],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true
        }
    });

    // Weight Progress Chart
    var weightCtx = document.getElementById('weightChart').getContext('2d');
    var weightChart = new Chart(weightCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [{
                label: 'Weight (kg)',
                data: [80, 78, 76, 75, 74],
                borderColor: '#2196F3',
                tension: 0.1
            }]
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