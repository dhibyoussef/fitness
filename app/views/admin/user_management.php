<?php
// app/views/admin/user_management.php
session_start();
require_once __DIR__ . '../../../app/models/UserModel.php';
require_once __DIR__ . '../../../app/models/AdminModel.php';

if (!isset($_SESSION['admin'])) {
    header('Location: /admin/login');
    exit;
}

$userModel = new UserModel($pdo);
$users = $userModel->getAllUsers(10, 0);
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">User Management</h1>

        <form method="GET" class="mb-4 animate__animated animate__fadeInUp">
            <div class="input-group">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control"
                    placeholder="Search users...">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <form method="POST" action="/admin/users/bulk_activate" id="bulkForm"
            class="animate__animated animate__fadeInUp">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <table class="table table-striped">
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
                    <?php foreach ($users as $user): ?>
                    <tr class="animate__animated animate__fadeIn"
                        style="animation-delay: <?php echo htmlspecialchars(($user['id'] % 10) * 0.1); ?>s;">
                        <td><input type="checkbox" name="user_ids[]" value="<?php echo $user['id']; ?>"
                                class="userCheckbox"></td>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['status']); ?></td>
                        <td>
                            <a href="/admin/users/<?php echo $user['id']; ?>" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Activate Selected</button>
        </form>

        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.userCheckbox').forEach(cb => cb.checked = this.checked);
    });
    </script>
</body>

</html>