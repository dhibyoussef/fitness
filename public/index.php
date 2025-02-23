<?php
// public/index.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/BaseController.php';
require_once __DIR__ . '/../app/controllers/AdminMiddleware.php';
require_once __DIR__ . '/../app/controllers/AuthMiddleware.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('Router');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::INFO));

$pdo = Database::getInstance();
$authMiddleware = new AuthMiddleware();
$adminMiddleware = new AdminMiddleware();

$routes = [
    'GET' => [
       '/' => fn() => (new BaseController($pdo))->render('home/index', ['pageTitle' => 'Fitness Tracker - Home']),
        '/auth/login' => fn() => (new BaseController($pdo))->render('auth/login', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken()]),
        '/auth/logout' => fn() => (new BaseController($pdo))->render('auth/logout', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken()]),
        '/auth/signup' => fn() => (new BaseController($pdo))->render('auth/signup', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken()]),
        '/workout/custom' => fn() => (new BaseController($pdo))->render('workout/custom', [
            'csrf_token' => (new BaseController($pdo))->generateCsrfToken(),
            'exercises' => (new ExerciseModel($pdo))->getUserExercises($_SESSION['user_id'] ?? 0)
        ]),
        '/workout/five-day' => fn() => (new BaseController($pdo))->render('workout/five-day'),
        '/workout/four-day' => fn() => (new BaseController($pdo))->render('workout/four-day'),
        '/statistics' => fn() => (new StatisticsController($pdo))->index(),
        '/admin/dashboard' => fn() => $adminMiddleware->handle(fn() => (new DashboardController($pdo))->index()),
        '/admin/statistics' => fn() => $adminMiddleware->handle(fn() => (new StatisticsController($pdo))->index()),
        '/admin/user_management' => fn() => $adminMiddleware->handle(fn() => (new UserManagementController($pdo))->index()),
        '/admin/users/(\d+)' => fn($id) => $adminMiddleware->handle(fn() => (new UserManagementController($pdo))->viewUserDetails((int)$id)),
        '/nutrition/index' => fn() => $authMiddleware->handle(fn() => (new IndexController($pdo))->index()),
        '/nutrition/create' => fn() => $authMiddleware->handle(fn() => (new CreateController($pdo))->render('nutrition/create', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken(), 'categories' => (new NutritionModel($pdo))->getAllCategories()])),
        '/nutrition/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->edit((int)$id)),
        '/nutrition/show/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new ShowController($pdo))->show((int)$id)),
        '/nutrition/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/progress/index' => fn() => $authMiddleware->handle(fn() => (new IndexController($pdo))->index()),
        '/progress/create' => fn() => $authMiddleware->handle(fn() => (new CreateController($pdo))->render('progress/create', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken()])),
        '/progress/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->edit((int)$id)),
        '/progress/show/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new ShowController($pdo))->show((int)$id)),
        '/progress/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/user/profile' => fn() => $authMiddleware->handle(fn() => (new ProfileController($pdo))->show((int)$_SESSION['user_id'])),
        '/user/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->edit((int)$id)),
        '/user/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/workout/index' => fn() => $authMiddleware->handle(fn() => (new IndexController($pdo))->index()),
        '/workout/create' => fn() => $authMiddleware->handle(fn() => (new CreateController($pdo))->render('workout/create', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken(), 'exercises' => (new ExerciseModel($pdo))->getUserExercises((int)$_SESSION['user_id']), 'categories' => (new WorkoutModel($pdo))->getAllCategories()])),
        '/workout/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->edit((int)$id)),
        '/workout/show/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new ShowController($pdo))->show((int)$id)),
        '/workout/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/fitness/index' => fn() => $authMiddleware->handle(fn() => (new FitnessController($pdo))->index()),
    ],
    'POST' => [
'/auth/login' => fn() => (new LoginController($pdo))->login($_POST),
        '/auth/logout' => fn() => (new LogoutController($pdo))->logout(),
        '/auth/signup' => fn() => (new SignupController($pdo))->signup($_POST),
        '/workout/createCustomWorkout' => fn() => (new CustomController($pdo))->createCustomWorkout($_POST),
        '/admin/users/bulk_activate' => fn() => $adminMiddleware->handle(fn() => (new UserManagementController($pdo))->bulkActivateUsers()),
        '/nutrition/create' => fn() => $authMiddleware->handle(fn() => (new CreateController($pdo))->create($_POST)),
        '/nutrition/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->update((int)$id, $_POST)),
        '/nutrition/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/progress/create' => fn() => $authMiddleware->handle(fn() => (new CreateController($pdo))->create($_POST)),
        '/progress/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->update((int)$id, $_POST)),
        '/progress/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/user/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->update((int)$id, $_POST)),
        '/user/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/workout/create' => fn() => $authMiddleware->handle(fn() => (new CreateController($pdo))->create($_POST)),
        '/workout/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditController($pdo))->update((int)$id, $_POST)),
        '/workout/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteController($pdo))->delete((int)$id)),
        '/fitness/calculate' => fn() => $authMiddleware->handle(fn() => (new FitnessController($pdo))->calculate()),
    ]
];

$method = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');

foreach ($routes[$method] ?? [] as $pattern => $handler) {
    $pattern = str_replace('/', '\/', $pattern);
    if (preg_match("/^{$pattern}$/", $uri, $matches)) {
        array_shift($matches);
        try {
            $handler(...$matches);
        } catch (Exception $e) {
            $logger->error("Route execution error", [
                'uri' => $uri,
                'method' => $method,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            (new BaseController($pdo))->renderError("An error occurred: " . htmlspecialchars($e->getMessage()));
        }
        exit;
    }
}

$logger->warning("Route not found", ['uri' => $uri, 'method' => $method]);
http_response_code(404);
(new BaseController($pdo))->renderError("Page not found.");