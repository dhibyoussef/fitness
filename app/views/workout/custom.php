<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Custom Workout</title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS -->
</head>

<body>
    <header>
        <h1>Create Custom Workout</h1>
        <nav>
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/workout">View Workouts</a></li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form action="/workout/store_custom" method="POST">
            <label for="name">Workout Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="exercises">Exercises (comma-separated):</label>
            <input type="text" id="exercises" name="exercises" required>

            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>

            <button type="submit">Create Custom Workout</button>
        </form>
        <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</body>

</html>