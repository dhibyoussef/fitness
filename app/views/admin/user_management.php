<?php
// app/views/admin/user_management.php
session_start();
require_once __DIR__ . '/../../../app/models/UserModel.php';
require_once __DIR__ . '/../../../app/controllers/BaseController.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $userModel = new UserModel($pdo);
    $baseController = new BaseController($pdo);

    // Pagination and search logic
    $perPage = 10;
    $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $offset = ($currentPage - 1) * $perPage;

    $totalUsers = $userModel->getUserCount($search); // Assume this method accepts a search param
    $totalPages = ceil($totalUsers / $perPage);
    $users = $userModel->getUsers(); // Adjusted method parameters to match expected order

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
    <title>User Management - Fitness Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f0f2f5;
        min-height: 100vh;
        margin: 0;
        overflow-x: hidden;
    }

    .header {
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
    }

    .sidebar {
        position: fixed;
        top: 70px;
        left: 0;
        width: 250px;
        height: calc(100vh - 70px);
        background: #ffffff;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        overflow-y: auto;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .sidebar-logo {
        font-size: 1.5rem;
        margin-right: 10px;
        color: #4a90e2;
    }

    .nav-link {
        padding: 10px;
        color: #4a90e2;
        display: block;
        transition: background 0.3s, color 0.3s;
    }

    .nav-link.active,
    .nav-link:hover {
        background: #4a90e2;
        color: white;
        border-radius: 8px;
    }

    .content {
        margin-left: 270px;
        padding: 90px 20px 20px;
        overflow-y: auto;
        height: 100vh;
    }

    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }

    .table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
    }

    .table th {
        background: #4a90e2;
        color: white;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .execution-time {
        font-size: 0.9rem;
        color: #6c757d;
        text-align: right;
        margin-top: 20px;
    }

    .dark-mode {
        background: #1a202c;
        color: #e2e8f0;
    }

    .dark-mode .header,
    .dark-mode .sidebar,
    .dark-mode .card,
    .dark-mode .table {
        background: #2d3748;
    }

    .dark-mode .table th {
        background: #2c5282;
    }

    .logout-btn {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.3s;
    }

    .logout-btn:hover {
        color: #c82333;
    }

    .dark-mode .logout-btn {
        color: #ff6b6b;
    }

    .dark-mode .logout-btn:hover {
        color: #ff4040;
    }
    </style>
</head>

<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">
    <header class="header">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-gray-800 dark:text-white">Fitness Tracker</a>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/user/profile"
                        class="text-gray-600 hover:text-blue-500 dark:text-gray-300 dark:hover:text-blue-400 flex items-center">
                        <i
                            class="fas fa-user mr-2"></i><?php echo htmlspecialchars($_SESSION['username'] ?? 'Profile'); ?>
                    </a>
                    <form method="POST" action="/auth/logout">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i></button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="toggle_theme" value="1">
                        <button type="submit"
                            class="text-gray-600 hover:text-blue-500 dark:text-gray-300 dark:hover:text-blue-400">
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
            <h4>Admin Panel</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="/admin/dashboard"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-tachometer-alt mr-2"></i> Dashboard</a></li>
            <li class="nav-item"><a href="/admin/users"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-users mr-2"></i> User Management</a></li>
            <li class="nav-item"><a href="/statistics"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/statistics') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-chart-line mr-2"></i> Statistics</a></li>
            <li class="nav-item"><a href="/admin/settings"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-cog mr-2"></i> Settings</a></li>
            <li class="nav-item"><a href="/admin/notifications"
                    class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/notifications') !== false ? 'active' : ''; ?>"><i
                        class="fas fa-bell mr-2"></i> Notifications</a></li>
            <li class="nav-item"><a href="/admin/logout" class="nav-link"><i class="fas fa-sign-out-alt mr-2"></i>
                    Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="container">
            <h1 class="text-3xl font-bold mb-4">User Management</h1>
            <div class="card mb-4">
                <form method="GET" class="d-flex align-items-center">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search users..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="card">
                <form method="POST" action="/admin/users/bulk_activate" id="bulkForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
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
                                    <td colspan="7" class="text-center">No users found.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]"
                                            value="<?php echo htmlspecialchars($user['id']); ?>" class="userCheckbox">
                                    </td>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></td>
                                    <td><?php echo htmlspecialchars($user['status'] ?? 'Active'); ?></td>
                                    <td>
                                        <a href="/admin/users/<?php echo htmlspecialchars($user['id']); ?>/view"
                                            class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                        <a href="/admin/users/<?php echo htmlspecialchars($user['id']); ?>/edit"
                                            class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <a href="/admin/users/<?php echo htmlspecialchars($user['id']); ?>/delete"
                                            class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i
                                                class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Activate
                            Selected</button>
                    </div>
                </form>

                <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                        <li class="page-item"><a class="page-link"
                                href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                        </li>
                        <?php endif; ?>
                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item"><a class="page-link"
                                href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>

            <?php $execution_time = microtime(true) - $start_time; ?>
            <p class="execution-time">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-4 dark:bg-gray-900">
        <div class="container mx-auto text-center">
            <p>© <?php echo date('Y'); ?> Fitness Tracker. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectAll = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.userCheckbox');

        selectAll.addEventListener('change', () => {
            userCheckboxes.forEach(cb => cb.checked = selectAll.checked);
        });

        userCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                if (!cb.checked) selectAll.checked = false;
            });
        });
    });
    </script>
</body>

</html>