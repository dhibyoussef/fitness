<?php
session_start();

require_once __DIR__ . '/../../models/ExerciseModel.php';
$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
$userId = 3;
$exerciseId = 4;

// Calculate one-rep max (1RM)
$one_rm = 100;
$load = [
    ['percent' => 80, 'weight' => 100],
    ['percent' => 90, 'weight' => 110],
    ['percent' => 100, 'weight' => 120]
];
$warmup = [
    ['set' => 1, 'weight' => 50, 'reps' => 10, 'rest' => 60],
    ['set' => 2, 'weight' => 60, 'reps' => 8, 'rest' => 60]
];
$tdee = 2000;


$exerciseModel = new ExerciseModel($pdo);
$exercise = $exerciseModel->getExerciseById(4, $userId);
$macros = ['protein' => 100, 'carbs' => 100, 'fat' => 100];


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
        </h1>

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
            <p>BMR: <?php echo $tdee; ?> kcal</p>
            <p>TDEE: <?php echo $tdee; ?> kcal</p>
            <p>BMI: <?php echo $tdee; ?></p>
            <p>Cut: <?php echo $tdee; ?> kcal</p>
            <p>Maintain: <?php echo $tdee; ?> kcal</p>
            <p>Bulk: <?php echo $tdee; ?> kcal</p>
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