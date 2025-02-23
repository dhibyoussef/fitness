<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '../../../app/models/NutritionModel.php';
$nutritionModel = new NutritionModel($pdo);
$nutrition = $nutritionModel->getNutritionById($_SESSION['user_id']);
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// app/controllers/NutritionController.php
// app/views/nutrition/index.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Plans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Nutrition Plans</h1>

        <?php if ($success = isset($_SESSION['flash_messages']['success']) ? $_SESSION['flash_messages']['success'] : null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        <?php if ($error = isset($_SESSION['flash_messages']['error']) ? $_SESSION['flash_messages']['error'] : null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mb-4">
            <a href="/nutrition/create" class="btn btn-primary animate__animated animate__fadeInUp">Add New Meal</a>
        </div>

        <div class="card shadow-sm p-4 mb-4 animate__animated animate__fadeInUp">
            <h2 class="text-xl font-semibold mb-3">Nutrition Stats</h2>
            <p>Total Calories: <?php echo number_format($stats['total_calories']); ?> kcal</p>
            <p>Average Calories: <?php echo number_format($stats['avg_calories'], 1); ?> kcal</p>
            <p>Total Protein: <?php echo number_format($stats['total_protein'], 1); ?> g</p>
            <p>Total Carbs: <?php echo number_format($stats['total_carbs'], 1); ?> g</p>
            <p>Total Fat: <?php echo number_format($stats['total_fat'], 1); ?> g</p>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Calories</th>
                    <th>Protein (g)</th>
                    <th>Carbs (g)</th>
                    <th>Fat (g)</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meals as $meal): ?>
                <tr class="animate__animated animate__fadeIn"
                    style="animation-delay: <?php echo $loopIndex * 0.1; ?>s;">
                    <td><?php echo htmlspecialchars($meal['name']); ?></td>
                    <td><?php echo htmlspecialchars($meal['calories']); ?></td>
                    <td><?php echo htmlspecialchars($meal['protein'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($meal['carbs'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($meal['fat'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($meal['category_name'] ?? 'N/A'); ?></td>
                    <td>
                        <a href="/nutrition/show/<?php echo $meal['id']; ?>" class="btn btn-sm btn-info">View</a>
                        <a href="/nutrition/edit/<?php echo $meal['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="/nutrition/delete/<?php echo $meal['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
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
                        href="?page=<?php echo $i; ?>&filter=<?php echo urlencode($filter); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>