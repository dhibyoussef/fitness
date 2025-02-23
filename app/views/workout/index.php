<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '../../../app/models/WorkoutModel.php';
$workoutModel = new WorkoutModel($pdo);
$workout = $workoutModel->getWorkoutById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workouts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Workouts</h1>

        <?php if ($success = isset($_SESSION['flash_messages']['success']) ? $_SESSION['flash_messages']['success'] : null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        <?php if ($error = isset($_SESSION['flash_messages']['error']) ? $_SESSION['flash_messages']['error'] : null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mb-4 flex space-x-4">
            <a href="/workout/create" class="btn btn-primary animate__animated animate__fadeInUp">Add New Workout</a>
            <a href="?show_predefined=<?php echo $showPredefined ? '0' : '1'; ?>"
                class="btn btn-outline-secondary animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <?php echo $showPredefined ? 'Hide Predefined' : 'Show Predefined'; ?>
            </a>
        </div>

        <div class="card shadow-sm p-4 mb-4 animate__animated animate__fadeInUp">
            <h2 class="text-xl font-semibold mb-3">Workout Stats</h2>
            <p>Total Duration: <?php echo number_format($stats['total_duration']); ?> min</p>
            <p>Average Duration: <?php echo number_format($stats['avg_duration'], 1); ?> min</p>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Duration (min)</th>
                    <th>Calories</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workouts as $workout): ?>
                <tr class="animate__animated animate__fadeIn"
                    style="animation-delay: <?php echo $loopIndex * 0.1; ?>s;">
                    <td><?php echo htmlspecialchars($workout['name']); ?></td>
                    <td><?php echo htmlspecialchars($workout['duration']); ?></td>
                    <td><?php echo htmlspecialchars($workout['calories'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($workout['category_name'] ?? 'N/A'); ?></td>
                    <td>
                        <a href="/workout/show/<?php echo $workout['id']; ?>" class="btn btn-sm btn-info">View</a>
                        <?php if (!$workout['is_predefined']): ?>
                        <a href="/workout/edit/<?php echo $workout['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="/workout/delete/<?php echo $workout['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?>&filter=<?php echo urlencode($filter); ?>&show_predefined=<?php echo $showPredefined ? '1' : '0'; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>