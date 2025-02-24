<?php
// app/views/workout/index.php
session_start();
require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';



try {
    $pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $workoutModel = new WorkoutModel($pdo);
    $userModel = new UserModel($pdo);
    $baseController = new BaseController($pdo);
    $totalWorkouts = 10;
    $workouts = $workoutModel->getWorkouts();

    // Pagination and filter logic
    $perPage = 10;
    $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';
    $showPredefined = isset($_GET['show_predefined']) ? (bool)$_GET['show_predefined'] : true;
    $offset = ($currentPage - 1) * $perPage;
    $userId = 3;
    $user = $userModel->getUserById($userId);
    $totalPages = ceil($totalWorkouts / $perPage);
    $stats = $workoutModel->getOverallWorkoutStatistics();



    $totalDuration = array_sum(array_column($workouts, 'duration'));
    $csrf_token = $baseController->generateCsrfToken();

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
    <title>Workouts - Fitness Tracker</title>
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
    }

    .card {
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 1.5rem;
        margin-bottom: 20px;
    }

    .table {
        background: var(--card-bg);
        border-radius: 8px;
        overflow: hidden;
    }

    .table th {
        background: var(--primary);
        color: #ffffff;
    }

    .btn-primary {
        background: var(--primary);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .btn-icon {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .execution-time {
        font-size: 0.85rem;
        color: var(--secondary);
        text-align: right;
    }

    footer {
        background: #343a40;
        color: #ffffff;
        padding: 1rem 0;
        margin-left: 250px;
        width: calc(100% - 250px);
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
                <a href="/workout/index"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/workout/index') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-dumbbell mr-2"></i> My Workouts
                </a>
            </li>
            <li class="nav-item">
                <a href="/workout/create"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/workout/create') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle mr-2"></i> Create Workout
                </a>
            </li>
            <li class="nav-item">
                <a href="/workout/custom"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/workout/custom') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-edit mr-2"></i> Custom Workout
                </a>
            </li>
            <li class="nav-item">
                <a href="/nutrition"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/nutrition') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-apple-alt mr-2"></i> Nutrition
                </a>
            </li>
            <li class="nav-item">
                <a href="/profile"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
            </li>
        </ul>
    </div>

    <div class="content">
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-5">My Workouts</h1>

            <?php if (isset($_SESSION['flash_messages']['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['flash_messages']['success']); unset($_SESSION['flash_messages']['success']); ?>
            </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['flash_messages']['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['flash_messages']['error']); unset($_SESSION['flash_messages']['error']); ?>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mb-4">
                <div class="d-flex gap-3">
                    <a href="/workout/create" class="btn btn-primary btn-icon">
                        <i class="fas fa-plus"></i> Add New Workout
                    </a>
                    <a href="?show_predefined=<?php echo $showPredefined ? '0' : '1'; ?>&filter=<?php echo urlencode($filter); ?>"
                        class="btn btn-outline-secondary btn-icon">
                        <i class="fas <?php echo $showPredefined ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                        <?php echo $showPredefined ? 'Hide Predefined' : 'Show Predefined'; ?>
                    </a>
                </div>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="filter" class="form-control" placeholder="Filter workouts..."
                        value="<?php echo htmlspecialchars($filter); ?>">
                    <button type="submit" class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="card">
                <h2 class="text-xl font-semibold mb-3">Workout Stats</h2>
                <p>Total Duration: <?php echo number_format($totalDuration, 0); ?> min</p>
                <p>Average Duration:
                    <?php echo $totalWorkouts > 0 ? number_format($totalDuration / $totalWorkouts, 1) : '0'; ?> min</p>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Duration (min)</th>
                                <th>Calories</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($workouts)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No workouts found.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($workouts as $index => $workout): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($workout['name']); ?></td>
                                <td><?php echo htmlspecialchars($workout['duration']); ?></td>
                                <td><?php echo htmlspecialchars($workout['calories'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($workout['category_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="/workout/show/<?php echo $workout['id']; ?>"
                                        class="btn btn-sm btn-info btn-icon">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (!$workout['is_predefined']): ?>
                                    <a href="/workout/edit/<?php echo $workout['id']; ?>"
                                        class="btn btn-sm btn-warning btn-icon">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/workout/delete/<?php echo $workout['id']; ?>"
                                        class="btn btn-sm btn-danger btn-icon"
                                        onclick="return confirm('Are you sure you want to delete this workout?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?page=<?php echo $currentPage - 1; ?>&filter=<?php echo urlencode($filter); ?>&show_predefined=<?php echo $showPredefined ? '1' : '0'; ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link"
                            href="?page=<?php echo $i; ?>&filter=<?php echo urlencode($filter); ?>&show_predefined=<?php echo $showPredefined ? '1' : '0'; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?page=<?php echo $currentPage + 1; ?>&filter=<?php echo urlencode($filter); ?>&show_predefined=<?php echo $showPredefined ? '1' : '0'; ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

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
</body>

</html>