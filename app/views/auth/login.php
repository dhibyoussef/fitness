    <?php
    $pageTitle = 'Login - Fitness Tracker';
    $csrf_token = isset($csrf_token) ? $csrf_token : ''; // Passed from BaseController::render()
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
                background: url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center fixed;
                background-size: cover;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-container {
                max-width: 400px;
                width: 100%;
                background: rgba(255, 255, 255, 0.8);
                border-radius: 10px;
                padding: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>
    <body class="bg-gray-100 flex flex-col <?php echo (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : ''; ?>">
    <header class="bg-white shadow-md dark-mode:bg-gray-800 fixed top-0 w-full z-10">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-gray-800 dark-mode:text-white">Fitness Tracker</a>
            </div>
        </nav>
    </header>
    <div class="login-container">
        <div class="card shadow-lg p-6 animate__animated animate__fadeIn">
            <h1 class="text-3xl font-bold text-center mb-6">Login</h1>

            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <?php echo htmlspecialchars($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?php echo htmlspecialchars($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/auth/login">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember_me" id="remember_me" class="form-check-input">
                    <label for="remember_me" class="form-check-label">Remember Me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-4">
                Don’t have an account? <a href="/auth/signup" class="text-blue-500 hover:underline">Sign Up</a>
            </p>
            <p class="text-center">
                <a href="/" class="text-gray-600 hover:underline">Back to Home</a>
            </p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>