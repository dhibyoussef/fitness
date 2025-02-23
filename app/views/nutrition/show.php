<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '../../../app/models/NutritionModel.php';
$nutritionModel = new NutritionModel($pdo);
$nutrition = $nutritionModel->getNutritionById($_SESSION['user_id']);

// app/views/nutrition/show.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">
            <?php echo htmlspecialchars($meal['name']); ?></h1>

        <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <p><strong>Calories:</strong> <?php echo htmlspecialchars($meal['calories']); ?> kcal</p>
            <p><strong>Protein:</strong> <?php echo htmlspecialchars($meal['protein'] ?? 'N/A'); ?> g</p>
            <p><strong>Carbs:</strong> <?php echo htmlspecialchars($meal['carbs'] ?? 'N/A'); ?> g</p>
            <p><strong>Fat:</strong> <?php echo htmlspecialchars($meal['fat'] ?? 'N/A'); ?> g</p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($meal['category'] ?? 'N/A'); ?></p>
            <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime($meal['created_at'])); ?></p>

            <h2 class="text-xl font-semibold mt-4 mb-2">Suggestions</h2>
            <ul>
                <?php foreach ($meal['suggestions'] as $suggestion): ?>
                <li><?php echo htmlspecialchars($suggestion); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="mt-6">
            <a href="/nutrition/index" class="btn btn-primary">Back to List</a>
            <a href="/nutrition/edit/<?php echo $meal['id']; ?>" class="btn btn-warning">Edit</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>