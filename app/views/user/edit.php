<?php
$pageTitle = $pageTitle ?? 'Edit Profile';
$user = $user ?? [];
$csrf_token = $csrf_token ?? '';
$progress = $progress ?? ['avg_weight' => 50, 'min_weight' => 40, 'max_weight' => 60, 'avg_body_fat' => 20, 'avg_muscle_mass' => 30];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Bootstrap, TailwindCSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-r from-indigo-200 via-purple-200 to-pink-200">

<div class="container mx-auto py-8">
    <!-- Page Title -->
    <h1 class="text-4xl font-extrabold text-purple-700 mb-6 text-center animate__animated animate__fadeIn">
        <i class="fa-solid fa-user-edit"></i> <?php echo htmlspecialchars($pageTitle); ?>
    </h1>

    <!-- Flash Messages -->
    <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
        <div class="alert alert-success text-center animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
    <?php endif; ?>
    <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
        <div class="alert alert-danger text-center animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
    <?php endif; ?>

    <!-- Profile Update Form -->
    <form method="POST" action="/user/update/<?php echo htmlspecialchars($user['id']); ?>"
          class="bg-white shadow-xl rounded-lg p-6 animate__animated animate__fadeInUp space-y-4"
          style="max-width: 600px; margin: auto;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <!-- Username -->
        <div class="mb-3">
            <label for="name" class="block text-gray-700 font-medium mb-2">
                <i class="fa-solid fa-user"></i> Username
            </label>
            <input type="text" name="name" id="name" class="form-control hover:ring-2 ring-purple-400 focus:ring-purple-500"
                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="block text-gray-700 font-medium mb-2">
                <i class="fa-solid fa-envelope"></i> Email
            </label>
            <input type="email" name="email" id="email" class="form-control hover:ring-2 ring-purple-400 focus:ring-purple-500"
                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <!-- Buttons -->
        <div class="text-center space-x-4">
            <button type="submit" class="btn btn-primary hover:bg-purple-600 transition ease-in-out">
                <i class="fa-solid fa-check"></i> Update Profile
            </button>
            <a href="/user/profile" class="btn btn-secondary hover:text-white hover:bg-gray-700">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>

    <!-- Progress Stats -->
    <div class="bg-white shadow-lg rounded mt-8 p-6 animate__animated animate__fadeInUp space-y-4" style="max-width: 600px; margin: auto;">
        <h2 class="text-2xl font-bold text-purple-700 mb-4 text-center">Progress Stats</h2>

        <div class="flex items-center justify-between">
            <span>Average Weight:</span>
            <div class="w-3/4 bg-gray-200 rounded relative overflow-hidden" style="height: 12px;">
                <div class="bg-purple-600 h-full" style="width: <?php echo $progress['avg_weight'] / 100 * 100; ?>%;"></div>
            </div>
            <span><?php echo htmlspecialchars($progress['avg_weight']); ?> kg</span>
        </div>

        <div class="flex items-center justify-between">
            <span>Min Weight:</span>
            <div class="w-3/4 bg-gray-200 rounded relative overflow-hidden" style="height: 12px;">
                <div class="bg-green-500 h-full" style="width: <?php echo $progress['min_weight'] / 100 * 100; ?>%;"></div>
            </div>
            <span><?php echo htmlspecialchars($progress['min_weight']); ?> kg</span>
        </div>

        <div class="flex items-center justify-between">
            <span>Max Weight:</span>
            <div class="w-3/4 bg-gray-200 rounded relative overflow-hidden" style="height: 12px;">
                <div class="bg-red-500 h-full" style="width: <?php echo $progress['max_weight'] / 100 * 100; ?>%;"></div>
            </div>
            <span><?php echo htmlspecialchars($progress['max_weight']); ?> kg</span>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>