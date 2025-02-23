<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '../../../app/models/ExerciseModel.php';

$exerciseModel = new ExerciseModel($pdo);
$exercises = $exerciseModel->getUserExercises($_SESSION['user_id']);



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tools</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Fitness Tools</h1>

        <form method="POST" action="/fitness/calculate" class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="mb-3">
                <label for="exercise_id" class="form-label">Exercise</label>
                <select name="exercise_id" id="exercise_id" class="form-select">
                    <?php foreach ($exercises as $exercise): ?>
                    <option value="<?php echo $exercise['id']; ?>"><?php echo htmlspecialchars($exercise['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="weight" class="form-label">Weight Lifted (kg)</label>
                <input type="number" name="weight" id="weight" class="form-control" step="0.1" required>
            </div>

            <div class="mb-3">
                <label for="reps" class="form-label">Reps</label>
                <input type="number" name="reps" id="reps" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select name="gender" id="gender" class="form-select">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" name="age" id="age" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="height" class="form-label">Height (cm)</label>
                <input type="number" name="height" id="height" class="form-control" step="0.1" required>
            </div>

            <div class="mb-3">
                <label for="body_fat" class="form-label">Body Fat % (optional)</label>
                <input type="number" name="body_fat" id="body_fat" class="form-control" step="0.1">
            </div>

            <div class="mb-3">
                <label for="activity" class="form-label">Activity Level</label>
                <select name="activity" id="activity" class="form-select">
                    <option value="sedentary">Sedentary</option>
                    <option value="light">Light</option>
                    <option value="moderate">Moderate</option>
                    <option value="very">Very Active</option>
                    <option value="extra">Extra Active</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="goal" class="form-label">Goal</label>
                <select name="goal" id="goal" class="form-select">
                    <option value="cut">Cut</option>
                    <option value="maintain">Maintain</option>
                    <option value="bulk">Bulk</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Calculate</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>