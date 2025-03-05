<?php
$pageTitle = $pageTitle ?? 'Delete Account';
$id = $id ?? 0;
$username = $username ?? 'User';
$csrf_token = $csrf_token ?? '';
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
    <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
    <?php endif; ?>
    <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn"><?php echo htmlspecialchars($pageTitle); ?></h1>

    <div class="card shadow-sm p-4 animate__animated animate__fadeInUp">
        <p>Are you sure you want to delete your account, <strong><?php echo htmlspecialchars($username); ?></strong>? This action is permanent and cannot be undone.</p>

        <form method="POST" action="/user/delete/<?php echo htmlspecialchars($id); ?>">
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