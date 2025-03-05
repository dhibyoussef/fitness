<?php
// public/index.php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/BaseController.php';
require_once __DIR__ .'/app/middleware/AuthMiddleware.php';
require_once __DIR__ .'/app/middleware/AdminMiddleware.php';
require_once __DIR__ .'/app/middleware/CsrfMiddleware.php';
require_once __DIR__ .'/app/controllers/FitnessController.php';
require_once '../fitness-app/app/controllers/AdminController/DashboardController.php';
require_once '../fitness-app/app/controllers/AdminController/StatisticsController.php';
require_once '../fitness-app/app/controllers/AdminController/UserManagementController.php';
require_once  '../fitness-app/app/controllers/AuthController/LoginController.php';
require_once '../fitness-app/app/controllers/AuthController/LogoutController.php';
require_once '../fitness-app/app/controllers/AuthController/SignupController.php';
require_once '../fitness-app/app/controllers/NutritionController/IndexController.php';
require_once '../fitness-app/app/controllers/NutritionController/CreateController.php';
require_once '../fitness-app/app/controllers/NutritionController/EditController.php';
require_once '../fitness-app/app/controllers/NutritionController/ShowController.php';
require_once '../fitness-app/app/controllers/NutritionController/DeleteController.php';
require_once '../fitness-app/app/controllers/WorkoutController/IndexController.php';
require_once '../fitness-app/app/controllers/WorkoutController/CreateController.php';
require_once '../fitness-app/app/controllers/WorkoutController/EditController.php';
require_once '../fitness-app/app/controllers/WorkoutController/ShowController.php';
require_once '../fitness-app/app/controllers/WorkoutController/DeleteController.php';
require_once '../fitness-app/app/controllers/WorkoutController/CustomController.php';
require_once '../fitness-app/app/controllers/ProgressController/IndexController.php';
require_once '../fitness-app/app/controllers/ProgressController/CreateController.php';
require_once '../fitness-app/app/controllers/ProgressController/EditController.php';
require_once '../fitness-app/app/controllers/ProgressController/ShowController.php';
require_once '../fitness-app/app/controllers/ProgressController/DeleteController.php';
require_once '../fitness-app/app/controllers/UserController/EditController.php';
require_once '../fitness-app/app/controllers/UserController/DeleteController.php';
require_once '../fitness-app/app/controllers/UserController/ProfileController.php';
require_once __DIR__ . '/app/models/ExerciseModel.php';
require_once __DIR__ . '/app/models/NutritionModel.php';
require_once __DIR__ . '/app/models/WorkoutModel.php';
require_once __DIR__ . '/app/models/ProgressModel.php';
require_once __DIR__ . '/app/models/UserModel.php';
require_once __DIR__ . '/app/models/AdminModel.php';
require_once __DIR__ . '/app/models/baseModel.php';
require_once __DIR__ . '/app/models/ProfileModel.php';


use App\Controllers\AdminController\DashboardController;
use App\Controllers\AdminController\StatisticsController;
use App\Controllers\AdminController\UserManagementController;
use App\controllers\AuthController\LoginController;
use App\Controllers\AuthController\LogoutController;
use App\Controllers\AuthController\SignupController;
use App\Controllers\BaseController;
use App\Controllers\NutritionController\CreateControllerN;
use App\Controllers\NutritionController\DeleteControllerN;
use App\Controllers\NutritionController\EditControllerN;
use App\Controllers\NutritionController\IndexControllerN;
use App\Controllers\NutritionController\ShowControllerN;
use App\Controllers\ProgressController\CreateControllerP;
use App\Controllers\ProgressController\DeleteControllerP;
use App\Controllers\ProgressController\EditControllerP;
use App\Controllers\ProgressController\IndexControllerP;
use App\Controllers\ProgressController\ShowControllerP;
use App\Controllers\UserController\DeleteControllerU;
use App\Controllers\UserController\EditControllerU;
use App\Controllers\UserController\ProfileControllerU;
use App\Controllers\WorkoutController\CreateControllerW;
use App\Controllers\WorkoutController\CustomControllerW;
use App\Controllers\WorkoutController\DeleteControllerW;
use App\Controllers\WorkoutController\EditControllerW;
use App\Controllers\WorkoutController\IndexControllerW;
use App\Controllers\WorkoutController\ShowControllerW;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Models\ExerciseModel;
use App\Models\NutritionModel;
use App\Models\ProgressModel;
use App\Models\UserModel;
use App\Models\WorkoutModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Controllers\FitnessController;


