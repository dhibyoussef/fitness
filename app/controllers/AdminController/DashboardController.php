<?php
// app/controllers/AdminController/DashboardController.php

namespace App\Controllers\AdminController {
    session_start();
    require_once __DIR__ . '/../../../config/database.php';
    require_once __DIR__ . '/../../controllers/BaseController.php';
    require_once __DIR__ . '/../../models/AdminModel.php';
    require_once __DIR__ . '/../../../vendor/autoload.php';

    use Exception;
    use Monolog\Logger;
    use App\Models\AdminModel;
    use App\Controllers\BaseController;
    use Monolog\Handler\StreamHandler;
    use PDO;

    class DashboardController extends BaseController {
        private AdminModel $adminModel;
        protected Logger $logger;

        public function __construct(PDO $pdo) {
            parent::__construct($pdo);
            $this->adminModel = new AdminModel($pdo);
            $this->logger = new Logger('DashboardController');
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log'));
            $this->requireAuth();
        }

        public function index(): void {
            try {
                $this->checkAdminPermissions();
                $dashboardData = $this->getDashboardStatistics();
                $this->render('admin/dashboard', array_merge(
                    $dashboardData,
                    [
                        'csrf_token' => $this->generateCsrfToken(),
                        'user_id' => $_SESSION['user_id'],
                        'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                    ]
                ));
            } catch (Exception $e) {
                $this->logger->error("Dashboard fetch error", [
                    'message' => $e->getMessage(),
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'trace' => $e->getTraceAsString()
                ]);
                $this->renderError("Failed to load dashboard: " . htmlspecialchars($e->getMessage()));
            }
        }

        private function getDashboardStatistics(): array {
            $cacheKey = 'admin_dashboard_stats_' . date('Ymd');
            $cached = $this->fetchFromCache($cacheKey);
            if ($cached !== false) {
                $this->logger->info("Retrieved dashboard stats from cache", ['key' => $cacheKey]);
                return $cached;
            }
            $this->logger->info("Dashboard stats not found in cache, computing...", ['key' => $cacheKey]);
            $cacheKey = 'admin_dashboard_stats_' . date('Ymd');
            $stats = [
                'userCount' => $this->adminModel->getUserCount(),
                'activeUsers' => $this->adminModel->getActiveUserCount(),
                'workoutStats' => $this->adminModel->getWorkoutStatistics(),
                'nutritionData' => $this->adminModel->getNutritionData(),
                'registrationTrends' => $this->adminModel->getRegistrationTrends(),
                'realTimeUsers' => $this->getRealTimeUsers(),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->logger->info("Cached dashboard stats", ['key' => $cacheKey]);
            return $stats;
        }



        private function getRealTimeUsers(): int {
            $query = "SELECT COUNT(*) FROM users WHERE last_activity > NOW() - INTERVAL 5 MINUTE";
            return (int)$this->pdo->query($query)->fetchColumn();
        }

        private function checkAdminPermissions(): void {
            if (!$this->isAdmin()) {
                $this->logger->warning("Unauthorized access attempt", [
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'ip' => $_SERVER['REMOTE_ADDR']
                ]);
                $this->setFlashMessage('error', 'Admin access required.');
                $this->redirect('/auth/login');
            }
        }

        public function isAdmin(): bool {
            return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        }


    }
}