<?php

session_start();
require_once __DIR__ . '/../../models/UserModel.php';
$pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
$userId = 3;
$userModel = new UserModel($pdo);
$user = $userModel->getUserById($userId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Delete Account</h1>

        <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
            <p>Are you sure you want to delete your account? This action is permanent and cannot be undone.</p>

            <form method="POST" action="/user/delete/<?php echo $id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="confirm" value="yes">
                <button type="submit" class="btn btn-danger">Yes, Delete My Account</button>
                <a href="/user/profile" class="btn btn-secondary">No, Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>