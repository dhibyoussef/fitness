<?php
$pageTitle = $pageTitle ?? 'Edit Workout';
$workout = $workout ?? [];
$exercises = $exercises ?? [];
$linkedExercises = $linkedExercises ?? [];
$categories = $categories ?? [];
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

    <form method="POST" action="/workout/update/<?php echo htmlspecialchars($workout['id'] ?? ''); ?>" class="card shadow-sm p-4 animate__animated animate__fadeInUp">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Workout Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($workout['name'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($workout['description'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label">Duration (minutes)</label>
            <input type="number" name="duration" id="duration" class="form-control" value="<?php echo htmlspecialchars($workout['duration'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="calories" class="form-label">Calories Burned</label>
            <input type="number" name="calories" id="calories" class="form-control" value="<?php echo htmlspecialchars($workout['calories'] ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select">
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo ($workout['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3" id="exercise-container">
            <h2 class="text-xl font-semibold mb-2">Exercises</h2>
            <?php foreach ($linkedExercises as $index => $exercise): ?>
                <div class="exercise-row mb-2" data-index="<?php echo $index; ?>">
                    <select name="exercises[<?php echo $index; ?>][id]" class="form-select mb-1">
                        <option value="">Select an exercise</option>
                        <?php foreach ($exercises as $ex): ?>
                            <option value="<?php echo htmlspecialchars($ex['id']); ?>" <?php echo ($exercise['exercise_id'] ?? '') == $ex['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ex['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="exercises[<?php echo $index; ?>][sets]" value="<?php echo htmlspecialchars($exercise['sets'] ?? ''); ?>" placeholder="Sets" class="form-control mb-1" min="1">
                    <input type="text" name="exercises[<?php echo $index; ?>][reps]" value="<?php echo htmlspecialchars($exercise['reps'] ?? ''); ?>" placeholder="Reps (e.g., 10 or 8-12)" class="form-control">
                    <button type="button" class="btn btn-outline-danger btn-sm mt-1 remove-exercise">Remove</button>
                </div>
            <?php endforeach; ?>
            <button type="button" id="add-exercise" class="btn btn-outline-secondary">Add Exercise</button>
        </div>

        <button type="submit" class="btn btn-primary">Update Workout</button>
        <a href="/workouts/index" class="btn btn-secondary">Cancel</a>
    </form>

    <p class="text-right text-sm text-gray-600 mt-4">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let exerciseCount = <?php echo count($linkedExercises); ?>;
    document.getElementById('add-exercise').addEventListener('click', () => {
        const container = document.getElementById('exercise-container');
        const newRow = document.createElement('div');
        newRow.className = 'exercise-row mb-2';
        newRow.dataset.index = exerciseCount;
        newRow.innerHTML = `
                <select name="exercises[${exerciseCount}][id]" class="form-select mb-1">
                    <option value="">Select an exercise</option>
                    <?php foreach ($exercises as $exercise): ?>
                        <option value="<?php echo htmlspecialchars($exercise['id']); ?>"><?php echo htmlspecialchars($exercise['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="exercises[${exerciseCount}][sets]" placeholder="Sets" class="form-control mb-1" min="1">
                <input type="text" name="exercises[${exerciseCount}][reps]" placeholder="Reps (e.g., 10 or 8-12)" class="form-control">
                <button type="button" class="btn btn-outline-danger btn-sm mt-1 remove-exercise">Remove</button>
            `;
        container.insertBefore(newRow, document.getElementById('add-exercise'));
        exerciseCount++;
        addRemoveListener(newRow.querySelector('.remove-exercise'));
    });

    function addRemoveListener(btn) {
        btn.addEventListener('click', () => btn.parentElement.remove());
    }

    document.querySelectorAll('.remove-exercise').forEach(addRemoveListener);
</script>
</body>
</html>