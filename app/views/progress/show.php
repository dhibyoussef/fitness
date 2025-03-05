<?php
$pageTitle = $pageTitle ?? 'Progress Details';
$progress = $progress ?? [];
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
</head>
<body class="bg-gray-100">
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Progress on <?php echo htmlspecialchars($progress['date'] ?? ''); ?></h1>

    <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
    <?php endif; ?>
    <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
        <p><strong>Weight:</strong> <?php echo htmlspecialchars($progress['weight'] ?? 'N/A'); ?> kg</p>
        <p><strong>Body Fat:</strong> <?php echo htmlspecialchars($progress['body_fat'] ?? 'N/A'); ?>%</p>
        <p><strong>Muscle Mass:</strong> <?php echo htmlspecialchars($progress['muscle_mass'] ?? 'N/A'); ?> kg</p>
        <p><strong>Logged:</strong> <?php echo isset($progress['created_at']) ? date('F j, Y', strtotime($progress['created_at'])) : 'N/A'; ?></p>
    </div>

    <div class="mt-6">
        <a href="/progress/index" class="btn btn-primary animate__animated animate__fadeInUp">Back to List</a>
        <a href="/progress/edit/<?php echo htmlspecialchars($progress['id'] ?? ''); ?>" class="btn btn-warning animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">Edit</a>
        <a href="/progress/delete/<?php echo htmlspecialchars($progress['id'] ?? ''); ?>" class="btn btn-danger animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">Delete</a>
    </div>

    <p class="text-right text-sm text-gray-600 mt-4">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>