<?php
$message = $message ?? 'An unknown error occurred';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-danger">
        <h1 class="alert-heading">Error</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="/" class="btn btn-primary">Return to Home</a>
    </div>
</div>
</body>
</html>