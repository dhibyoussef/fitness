<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '../../../app/models/ProgressModel.php';
$progressModel = new ProgressModel($pdo);
$progress = $progressModel->getProgressById($_SESSION['user_id']);

// app/views/progress/index.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 animate__animated animate__fadeIn">Progress Tracking</h1>

        <?php if ($success = isset($_SESSION['flash_messages']['success']) ? $_SESSION['flash_messages']['success'] : null): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        <?php if ($error = isset($_SESSION['flash_messages']['error']) ? $_SESSION['flash_messages']['error'] : null): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mb-4">
            <a href="/progress/create" class="btn btn-primary animate__animated animate__fadeInUp">Log New Progress</a>
        </div>

        <div class="card shadow-sm p-4 mb-4 animate__animated animate__fadeInUp">
            <h2 class="text-xl font-semibold mb-3">Progress Stats</h2>
            <p>Weight Change: <?php echo htmlspecialchars($stats['weight_change']); ?> kg</p>
            <p>Muscle Mass Change: <?php echo htmlspecialchars($stats['muscle_mass_change']); ?> kg</p>
            <p>Body Fat Change: <?php echo htmlspecialchars($stats['body_fat_change']); ?>%</p>
            <p>Total Entries: <?php echo htmlspecialchars($stats['total_entries']); ?></p>
        </div>

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
                <tr class="animate__animated animate__fadeIn"
                    style="animation-delay: <?php echo $loopIndex * 0.1; ?>s;">
                    <td><?php echo htmlspecialchars($entry['date']); ?></td>
                    <td><?php echo htmlspecialchars($entry['weight']); ?></td>
                    <td><?php echo htmlspecialchars($entry['body_fat']); ?></td>
                    <td><?php echo htmlspecialchars($entry['muscle_mass'] ?? 'N/A'); ?></td>
                    <td>
                        <a href="/progress/show/<?php echo $entry['id']; ?>" class="btn btn-sm btn-info">View</a>
                        <a href="/progress/edit/<?php echo $entry['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="/progress/delete/<?php echo $entry['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?>&filter=<?php echo urlencode($filter); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>