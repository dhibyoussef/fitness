<?php
$pageTitle = $pageTitle ?? 'Edit Meal Plan';
$nutrition = $nutrition ?? [];
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
            <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
        <?php endif; ?>
        <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
            <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="/nutrition/update/<?php echo htmlspecialchars($nutrition['id'] ?? ''); ?>" class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Meal Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? $nutrition['name'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="calories" class="form-label">Calories</label>
                <input type="number" name="calories" id="calories" class="form-control" value="<?php echo htmlspecialchars($_POST['calories'] ?? $nutrition['calories'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="protein" class="form-label">Protein (g)</label>
                <input type="number" name="protein" id="protein" class="form-control" step="0.1" value="<?php echo htmlspecialchars($_POST['protein'] ?? $nutrition['protein'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="carbs" class="form-label">Carbs (g)</label>
                <input type="number" name="carbs" id="carbs" class="form-control" step="0.1" value="<?php echo htmlspecialchars($_POST['carbs'] ?? $nutrition['carbs'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="fat" class="form-label">Fat (g)</label>
                <input type="number" name="fat" id="fat" class="form-control" step="0.1" value="<?php echo htmlspecialchars($_POST['fat'] ?? $nutrition['fat'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo (($_POST['category_id'] ?? $nutrition['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="/nutrition/index" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Meal</button>
            </div>
        </form>

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