<?php
// C:\xampp\htdocs\fitness-app\app\controllers\StatisticsController\StatisticsController.php
require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../config/database.php'; // Adjusted path to match your structure

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
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::INFO)); // Adjusted path
        $this->requireAuth(); // Ensure user is logged in
    }

    public function index(): void {
        try {
            $this->checkAdminPermissions();
            $statistics = $this->getDashboardStatistics();
            $this->render(__DIR__ . '/../../views/statistics/index.php', array_merge(
                $statistics,
                [
                    'csrf_token' => $this->generateCsrfToken(),
                    'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                ]
            ));
        } catch (Exception $e) {
            $this->logger->error("Stats fetch error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', 'Error loading statistics.');
            $this->redirect('/error');
        }
    }

    private function getDashboardStatistics(): array {
        $cacheKey = 'stats_' . date('Ymd');
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

            $this->storeInCache($cacheKey, $statistics, 300);
            $this->logger->info("Cached stats", ['key' => $cacheKey]);
        } catch (Exception $e) {
            $this->logger->error("Stats computation error", ['message' => $e->getMessage()]);
            $statistics['error'] = 'Error computing statistics: ' . $e->getMessage();
        }
        return $statistics;
    }



    private function getCategoryTrends(): array {
        $query = "SELECT c.name, COUNT(w.id) as workouts, COUNT(m.id) as meals 
                  FROM categories c 
                  LEFT JOIN workouts w ON w.category_id = c.id AND w.deleted_at IS NULL
                  LEFT JOIN meals m ON m.category_id = c.id AND m.deleted_at IS NULL
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