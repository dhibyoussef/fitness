<?php
// app/views/admin/user_details.php
session_start();
require_once __DIR__ . '/../../models/UserModel.php';

$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [  
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
$userId = 3;
require_once __DIR__ . '/../../models/UserModel.php';
$userModel = new UserModel($pdo);
$user = $userModel->getUserById($userId);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">User Details:
            <?php echo htmlspecialchars($user['username']); ?></h1>

        <div class="card shadow-sm p-4 animate__animated animate__zoomIn">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($user['status']); ?></p>
            <p><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            <p><strong>Last Activity:</strong>
                <?php echo $user['last_activity'] ? date('F j, Y H:i', strtotime($user['last_activity'])) : 'N/A'; ?>
            </p>
        </div>

        <div class="mt-6">
            <a href="/admin/user_management" class="btn btn-primary animate__animated animate__fadeInUp">Back to User
                Management</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>