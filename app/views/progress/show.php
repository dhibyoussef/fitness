<?php

$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
$userId = 3;
require_once __DIR__ . '/../../models/ProgressModel.php';
$progressModel = new ProgressModel($pdo);
$progress = $progressModel->getProgressById($userId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Progress on
            <?php echo htmlspecialchars($progress['date']); ?></h1>

        <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <p><strong>Weight:</strong> <?php echo htmlspecialchars($progress['weight']); ?> kg</p>
            <p><strong>Body Fat:</strong> <?php echo htmlspecialchars($progress['body_fat']); ?>%</p>
            <p><strong>Muscle Mass:</strong> <?php echo htmlspecialchars($progress['muscle_mass'] ?? 'N/A'); ?> kg</p>
            <p><strong>Logged:</strong> <?php echo date('F j, Y', strtotime($progress['created_at'])); ?></p>
        </div>

        <div class="mt-6">
            <a href="/progress/index" class="btn btn-primary animate__animated animate__fadeInUp">Back to List</a>
            <a href="/progress/edit/<?php echo $progress['id']; ?>"
                class="btn btn-warning animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">Edit</a>
            <a href="/progress/delete/<?php echo $progress['id']; ?>"
                class="btn btn-danger animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">Delete</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>