$logger = new Logger('Router');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log'));

try {
    $pdo = new PDO('mysql:host=localhost;dbname=fitnesstracker;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,

    ]);


} catch (Exception $e) {
    $logger->error("Database connection failed: " . $e->getMessage());
    die("Cannot connect to database: " . htmlspecialchars($e->getMessage()));
}


$authMiddleware = new AuthMiddleware();
$adminMiddleware = new AdminMiddleware();

$routes = [
    'GET' => [
        '/' => fn() => (new BaseController($pdo))->render('home/index', ['pageTitle' => 'Fitness Tracker - Home']),
        '/dashboard' => fn() => $authMiddleware->handle(function() use ($pdo) {
            $userModel = new UserModel($pdo);
            $workoutModel = new WorkoutModel($pdo);
            $progressModel = new ProgressModel($pdo);
            $userId = $_SESSION['user_id'];

            $user = $userModel->getUserById($userId);
            $totalWorkouts = $workoutModel->getTotalWorkouts($userId); // Now returns int
            $progressPercentage = $progressModel->getProgressPercentage($userId); // Now returns float
            $monthlyProgress = $progressModel->getMonthlyProgress($userId); // Now returns array of month-progress pairs

            return (new BaseController($pdo))->render('dashboard/index', [
                'pageTitle' => 'User Dashboard',
                'totalWorkouts' => $totalWorkouts,
                'progressPercentage' => $progressPercentage,
                'monthlyProgress' => $monthlyProgress,
                'csrf_token' => (new BaseController($pdo))->generateCsrfToken()
            ]);
        }),
        '/auth/login' => fn() => (new BaseController($pdo))->render('auth/login', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken()]),
        '/auth/logout' => fn() => $authMiddleware->handle(fn() => (new BaseController($pdo))->render('auth/logout', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken()])),
        '/auth/signup' => fn() => (new BaseController($pdo))->render('auth/signup', ['csrf_token' => (new BaseController($pdo))->generateCsrfToken()]),
        '/workout/custom' => fn() => (new BaseController($pdo))->render('workout/custom', [
            'csrf_token' => (new BaseController($pdo))->generateCsrfToken(),
            'exercises' => (new ExerciseModel($pdo))->getUserExercises($_SESSION['user_id'] ?? 0)
        ]),
        '/workout/five-day' => fn() => (new BaseController($pdo))->render('workout/five-day'),
        '/workout/four-day' => fn() => (new BaseController($pdo))->render('workout/four-day'),
        '/admin/dashboard' => fn() => $adminMiddleware->handle(fn() => (new DashboardController($pdo))->index()), // Fixed with adminMiddleware
        '/admin/statistics' => fn() => $adminMiddleware->handle(fn() => (new StatisticsController($pdo))->index()),
        '/admin/user_management' => fn() => $adminMiddleware->handle(function() use ($pdo) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            (new UserManagementController($pdo))->index($page, $perPage, $search);
        }),
        '/admin/users/(\d+)' => fn($id) => $adminMiddleware->handle(fn() => (new UserManagementController($pdo))->viewUserDetails((int)$id)),
        '/nutrition/index' => fn() => $authMiddleware->handle(fn() => (new IndexControllerN($pdo))->index()),
        '/nutrition/create' => fn() => $authMiddleware->handle(fn() => (new CreateControllerN($pdo))->showCreateForm()), // Fixed: Use showCreateForm()
        '/nutrition/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerN($pdo))->edit((int)$id)),
        '/nutrition/show/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new ShowControllerN($pdo))->show((int)$id)),
        '/nutrition/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerN($pdo))->delete((int)$id)),
        '/progress/index' => fn() => $authMiddleware->handle(function() use ($pdo) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 10;
            $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
            $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'date';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'DESC';
            (new IndexControllerP($pdo))->index($page, $itemsPerPage, $filter, $sortBy, $sortOrder);
        }),
        '/progress/create' => fn() => $authMiddleware->handle(fn() => (new CreateControllerP($pdo))->showCreateForm()), // Fixed: Use showCreateForm()
        '/progress/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerP($pdo))->edit((int)$id)),
        '/progress/show/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new ShowControllerP($pdo))->show((int)$id)),
        '/progress/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerP($pdo))->delete((int)$id)),
        '/user/profile' => fn() => $authMiddleware->handle(fn() => (new ProfileControllerU($pdo))->show((int)$_SESSION['user_id'])),
        '/user/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerU($pdo))->edit((int)$id)), // Fixed: Correct route for edit
        '/user/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerU($pdo))->delete((int)$id)),
        '/workouts/index' => fn() => $authMiddleware->handle(function() use ($pdo) { // Fixed: Changed "workout" to "workouts"
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 10;
            $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
            $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'created_at';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'DESC';
            $showPredefined = isset($_GET['show_predefined']) ? (bool)$_GET['show_predefined'] : false;
            (new IndexControllerW($pdo))->index($page, $itemsPerPage, $filter, $sortBy, $sortOrder, $showPredefined);
        }),
        '/workout/create' => fn() => $authMiddleware->handle(fn() => (new CreateControllerW($pdo))->showCreateForm()), // Updated route
        '/workout/edit/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerW($pdo))->edit((int)$id)),
        '/workout/show/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new ShowControllerW($pdo))->show((int)$id)),
        '/workout/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerW($pdo))->delete((int)$id)),
        '/fitness/index' => fn() => $authMiddleware->handle(fn() => (new FitnessController($pdo))->index()),
    ],
    'POST' => [
        '/auth/login' => fn() => (new LoginController($pdo))->login($_POST),
        '/auth/logout' => fn() => $authMiddleware->handle(fn() => (new LogoutController($pdo))->logout()),
        '/auth/signup' => fn() => (new SignupController($pdo))->signup($_POST),
        '/workout/createCustomWorkout' => fn() => (new CustomControllerW($pdo))->createCustomWorkout($_POST),
        '/admin/users/bulk_activate' => fn() => $adminMiddleware->handle(fn() => (new UserManagementController($pdo))->bulkActivateUsers()),
        '/nutrition/create' => fn() => $authMiddleware->handle(fn() => (new CreateControllerN($pdo))->create($_POST)),
        '/nutrition/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerN($pdo))->update((int)$id, $_POST)),
        '/nutrition/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerN($pdo))->delete((int)$id)),
        '/progress/create' => fn() => $authMiddleware->handle(fn() => (new CreateControllerP($pdo))->create($_POST)),
        '/progress/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerP($pdo))->update((int)$id, $_POST)),
        '/progress/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerP($pdo))->delete((int)$id)),
        '/user/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerU($pdo))->update((int)$id, $_POST)),
        '/user/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerU($pdo))->delete((int)$id)),
        '/workout/create' => fn() => $authMiddleware->handle(fn() => (new CreateControllerW($pdo))->create($_POST)),
        '/workout/update/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new EditControllerW($pdo))->update((int)$id, $_POST)),
        '/workout/delete/(\d+)' => fn($id) => $authMiddleware->handle(fn() => (new DeleteControllerW($pdo))->delete((int)$id)),
        '/fitness/calculate' => fn() => $authMiddleware->handle(fn() => (new FitnessController($pdo))->calculate()),

    ]
];

$method = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$logger->info("Available GET routes", ['routes' => array_keys($routes['GET'])]); // Debug logging

foreach ($routes[$method] ?? [] as $pattern => $handler) {
    $pattern = str_replace('/', '\/', $pattern);
    if (preg_match("/^$pattern$/", $uri, $matches)) {
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