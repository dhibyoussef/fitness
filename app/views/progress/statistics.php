<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Statistics</title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS -->
</head>

<body>
    <header>
        <h1>Progress Statistics</h1>
        <nav>
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/progress">View Progress</a></li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Average Weight: <?php echo htmlspecialchars($averageWeight); ?> kg</h2>
        <h2>Average Body Fat: <?php echo htmlspecialchars($averageBodyFat); ?> %</h2>
        <h3>Progress Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Weight (kg)</th>
                    <th>Body Fat (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($progressEntries as $entry): ?>
                <tr>
                    <td><?php echo htmlspecialchars($entry['date']); ?></td>
                    <td><?php echo htmlspecialchars($entry['weight']); ?></td>
                    <td><?php echo htmlspecialchars($entry['body_fat']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>

</html>