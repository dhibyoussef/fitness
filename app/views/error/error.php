<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&family=Roboto:wght@400&display=swap"
        rel="stylesheet">
</head>

<body class="bg-cover bg-center"
    style="background-image: url('https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?q=80&w=2085&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
    <main class="flex items-center justify-center min-h-screen">
        <div class="bg-white shadow-lg rounded-lg p-8 w-96 text-center">
            <h2 class="text-3xl font-bold mb-4 text-red-600">Oops! Something Went Wrong</h2>
            <p class="mb-6 text-gray-600">We encountered an unexpected error. Please try again later.</p>
            <a href="/" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">Return to
                Home</a>
            <p class="mt-4">If the problem persists, please <a href="/contact" class="text-blue-500">contact
                    support</a>.</p>
        </div>
    </main>
    <footer class="text-center mt-4">
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker. All rights reserved.</p>
    </footer>
</body>

</html>