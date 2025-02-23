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
    <title>Workout Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
    .video-container {
        max-width: 560px;
        margin: 20px auto;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">
            <?php echo htmlspecialchars($workout['name']); ?></h1>

        <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <p><strong>Description:</strong> <?php echo htmlspecialchars($workout['description'] ?: 'N/A'); ?></p>
            <p><strong>Duration:</strong> <?php echo htmlspecialchars($workout['formatted_duration']); ?></p>
            <p><strong>Calories:</strong> <?php echo htmlspecialchars($workout['formatted_calories']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($workout['category_name']); ?></p>
            <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime($workout['created_at'])); ?></p>

            <h2 class="text-xl font-semibold mt-4 mb-2">Exercises</h2>
            <?php if (empty($linkedExercises)): ?>
            <p>No exercises assigned.</p>
            <?php else: ?>
            <ul>
                <?php foreach ($linkedExercises as $exercise): ?>
                <li>
                    <?php echo htmlspecialchars($exercise['name']); ?>:
                    <?php echo htmlspecialchars($exercise['sets']); ?> sets,
                    <?php echo htmlspecialchars($exercise['reps']); ?> reps
                    <?php if ($exercise['video_url']): ?>
                    <div class="video-container">
                        <iframe width="100%" height="315" src="<?php echo htmlspecialchars($exercise['video_url']); ?>"
                            frameborder="0" allowfullscreen></iframe>
                    </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <div class="mt-6">
            <a href="/workout/index" class="btn btn-primary animate__animated animate__fadeInUp">Back to List</a>
            <?php if (!$workout['is_predefined']): ?>
            <a href="/workout/edit/<?php echo $workout['id']; ?>"
                class="btn btn-warning animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">Edit</a>
            <a href="/workout/delete/<?php echo $workout['id']; ?>"
                class="btn btn-danger animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">Delete</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>