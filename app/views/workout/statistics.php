<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Statistics</title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS -->
</head>

<body>
    <header>
        <h1>Workout Statistics</h1>
        <nav>
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/workout">View Workouts</a></li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Total Workouts: <?php echo htmlspecialchars($totalWorkouts); ?></h2>
        <h2>Total Duration: <?php echo htmlspecialchars($totalDuration); ?> minutes</h2>
        <h2>Average Duration: <?php echo htmlspecialchars($averageDuration); ?> minutes</h2>
    </main>
</body>

</html>