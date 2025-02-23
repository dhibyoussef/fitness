<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// C:\xampp\htdocs\fitness-app\app\views\workout\four-day.php
$pageTitle = '4-Day Workout Split - Fitness Tracker';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f0f2f5, #e9ecef);
        min-height: 100vh;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">4-Day Workout Split</h1>

        <div class="card shadow-sm p-6 animate__animated animate__fadeInUp">
            <h2 class="text-2xl font-semibold mb-4">Overview</h2>
            <p>This is a 4-day split balancing upper and lower body:</p>
            <ul class="list-group mb-4">
                <li class="list-group-item">Day 1: Upper Body Push (Bench Press, Overhead Press - 3 sets, 10-12 reps)
                </li>
                <li class="list-group-item">Day 2: Lower Body (Squats, Lunges - 4 sets, 10-12 reps)</li>
                <li class="list-group-item">Day 3: Upper Body Pull (Pull-Ups, Rows - 3 sets, 8-10 reps)</li>
                <li class="list-group-item">Day 4: Full Body (Deadlifts, Push-Ups - 3 sets, 10-12 reps)</li>
            </ul>
            <p><strong>Duration:</strong> Approx. 60 minutes per session</p>
            <p><strong>Calories Burned:</strong> Approx. 350-500 kcal per session</p>
        </div>

        <div class="mt-6">
            <a href="/workout/index" class="btn btn-primary">Back to Workouts</a>
            <a href="/workout/create" class="btn btn-outline-primary">Customize This Plan</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>