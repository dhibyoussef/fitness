<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Statistics</title>
    < link rel="stylesheet" href="/css/styles.css">
        <!-- Link to your CSS -->
</head>

<body>
    <header>
        <h1>Nutrition Statistics</h1>
        <nav>
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/nutrition">View Meals</a></li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Total Calories Consumed: <?php echo htmlspecialchars($totalCalories); ?></h2>
        <h3>Meal Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Meal Name</th>
                    <th>Calories</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meals as $meal): ?>
                <tr>
                    <td><?php echo htmlspecialchars($meal['name']); ?></td>
                    <td><?php echo htmlspecialchars($meal['calories']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>

</html>