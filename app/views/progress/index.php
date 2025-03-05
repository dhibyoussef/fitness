<?php
$pageTitle = $pageTitle ?? 'Progress Tracking'; // Use controller-provided value or default
$progressEntries = $progressEntries ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$itemsPerPage = $itemsPerPage ?? 10;
$filter = $filter ?? '';
$sortBy = $sortBy ?? 'date';
$sortOrder = $sortOrder ?? 'DESC';
$stats = $stats ?? ['weight_change' => 0, 'muscle_mass_change' => 0, 'body_fat_change' => 0, 'total_entries' => 0];
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f2f5, #e9ecef);
            margin: 0;
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #2d3748, #1a202c);
            padding-top: 60px;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.2);
            color: #e2e8f0;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #e2e8f0;
            padding: 15px 20px;
            font-size: 1.1rem;
            transition: 0.3s ease, color 0.3s ease;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .sidebar h4 {
            font-size: 1.5rem;
            padding-left: 20px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-white px-4 py-3"><?php echo htmlspecialchars($pageTitle); ?></h4>
    <nav class="nav flex-column">
        <a href="/" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="/user/profile" class="nav-link"><i class="fas fa-user"></i> Profile</a>
        <a href="/workouts/index" class="nav-link"><i class="fas fa-dumbbell"></i> Workouts</a>
        <a href="/nutrition/index" class="nav-link"><i class="fas fa-utensils"></i> Nutrition</a>
        <a href="/auth/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn"><?php echo htmlspecialchars($pageTitle); ?></h1>

        <!-- Display Flash Messages -->
        <?php if ($success = $_SESSION['flash']['success'] ?? null): ?>
            <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); unset($_SESSION['flash']['success']); ?></div>
        <?php endif; ?>
        <?php if ($error = $_SESSION['flash']['error'] ?? null): ?>
            <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); unset($_SESSION['flash']['error']); ?></div>
        <?php endif; ?>

        <!-- Button to Log New Progress -->
        <div class="mb-4">
            <a href="/progress/create" class="btn btn-primary animate__animated animate__fadeInUp">
                <i class="fas fa-plus-circle"></i> Log New Progress
            </a>
        </div>

        <!-- Progress Statistics -->
        <div class="card shadow-sm p-4 mb-4 animate__animated animate__fadeInUp">
            <h2 class="text-xl font-semibold mb-3">Progress Stats</h2>
            <p>Weight Change: <?php echo htmlspecialchars($stats['weight_change']); ?> kg</p>
            <p>Muscle Mass Change: <?php echo htmlspecialchars($stats['muscle_mass_change']); ?> kg</p>
            <p>Body Fat Change: <?php echo htmlspecialchars($stats['body_fat_change']); ?>%</p>
            <p>Total Entries: <?php echo htmlspecialchars($stats['total_entries']); ?></p>
        </div>

        <!-- Progress Entries Table -->
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Date</th>
                <th>Weight (kg)</th>
                <th>Body Fat (%)</th>
                <th>Muscle Mass (kg)</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($progressEntries as $entry): ?>
                <tr class="animate__animated animate__fadeIn">
                    <td><?php echo htmlspecialchars($entry['date']); ?></td>
                    <td><?php echo htmlspecialchars($entry['weight']); ?></td>
                    <td><?php echo htmlspecialchars($entry['body_fat']); ?></td>
                    <td><?php echo htmlspecialchars($entry['muscle_mass'] ?? 'N/A'); ?></td>
                    <td>
                        <a href="/progress/show/<?php echo $entry['id']; ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="/progress/edit/<?php echo $entry['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="/progress/delete/<?php echo $entry['id']; ?>" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($progressEntries)): ?>
                <tr>
                    <td colspan="5" class="text-center">No progress entries found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="/progress/index?page=<?php echo $i; ?>&filter=<?php echo urlencode($filter); ?>&sortBy=<?php echo $sortBy; ?>&sortOrder=<?php echo $sortOrder; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>