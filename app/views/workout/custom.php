<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}


// C:\xampp\htdocs\fitness-app\app\views\workout\custom.php

$pageTitle = 'Create Custom Workout - Fitness Tracker';
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f0f2f5, #e9ecef);
        min-height: 100vh;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Create Custom Workout</h1>

        <?php if ($error = isset($_SESSION['flash_messages']['error']) ? $_SESSION['flash_messages']['error'] : null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="/workout/createCustomWorkout"
            class="card shadow-sm p-6 animate__animated animate__fadeInUp">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="mb-4">
                <label for="name" class="form-label">Workout Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="e.g., My Custom Routine">
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-4">
                <label for="duration" class="form-label">Duration (minutes)</label>
                <input type="number" name="duration" id="duration" class="form-control" required>
            </div>

            <div class="mb-4">
                <label for="calories" class="form-label">Calories Burned (optional)</label>
                <input type="number" name="calories" id="calories" class="form-control">
            </div>

            <div class="mb-4" id="exercise-container">
                <h2 class="text-xl font-semibold mb-2">Exercises</h2>
                <div class="exercise-row mb-2">
                    <select name="exercises[0][id]" class="form-select mb-1">
                        <option value="">Select an exercise</option>
                        <?php foreach ($exercises ?? [] as $exercise): ?>
                        <option value="<?php echo $exercise['id']; ?>">
                            <?php echo htmlspecialchars($exercise['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="exercises[0][sets]" placeholder="Sets" class="form-control mb-1" min="1">
                    <input type="text" name="exercises[0][reps]" placeholder="Reps (e.g., 10 or 8-12)"
                        class="form-control">
                </div>
                <button type="button" id="add-exercise" class="btn btn-outline-secondary">Add Another Exercise</button>
            </div>

            <button type="submit" class="btn btn-primary">Create Workout</button>
            <a href="/workout/index" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let exerciseCount = 1;
    document.getElementById('add-exercise').addEventListener('click', () => {
        const container = document.getElementById('exercise-container');
        const newRow = document.createElement('div');
        newRow.className = 'exercise-row mb-2';
        newRow.innerHTML = `
                <select name="exercises[${exerciseCount}][id]" class="form-select mb-1">
                    <option value="">Select an exercise</option>
                    <?php foreach ($exercises ?? [] as $exercise): ?>
                        <option value="<?php echo $exercise['id']; ?>"><?php echo htmlspecialchars($exercise['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="exercises[${exerciseCount}][sets]" placeholder="Sets" class="form-control mb-1" min="1">
                <input type="text" name="exercises[${exerciseCount}][reps]" placeholder="Reps (e.g., 10 or 8-12)" class="form-control">
            `;
        container.insertBefore(newRow, document.getElementById('add-exercise'));
        exerciseCount++;
    });
    </script>
</body>

</html>