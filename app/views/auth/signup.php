<?php
session_start();


// C:\xampp\htdocs\fitness-app\app\views\auth\signup.php
$pageTitle = 'Sign Up - Fitness Tracker';
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

    .signup-container {
        max-width: 400px;
        width: 100%;
    }
    </style>
</head>

<body class="bg-gray-100">
    <header class="bg-white shadow-md dark-mode:bg-gray-800 fixed top-0 w-full z-10">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-gray-800 dark-mode:text-white">Fitness Tracker</a>
            </div>
        </nav>
    </header>
    <form method="POST" action="../../../app/controllers/AuthController/SignupController.php">
        <div class="signup-container">
            <div class="card shadow-lg p-6 animate__animated animate__fadeIn">
                <h1 class="text-3xl font-bold text-center mb-6">Sign Up</h1>

                <?php if ($error = isset($_SESSION['flash_messages']['error']) ? $_SESSION['flash_messages']['error'] : null): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                <?php if ($success = isset($_SESSION['flash_messages']['success']) ? $_SESSION['flash_messages']['success'] : null): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="/auth/signup">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <div class="mb-4">
                        <label for="name" class="form-label">Username</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                </form>

                <p class="text-center mt-4">
                    Already have an account? <a href="../../views/auth/login.php"
                        class="text-blue-500 hover:underline">Login</a>
                </p>
                <p class="text-center">
                    <a href="/" class="text-gray-600 hover:underline">Back to Home</a>
                </p>
            </div>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>