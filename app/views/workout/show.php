<?php
$pageTitle = $pageTitle ?? 'Workout Details';
$workout = $workout ?? [];
$exercises = $exercises ?? [];
$linkedExercises = $linkedExercises ?? [];
$csrf_token = $csrf_token ?? '';
$execution_time = $execution_time ?? 0;
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
        <?php echo htmlspecialchars($workout['name'] ?? 'Workout Details'); ?>
    </h1>

    <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
        <p><strong>Description:</strong> <?php echo htmlspecialchars($workout['description'] ?? 'N/A'); ?></p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($workout['formatted_duration'] ?? 'N/A'); ?></p>
        <p><strong>Calories:</strong> <?php echo htmlspecialchars($workout['formatted_calories'] ?? 'N/A'); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($workout['category_name'] ?? 'N/A'); ?></p>
        <p><strong>Created:</strong> <?php echo $workout['created_at'] ? date('F j, Y', strtotime($workout['created_at'])) : 'N/A'; ?></p>

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
                        <?php if (!empty($exercise['video_url'])): ?>
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
        <a href="/workouts/index" class="btn btn-primary animate__animated animate__fadeInUp">Back to List</a>
        <?php if (!($workout['is_predefined'] ?? false)): ?>
            <a href="/workout/edit/<?php echo $workout['id']; ?>" class="btn btn-warning animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">Edit</a>
            <a href="/workout/delete/<?php echo $workout['id']; ?>" class="btn btn-danger animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">Delete</a>
        <?php endif; ?>
    </div>

    <p class="text-right text-sm text-gray-600 mt-4">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>