<?php
$pageTitle = $pageTitle ?? 'Logout - Fitness Tracker';
$csrf_token = $csrf_token ?? ''; // Provided by BaseController::render()
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f2f5, #e9ecef);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-container {
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="logout-container">
    <div class="card shadow-lg p-6 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-center mb-6">Logout</h1>

        <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
            <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
        <?php endif; ?>
        <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
            <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
        <?php endif; ?>

        <p class="text-center mb-4">Are you sure you want to logout?</p>

        <form method="POST" action="/auth/logout" id="logout-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="mb-4 form-check">
                <input type="checkbox" name="logout_all_devices" id="logout_all_devices" class="form-check-input">
                <label for="logout_all_devices" class="form-check-label">Logout from all devices</label>
            </div>
            <button type="submit" class="btn btn-danger w-100">Yes, Logout</button>
        </form>

        <p class="text-center mt-4">
            <a href="/dashboard" class="text-gray-600 hover:underline">Cancel</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('logout-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data.status === 'success') {
            form.classList.add('animate__animated', data.animation);
            setTimeout(() => window.location.href = data.redirect, 500);
        } else {
            form.classList.add('animate__animated', data.animation);
            setTimeout(() => form.classList.remove('animate__animated', data.animation), 1000);
            alert(data.message); // Fallback for error display
        }
    });
</script>
</body>
</html>