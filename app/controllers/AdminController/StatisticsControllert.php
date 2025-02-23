<?php
// app/controllers/AdminController/StatisticsController.php
require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class StatisticsController extends BaseController {
    private WorkoutModel $workoutModel;
    private NutritionModel $nutritionModel;
    private ProgressModel $progressModel;
    private UserModel $userModel;
    private Logger $logger;

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
            $this->render(__DIR__ . '/../../views/admin/statistics.php', array_merge(
                $statistics,
                [
                    'csrf_token' => $this->generateCsrfToken(),
                    'user_id' => $_SESSION['user_id'],
                    'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                ]
            ));
        } catch (Exception $e) {
            $this->logger->error("Statistics fetch error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->renderError("Failed to load statistics: " . htmlspecialchars($e->getMessage()));
        }
    }

    private function getDashboardStatistics(): array {
        $cacheKey = 'admin_stats_' . date('Ymd');
        $cached = $this->fetchFromCache($cacheKey);
        if ($cached !== false) {
            $this->logger->info("Retrieved stats from cache", ['key' => $cacheKey]);
            return $cached;
        }

        $statistics = [];
        try {
            $registrationData = $this->userModel->getRegistrationStatistics();
            $statistics['registrationLabels'] = array_keys($registrationData);
            $statistics['registrationData'] = array_values($registrationData);

            $activeUserData = $this->userModel->getActiveUserStatistics();
            $statistics['activeUserLabels'] = array_keys($activeUserData);
            $statistics['activeUserData'] = array_values($activeUserData);

            $statistics['workoutStats'] = $this->workoutModel->getOverallWorkoutStatistics();
            $statistics['nutritionStats'] = $this->nutritionModel->getOverallNutritionStatistics();
            $statistics['progressStats'] = $this->progressModel->getOverallProgressStatistics();
            $statistics['categoryTrends'] = $this->getCategoryTrends();
            $statistics['timestamp'] = date('Y-m-d H:i:s');

            $this->storeInCache($cacheKey, $statistics, 300);
            $this->logger->info("Cached stats", ['key' => $cacheKey]);
        } catch (Exception $e) {
            $this->logger->error("Stats computation error", ['exception' => $e->getMessage()]);
            $statistics['error'] = "Partial data available due to: " . $e->getMessage();
        }

        return $statistics;
    }



    private function getCategoryTrends(): array {
        $query = "SELECT c.name, COUNT(w.id) as count 
                  FROM categories c 
                  LEFT JOIN workouts w ON w.category_id = c.id 
                  GROUP BY c.id, c.name";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
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