<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Progress Entry</title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS -->
</head>

<body>
    <header>
        <h1>Delete Progress Entry</h1>
        <nav>
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/progress">View Progress</a></li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Are you sure you want to delete the progress entry from <?php echo htmlspecialchars($progress['date']); ?>?
        </h2>
        <form action="/progress/delete/<?php echo $progress['id']; ?>" method="POST">
            <button type="submit">Yes, Delete</button>
            <a href="/progress">Cancel</a>
        </form>
    </main>
</body>

</html>