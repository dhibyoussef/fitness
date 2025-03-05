<?php
namespace App\Controllers\AdminController;

require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use App\Controllers\BaseController;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Models\NutritionModel;
use App\Models\ProgressModel;
use App\Models\UserModel;
use App\Models\WorkoutModel;
use PDO;

class StatisticsController extends BaseController {
    private WorkoutModel $workoutModel;
    private NutritionModel $nutritionModel;
    private ProgressModel $progressModel;
    private UserModel $userModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->userModel = new UserModel($pdo);
        $this->logger = new Logger('StatisticsController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function index(): void {
        try {
            $this->checkAdminPermissions();
            $statistics = $this->getDashboardStatistics();
            $this->render('admin/statistics', [
                'pageTitle' => 'Admin Statistics - Fitness Tracker',
                'workoutStats' => $statistics['workoutStats'],
                'nutritionStats' => $statistics['nutritionStats'],
                'categoryTrends' => $statistics['categoryTrends'],
                'registrationTrends' => $statistics['registrationTrends'],
                'activeUserStats' => $statistics['activeUserStats'],
                'registrationLabels' => json_encode($statistics['registrationLabels']),
                'registrationData' => json_encode($statistics['registrationData']),
                'activeUserLabels' => json_encode($statistics['activeUserLabels']),
                'activeUserData' => json_encode($statistics['activeUserData']),
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Statistics fetch error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', "Failed to load statistics: " . $e->getMessage());
            $this->redirect('/admin/dashboard');
        }
    }

    private function getDashboardStatistics(): array {
        $cacheKey = 'admin_stats_' . date('Ymd');
        $cached = $this->fetchFromCache($cacheKey);
        if ($cached !== false) {
            $this->logger->info("Retrieved stats from cache", ['key' => $cacheKey]);
            return $cached;
        }

        $this->logger->info("Computing statistics", ['key' => $cacheKey]);
        $statistics = [
            'workoutStats' => $this->workoutModel->getOverallWorkoutStatistics() ?? ['total_workouts' => 0, 'avg_duration' => 0, 'total_calories' => 0],
            'nutritionStats' => $this->nutritionModel->getOverallNutritionStatistics() ?? ['avg_calories_per_meal' => 0, 'total_calories' => 0, 'total_meals' => 0],
            'categoryTrends' => $this->workoutModel->getAllCategories() ?? [],
            'registrationTrends' => $this->userModel->getRegistrationStatistics() ?? [],
            'activeUserStats' => $this->userModel->getActiveUserStatistics() ?? [],
        ];

        $statistics['registrationLabels'] = array_keys($statistics['registrationTrends']);
        $statistics['registrationData'] = array_values($statistics['registrationTrends']);
        $statistics['activeUserLabels'] = array_keys($statistics['activeUserStats']);
        $statistics['activeUserData'] = array_values($statistics['activeUserStats']);

        $this->storeInCache($cacheKey, $statistics, 300); // 5 minutes
        $this->logger->info("Cached stats", ['key' => $cacheKey]);
        return $statistics;
    }

    private function checkAdminPermissions(): void {
        if (!$this->isAdmin()) {
            $this->logger->warning("Unauthorized stats access", [
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            $this->setFlashMessage('error', 'Admin access required.');
            $this->redirect('/auth/login');
        }
    }
}