<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Fitness App'; ?></title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS -->
</head>

<body>
    <?php include 'views/layouts/user/header.php'; ?>
    <main>
        <?php include($view); // Include the specific view ?>
    </main>
    <?php include 'views/layouts/user/footer.php'; ?>
</body>

</html>