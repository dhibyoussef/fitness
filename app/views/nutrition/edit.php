<?php
// app/views/nutrition/edit.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '../../../app/models/NutritionModel.php';

$nutritionModel = new NutritionModel($pdo);
$nutrition = $nutritionModel->getNutritionById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meal Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Edit Meal Plan</h1>

        <?php if ($error = isset($_SESSION['flash_messages']['error']) ? $_SESSION['flash_messages']['error'] : null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="/nutrition/update/<?php echo $nutrition['id']; ?>"
            class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Meal Name</label>
                <input type="text" name="name" id="name" class="form-control"
                    value="<?php echo htmlspecialchars($nutrition['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="calories" class="form-label">Calories</label>
                <input type="number" name="calories" id="calories" class="form-control"
                    value="<?php echo htmlspecialchars($nutrition['calories']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="protein" class="form-label">Protein (g)</label>
                <input type="number" name="protein" id="protein" class="form-control"
                    value="<?php echo htmlspecialchars($nutrition['protein']); ?>" step="0.1">
            </div>

            <div class="mb-3">
                <label for="carbs" class="form-label">Carbs (g)</label>
                <input type="number" name="carbs" id="carbs" class="form-control"
                    value="<?php echo htmlspecialchars($nutrition['carbs']); ?>" step="0.1">
            </div>

            <div class="mb-3">
                <label for="fat" class="form-label">Fat (g)</label>
                <input type="number" name="fat" id="fat" class="form-control"
                    value="<?php echo htmlspecialchars($nutrition['fat']); ?>" step="0.1">
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"
                        <?php echo $category['id'] == $nutrition['category_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Meal</button>
            <a href="/nutrition/index" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>