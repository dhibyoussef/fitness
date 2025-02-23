<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '../../../app/models/ExerciseModel.php';

$exerciseModel = new ExerciseModel($pdo);
$exercise = $exerciseModel->getExerciseById($_POST['exercise_id'], $_SESSION['user_id'] );

// app/views/fitness/results.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Fitness Results for
            <?php echo htmlspecialchars($exercise['name']); ?></h1>

        <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <h2 class="text-2xl font-semibold mb-3">1RM Estimate</h2>
            <p><?php echo htmlspecialchars($one_rm); ?> kg</p>

            <h2 class="text-2xl font-semibold mb-3 mt-4">Load Percentages</h2>
            <ul>
                <?php foreach ($load as $entry): ?>
                <li><?php echo $entry['percent']; ?>%: <?php echo $entry['weight']; ?> kg</li>
                <?php endforeach; ?>
            </ul>

            <h2 class="text-2xl font-semibold mb-3 mt-4">Warmup Sets</h2>
            <ul>
                <?php foreach ($warmup as $set): ?>
                <li>Set <?php echo $set['set']; ?>: <?php echo $set['weight']; ?> kg x <?php echo $set['reps']; ?>
                    (Rest: <?php echo $set['rest']; ?>)</li>
                <?php endforeach; ?>
            </ul>

            <h2 class="text-2xl font-semibold mb-3 mt-4">TDEE & Macros</h2>
            <p>BMR: <?php echo $tdee['bmr']; ?> kcal</p>
            <p>TDEE: <?php echo $tdee['tdee']; ?> kcal</p>
            <p>BMI: <?php echo $tdee['bmi']; ?></p>
            <p>Cut: <?php echo $tdee['cut']; ?> kcal</p>
            <p>Maintain: <?php echo $tdee['maintain']; ?> kcal</p>
            <p>Bulk: <?php echo $tdee['bulk']; ?> kcal</p>
            <p>Protein: <?php echo $macros['protein']; ?> g</p>
            <p>Carbs: <?php echo $macros['carbs']; ?> g</p>
            <p>Fat: <?php echo $macros['fat']; ?> g</p>
        </div>

        <div class="mt-6">
            <a href="/fitness/index" class="btn btn-primary">Back to Tools</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>