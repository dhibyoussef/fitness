<?php
// Load configuration
$config = require '../app/config/app.php';
$routes = require '../app/config/routes.php';

// Autoload classes
spl_autoload_register(function ($class) {
    include '../app/' . str_replace('\\', '/', $class) . '.php';
});

// Start the application
$requestUri = $_SERVER['REQUEST_URI'];
$requestedRoute = strtok($requestUri, '?'); // Remove query string

if (array_key_exists($requestedRoute, $routes)) {
    list($controllerName, $actionName) = explode('@', $routes[$requestedRoute]);
    $controller = new $controllerName();
    $controller->$actionName();
} else {
    // Handle 404 Not Found
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
}