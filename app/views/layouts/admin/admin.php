<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Admin Panel'; ?></title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS -->
</head>

<body>
    <?php include 'views/layouts/admin/header.php'; ?>
    <main>
        <?php include($view); // Include the specific view ?>
    </main>
    <?php include 'views/layouts/admin/footer.php'; ?>
</body>

</html>