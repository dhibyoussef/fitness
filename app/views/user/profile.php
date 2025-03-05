<?php
$pageTitle = $pageTitle ?? 'User Profile';
$user = $user ?? ['id' => 0, 'display_name' => 'User', 'email' => 'N/A', 'role' => 'N/A', 'joined' => 'N/A', 'last_activity' => null];
$progress = $progress ?? ['avg_weight' => 0, 'avg_body_fat' => 0, 'avg_muscle_mass' => 0];
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #f3e5f5, #e0f7fa);
        }
        .card-shadow {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .progress-bar-wrapper {
            background-color: #e9ecef;
            border-radius: 50rem;
            overflow: hidden;
        }
        .progress-bar {
            height: 12px;
        }
        .animated-hover:hover {
            transform: scale(1.03);
            transition: 0.15s ease-in-out;
        }
    </style>
</head>
<body>
<div class="container mx-auto py-12 max-w-4xl">
    <!-- Flash Messages -->
    <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
    <?php endif; ?>
    <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
    <?php endif; ?>

    <!-- Page Header -->
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800 animate__animated animate__fadeIn">
            <i class="fa-solid fa-user-circle text-purple-600"></i>
            Welcome, <?php echo htmlspecialchars($user['display_name'] ?? 'User'); ?>!
        </h1>
        <p class="text-gray-600 mt-2">Here’s an overview of your profile and progress.</p>
    </header>

    <!-- Profile Information Card -->
    <div class="card bg-white p-6 rounded-lg mb-6 card-shadow animate__animated animate__fadeInUp">
        <h2 class="text-2xl font-semibold text-purple-600 mb-4">
            <i class="fa-solid fa-user"></i> Profile Details
        </h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        <p><strong>Joined:</strong> <?php echo htmlspecialchars($user['joined'] ?? 'N/A'); ?></p>
        <p><strong>Last Activity:</strong>
            <?php echo isset($user['last_activity']) ? date('F j, Y H:i', strtotime($user['last_activity'])) : 'N/A'; ?>
        </p>
    </div>

    <!-- Progress Stats Card -->
    <div class="card bg-white p-6 rounded-lg mb-6 card-shadow animate__animated animate__fadeInUp">
        <h2 class="text-2xl font-semibold text-purple-600 mb-4">
            <i class="fa-solid fa-chart-line"></i> Progress Stats
        </h2>
        <div class="mb-4">
            <span class="font-medium text-gray-700">Average Weight: </span><?php echo number_format($progress['avg_weight'] ?? 0, 1); ?> kg
            <div class="progress-bar-wrapper mt-2">
                <div class="progress-bar bg-purple-500" style="width: <?php echo min(($progress['avg_weight'] ?? 0) / 100 * 100, 100); ?>%;"></div>
            </div>
        </div>
        <div class="mb-4">
            <span class="font-medium text-gray-700">Average Body Fat: </span><?php echo number_format($progress['avg_body_fat'] ?? 0, 1); ?>%
            <div class="progress-bar-wrapper mt-2">
                <div class="progress-bar bg-blue-500" style="width: <?php echo min(($progress['avg_body_fat'] ?? 0) / 100 * 100, 100); ?>%;"></div>
            </div>
        </div>
        <div>
            <span class="font-medium text-gray-700">Average Muscle Mass: </span><?php echo number_format($progress['avg_muscle_mass'] ?? 0, 1); ?> kg
            <div class="progress-bar-wrapper mt-2">
                <div class="progress-bar bg-green-500" style="width: <?php echo min(($progress['avg_muscle_mass'] ?? 0) / 100 * 100, 100); ?>%;"></div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-center gap-4 mt-6">
        <a href="/user/edit/<?php echo htmlspecialchars($user['id']); ?>"
           class="btn btn-warning px-5 py-2 rounded-md font-medium text-white hover:bg-yellow-600 animated-hover">
            <i class="fa-solid fa-edit"></i> Edit Profile
        </a>
        <a href="/user/delete/<?php echo htmlspecialchars($user['id']); ?>"
           class="btn btn-danger px-5 py-2 rounded-md font-medium text-white hover:bg-red-600 animated-hover">
            <i class="fa-solid fa-trash"></i> Delete Account
        </a>
        <a href="/dashboard"
           class="btn btn-primary px-5 py-2 rounded-md font-medium text-white hover:bg-blue-600 animated-hover">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <p class="text-center text-gray-600 mt-4">Page loaded in <?php echo number_format($execution_time, 4); ?> seconds</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>