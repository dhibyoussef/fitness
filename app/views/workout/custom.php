<?php
// C:\xampp\htdocs\fitness-app\app\views\workout\custom.php
use App\Controllers\BaseController;
use App\Models\WorkoutModel;

session_start();
require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';

// Redirect if not logged in


try {
    $pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $workoutModel = new WorkoutModel($pdo);
    $baseController = new BaseController($pdo);

    $exercises = $workoutModel->getExercises() ?? []; // Fetch exercises
    $csrf_token = $baseController->generateCsrfToken();

    $pageTitle = 'Create Custom Workout - Fitness Tracker';
    $start_time = microtime(true);
} catch (Exception $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    :root {
        --primary: #4a90e2;
        --primary-dark: #357abd;
        --secondary: #6c757d;
        --background: #f0f2f5;
        --card-bg: #ffffff;
        --text: #333333;
        --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .dark-mode {
        --background: #1a202c;
        --card-bg: #2d3748;
        --text: #e2e8f0;
        --primary: #63b3ed;
        --primary-dark: #4299e1;
        --secondary: #a0aec0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: var(--background);
        color: var(--text);
        min-height: 100vh;
        margin: 0;
        overflow-x: hidden;
    }

    .header {
        background: var(--card-bg);
        box-shadow: var(--shadow);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        height: 70px;
    }

    .sidebar {
        position: fixed;
        top: 70px;
        left: 0;
        width: 250px;
        height: calc(100vh - 70px);
        background: var(--card-bg);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        overflow-y: auto;
        z-index: 900;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .sidebar-logo {
        font-size: 1.5rem;
        margin-right: 10px;
        color: var(--primary);
    }

    .nav-link {
        padding: 10px;
        color: var(--primary);
        display: block;
        transition: background 0.3s, color 0.3s;
        font-size: 0.9rem;
    }

    .nav-link.active,
    .nav-link:hover {
        background: var(--primary);
        color: #ffffff;
        border-radius: 8px;
    }

    .content {
        margin-left: 250px;
        padding: 90px 20px 20px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-container {
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
    }

    .card {
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 2rem;
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--primary);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-secondary,
    .btn-outline-secondary {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
    }

    .exercise-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 10px;
        align-items: center;
        margin-bottom: 15px;
    }

    .exercise-header {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 10px;
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 10px;
    }

    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
        width: 100%;
        max-width: 700px;
    }

    .execution-time {
        font-size: 0.85rem;
        color: var(--secondary);
        text-align: right;
        width: 100%;
        max-width: 700px;
    }

    footer {
        background: #343a40;
        color: #ffffff;
        padding: 1rem 0;
        margin-left: 250px;
        width: calc(100% - 250px);
    }

    .btn-icon {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }

        .content,
        footer {
            margin-left: 0;
            padding: 80px 15px 20px;
            width: 100%;
        }

        .form-container,
        .alert,
        .execution-time {
            max-width: 100%;
        }
    }
    </style>
</head>

<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">
    <header class="header">
        <nav class="container mx-auto px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/" class="text-2xl font-bold text-gray-800 dark:text-white">Fitness Tracker</a>
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/user/profile"
                        class="text-gray-600 hover:text-[var(--primary)] dark:text-gray-300 dark:hover:text-[var(--primary)] d-flex align-items-center">
                        <i
                            class="fas fa-user mr-2"></i><?php echo htmlspecialchars($_SESSION['username'] ?? 'Profile'); ?>
                    </a>
                    <form method="POST" action="/auth/logout" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <button type="submit"
                            class="btn btn-link text-gray-600 hover:text-red-500 dark:text-gray-300 dark:hover:text-red-400 p-0">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="toggle_theme" value="1">
                        <button type="submit"
                            class="btn btn-link text-gray-600 hover:text-[var(--primary)] dark:text-gray-300 dark:hover:text-[var(--primary)] p-0">
                            <i
                                class="fas <?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'fa-sun' : 'fa-moon'; ?>"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-dumbbell sidebar-logo"></i>
            <h4>My Fitness</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/dashboard"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/workouts/index" class="nav-link"><i class="fas fa-dumbbell me-2"></i> Workouts</a>

            </li>

            <li class="nav-item">
                <a href="nutrition/index"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/nutrition') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-apple-alt mr-2"></i> Nutrition
                </a>
            </li>
            <li class="nav-item">
                <a href="/user/profile" class="nav-link"><i class="fas fa-user me-2"></i> Profile</a>

            </li>
        </ul>
    </div>

    <div class="content">
        <div class="form-container">
            <h1 class="text-3xl font-bold mb-5">Create Custom Workout</h1>

            <?php if (isset($_SESSION['flash_messages']['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['flash_messages']['error']); unset($_SESSION['flash_messages']['error']); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/workout/createCustomWorkout" class="card">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="user_id" value="">
                <div class="mb-4">
                    <label for="name" class="form-label">Workout Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g., My Custom Routine"
                        required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"
                        placeholder="Briefly describe your workout..."></textarea>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration" id="duration" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label for="calories" class="form-label">Calories Burned (optional)</label>
                        <input type="number" name="calories" id="calories" class="form-control" min="0">
                    </div>
                </div>

                <div class="mb-4" id="exercise-container">
                    <h2 class="text-xl font-semibold mb-3">Exercises</h2>
                    <div class="exercise-header">
                        <span>Exercise</span>
                        <span>Sets</span>
                        <span>Reps</span>
                    </div>
                    <div class="exercise-row">
                        <select name="exercises[0][id]" class="form-select">
                            <option value="">Select an exercise</option>
                            <?php foreach ($exercises as $exercise): ?>
                            <option value="<?php echo htmlspecialchars($exercise['id']); ?>">
                                <?php echo htmlspecialchars($exercise['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <label>
                            <input type="number" name="exercises[0][sets]" placeholder="Sets" class="form-control" min="1">
                        </label>
                        <label>
                            <input type="text" name="exercises[0][reps]" placeholder="e.g., 10 or 8-12"
                                class="form-control">
                        </label>
                    </div>
                    <button type="button" id="add-exercise" class="btn btn-outline-secondary btn-icon mt-2">
                        <i class="fas fa-plus"></i> Add Exercise
                    </button>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/workouts/index" class="btn btn-secondary btn-icon">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-icon">
                        <i class="fas fa-save"></i> Create Workout
                    </button>
                </div>
            </form>

            <?php $execution_time = microtime(true) - $start_time; ?>
            <p class="execution-time">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
        </div>
    </div>

    <footer>
        <div class="container mx-auto text-center">
            <p>© <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let exerciseCount = 1;
    document.getElementById('add-exercise').addEventListener('click', () => {
        const container = document.getElementById('exercise-container');
        const newRow = document.createElement('div');
        newRow.className = 'exercise-row';
        newRow.innerHTML = `
                <select name="exercises[${exerciseCount}][id]" class="form-select">
                    <option value="">Select an exercise</option>
                    <?php foreach ($exercises as $exercise): ?>
                        <option value="<?php echo htmlspecialchars($exercise['id']); ?>">
                            <?php echo htmlspecialchars($exercise['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="exercises[${exerciseCount}][sets]" placeholder="Sets" class="form-control" min="1">
                <input type="text" name="exercises[${exerciseCount}][reps]" placeholder="e.g., 10 or 8-12" class="form-control">
            `;
        container.insertBefore(newRow, document.getElementById('add-exercise'));
        exerciseCount++;
    });
    </script>
</body>

</html>