<?php
// app/controllers/WorkoutController/IndexController.php
require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class IndexController extends BaseController {
    private WorkoutModel $workoutModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
        $this->logger = new Logger('WorkoutIndexController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function index(int $page = 1, int $itemsPerPage = 10, string $filter = '', string $sortBy = 'created_at', string $sortOrder = 'DESC', bool $showPredefined = false): void {
        try {
            $offset = max(0, ($page - 1) * $itemsPerPage);
            $workouts = $this->workoutModel->getFilteredWorkouts($filter, $offset, $itemsPerPage, $sortBy, $sortOrder, (int)$_SESSION['user_id'], $showPredefined);
            $totalWorkouts = $this->workoutModel->countFilteredWorkouts($filter, (int)$_SESSION['user_id'], $showPredefined);
            $totalPages = max(1, (int)ceil($totalWorkouts / $itemsPerPage));

            $this->render(__DIR__ . '/../../views/workout/index.php', [
                'workouts' => $workouts,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'itemsPerPage' => $itemsPerPage,
                'filter' => htmlspecialchars($filter),
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'showPredefined' => $showPredefined,
                'csrf_token' => $this->generateCsrfToken(),
                'stats' => $this->getWorkoutStats(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Workouts fetch error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', 'Error loading workouts.');
            $this->redirect('/error');
        }
    }

    private function getWorkoutStats(): array {
        $query = "SELECT SUM(duration) as total_duration, AVG(duration) as avg_duration 
                  FROM workouts WHERE user_id = :user_id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id' => (int)$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_duration' => 0, 'avg_duration' => 0];
    }
}