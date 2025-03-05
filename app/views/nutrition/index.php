<?php
$pageTitle = $pageTitle ?? 'Nutrition Plans';
$meals = $meals ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$itemsPerPage = $itemsPerPage ?? 10;
$filter = $filter ?? '';
$sortBy = $sortBy ?? 'created_at';
$sortOrder = $sortOrder ?? 'DESC';
$csrf_token = $csrf_token ?? '';
$stats = $stats ?? [
    'total_calories' => 0, 'avg_calories' => 0,
    'total_protein' => 0, 'avg_protein' => 0,
    'total_carbs' => 0, 'avg_carbs' => 0,
    'total_fat' => 0, 'avg_fat' => 0
];
$execution_time = $execution_time ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
            padding: 2rem;
            margin-bottom: 20px;
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
<body>

<header class="header">
    <nav class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a href="/" class="text-2xl font-bold">Fitness Tracker</a>
            <div class="d-flex align-items-center gap-3">
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="toggle_theme" value="1">
                    <button type="submit" class="btn btn-link text-gray-600 hover:text-[var(--primary)] p-0">
                        <i class="fas fa-moon"></i>
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
            <a href="/dashboard" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="/workouts/index" class="nav-link"><i class="fas fa-dumbbell"></i> Workouts</a>
        </li>
        <li class="nav-item">
            <a href="/nutrition/index" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/nutrition') !== false ? 'active' : ''; ?>">
                <i class="fas fa-apple-alt"></i> Nutrition
            </a>
        </li>

        <li class="nav-item">
            <a href="/progress/index"
               class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/progress') !== false ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Progress
            </a>
        </li>
        <li class="nav-item">
            <a href="/user/profile" class="nav-link"><i class="fas fa-user"></i> Profile</a>
        </li>
        <li class="nav-item">
            <form method="POST" action="/auth/logout" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="nav-link btn btn-link text-start" style="padding: 10px; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="content">
    <div class="container">
        <h1 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars($pageTitle); ?></h1>

        <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
            <div class="alert alert-success animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
        <?php endif; ?>
        <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
            <div class="alert alert-danger animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
        <?php endif; ?>

        <div class="mb-4">
            <a href="/nutrition/create" class="btn btn-primary">Add New Meal</a>
        </div>

        <div class="card shadow-sm p-4 mb-4 animate__animated animate__fadeInUp">
            <h2 class="text-xl font-semibold mb-3">Nutrition Stats</h2>
            <p>Total Calories: <?php echo number_format($stats['total_calories'], 0); ?> kcal</p>
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
            <?php if (empty($meals)): ?>
                <tr>
                    <td colspan="7" class="text-center">No meals found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($meals as $index => $meal): ?>
                    <tr class="animate__animated animate__fadeIn" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <td><?php echo htmlspecialchars($meal['name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($meal['calories'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($meal['protein'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($meal['carbs'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($meal['fat'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($meal['category_name'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="/nutrition/show/<?php echo htmlspecialchars($meal['id'] ?? ''); ?>" class="btn btn-sm btn-info">View</a>
                            <a href="/nutrition/edit/<?php echo htmlspecialchars($meal['id'] ?? ''); ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="/nutrition/delete/<?php echo htmlspecialchars($meal['id'] ?? ''); ?>" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&filter=<?php echo urlencode($filter); ?>&sortBy=<?php echo urlencode($sortBy); ?>&sortOrder=<?php echo urlencode($sortOrder); ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo urlencode($filter); ?>&sortBy=<?php echo urlencode($sortBy); ?>&sortOrder=<?php echo urlencode($sortOrder); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&filter=<?php echo urlencode($filter); ?>&sortBy=<?php echo urlencode($sortBy); ?>&sortOrder=<?php echo urlencode($sortOrder); ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <p class="text-right text-sm text-gray-600 mt-4">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
    </div>
</div>

<footer>
    <div class="container text-center">
        <p>© <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>