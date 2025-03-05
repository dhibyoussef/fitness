<?php
$pageTitle = $pageTitle ?? 'User Management - Fitness Tracker';
$users = $users ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$perPage = $perPage ?? 10;
$search = $search ?? '';
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
            --background: #f7f9fc;
            --text: #333333;
            --card-bg: #ffffff;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            display: flex;
        }

        .header {
            background: var(--card-bg);
            height: 70px;
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 250px;
            height: calc(100vh - 70px);
            background: var(--card-bg);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            z-index: 900;
        }

        .sidebar-header {
            font-size: 1.5rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 20px;
        }

        .nav-link {
            padding: 10px;
            color: var(--text);
            display: block;
            font-size: 0.9rem;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .nav-link.active,
        .nav-link:hover {
            background: var(--primary);
            color: #fff;
        }

        .content {
            margin-left: 250px;
            padding: 90px 20px 20px;
            width: calc(100% - 250px);
        }

        .content .card {
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            padding: 20px;
        }

        footer {
            background: #343a40;
            color: #fff;
            padding: 15px;
            text-align: center;
            margin-left: 250px;
            width: calc(100% - 250px);
            position: fixed;
            bottom: 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .content {
                margin-left: 0;
                padding: 90px 15px 15px;
            }

            footer {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">

<header class="header">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="text-2xl font-bold">Fitness Tracker</a>
        <div class="d-flex align-items-center">
            <form method="POST" action="" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="btn btn-link px-2">
                    <i class="fas <?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'fa-sun' : 'fa-moon'; ?>"></i>
                </button>
            </form>

        </div>
    </div>
</header>

<div class="sidebar">
    <div class="sidebar-header">Admin Panel</div>
    <ul class="nav flex-column">
        <li>
            <a href="/admin/dashboard" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li>
            <a href="/admin/user_management" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/user_management') !== false ? 'active' : ''; ?>"><i class="fas fa-users"></i> User Management</a>
        </li>
        <li>
            <a href="/admin/statistics" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/statistics') !== false ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Statistics</a>
        </li>

        <li>
            <a href="/auth/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>

<div class="content">
    <h1 class="mb-4"><?php echo htmlspecialchars($pageTitle); ?></h1>
    <div class="card">
        <form method="GET" class="d-flex mb-3">
            <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-search"></i></button>
        </form>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></td>
                            <td><?php echo htmlspecialchars($user['status'] ?? 'Active'); ?></td>
                            <td>
                                <a href="/admin/users/<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-sm btn-info">View</a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer>© <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>