<?php
$pageTitle = $pageTitle ?? 'Meal Plan Details';
$meal = $meal ?? [];
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
    <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn"><?php echo htmlspecialchars($meal['name'] ?? ''); ?></h1>

    <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
    <?php endif; ?>
    <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
        <p><strong>Calories:</strong> <?php echo htmlspecialchars($meal['calories'] ?? 'N/A'); ?> kcal</p>
        <p><strong>Protein:</strong> <?php echo htmlspecialchars($meal['protein'] ?? 'N/A'); ?> g</p>
        <p><strong>Carbs:</strong> <?php echo htmlspecialchars($meal['carbs'] ?? 'N/A'); ?> g</p>
        <p><strong>Fat:</strong> <?php echo htmlspecialchars($meal['fat'] ?? 'N/A'); ?> g</p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($meal['category'] ?? 'N/A'); ?></p>
        <p><strong>Created:</strong> <?php echo isset($meal['created_at']) ? date('F j, Y', strtotime($meal['created_at'])) : 'N/A'; ?></p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Suggestions</h2>
        <ul class="list-disc pl-5">
            <?php foreach ($meal['suggestions'] ?? [] as $suggestion): ?>
                <li><?php echo htmlspecialchars($suggestion['suggestion'] ?? ''); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="mt-6 flex justify-end gap-2">
        <a href="/nutrition/index" class="btn btn-primary">Back to List</a> <!-- Fixed: Correct route -->
        <a href="/nutrition/edit/<?php echo htmlspecialchars($meal['id'] ?? ''); ?>" class="btn btn-warning">Edit</a>
    </div>

    <p class="text-right text-sm text-gray-600 mt-4">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>