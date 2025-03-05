<?php
$pageTitle = $pageTitle ?? 'Edit Progress';
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
    <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn"><?php echo htmlspecialchars($pageTitle); ?></h1>

    <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
    <?php endif; ?>
    <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="/progress/update/<?php echo htmlspecialchars($progress['id'] ?? ''); ?>" class="card shadow-sm p-4 animate__animated animate__fadeInUp">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <div class="mb-3">
            <label for="weight" class="form-label">Weight (kg)</label>
            <input type="number" name="weight" id="weight" class="form-control" value="<?php echo htmlspecialchars($progress['weight'] ?? ''); ?>" step="0.1" required>
        </div>

        <div class="mb-3">
            <label for="body_fat" class="form-label">Body Fat (%)</label>
            <input type="number" name="body_fat" id="body_fat" class="form-control" value="<?php echo htmlspecialchars($progress['body_fat'] ?? ''); ?>" step="0.1" required>
        </div>

        <div class="mb-3">
            <label for="muscle_mass" class="form-label">Muscle Mass (kg)</label>
            <input type="number" name="muscle_mass" id="muscle_mass" class="form-control" value="<?php echo htmlspecialchars($progress['muscle_mass'] ?? ''); ?>" step="0.1">
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="<?php echo htmlspecialchars($progress['date'] ?? ''); ?>" required>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="/progress/index" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Progress</button>
        </div>
    </form>

    <p class="text-right text-sm text-gray-600 mt-4">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>