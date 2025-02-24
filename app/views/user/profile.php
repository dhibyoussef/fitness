<?php
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/WorkoutModel.php';
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
$displayname = 'John Doe';
$userId = 3;
$workoutModel = new WorkoutModel($pdo);
$progress = $workoutModel->getOverallWorkoutStatistics();
$avg_weight = 70;
$avg_body_fat = 10;
$avg_muscle_mass = 10;

$userModel = new UserModel($pdo);
$user = $userModel->getUserById($userId);
$joined = date('F j, Y H:i');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Profile:
            <?php echo htmlspecialchars($displayname); ?></h1>

        <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>Joined:</strong> <?php echo htmlspecialchars($joined); ?></p>
            <p><strong>Last Activity:</strong>
                <?php echo $user['last_activity'] ? date('F j, Y H:i', strtotime($user['last_activity'])) : 'N/A'; ?>
            </p>

            <h2 class="text-xl font-semibold mt-4 mb-2">Progress Stats</h2>
            <p>Average Weight: <?php echo number_format($avg_weight, 1); ?> kg</p>
            <p>Average Body Fat: <?php echo number_format($avg_body_fat, 1); ?>%</p>
            <p>Average Muscle Mass: <?php echo number_format($avg_muscle_mass, 1); ?> kg</p>
        </div>

        <div class="mt-6">
            <a href="/user/edit/<?php echo $user['id']; ?>"
                class="btn btn-warning animate__animated animate__fadeInUp">Edit Profile</a>
            <a href="/user/delete/<?php echo $user['id']; ?>" class="btn btn-danger animate__animated animate__fadeInUp"
                style="animation-delay: 0.2s;">Delete Account</a>
            <a href="/dashboard" class="btn btn-primary animate__animated animate__fadeInUp"
                style="animation-delay: 0.4s;">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>