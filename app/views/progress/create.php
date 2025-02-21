<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Progress Entry</title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS -->
</head>

<body>
    <header>
        <h1>Add New Progress Entry</h1>
        <nav>
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/progress">View Progress</a></li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form action="/progress/store" method="POST">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="weight">Weight (kg):</label>
            <input type="number" id="weight" name="weight" required>

            <label for="body_fat">Body Fat (%):</label>
            <input type="number" id="body_fat" name="body_fat" required>

            <button type="submit">Add Progress</button>
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