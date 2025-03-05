<?php
// app/controllers/WorkoutController/DeleteController.php
namespace App\Controllers\WorkoutController {
    require_once __DIR__ . '/../../models/WorkoutModel.php';
    require_once __DIR__ . '/../../controllers/BaseController.php';
    require_once __DIR__ . '/../../../config/database.php';

    use App\Controllers\BaseController;
    use Exception;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use PDO;
    use App\Models\WorkoutModel;

    class DeleteControllerW extends BaseController
    {
        private WorkoutModel $workoutModel;
        protected Logger $logger;

        public function __construct(PDO $pdo)
        {
            parent::__construct($pdo);
            $this->workoutModel = new WorkoutModel($pdo);
            $this->logger = new Logger('WorkoutDeleteController');
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
            $this->requireAuth();
        }

        public function delete(int $id): void
        {
            try {
                if ($id <= 0) {
                    throw new Exception('Invalid workout ID.');
                }

                $workout = $this->workoutModel->getWorkoutById($id);
                if (!$workout || $workout['user_id'] !== (int)$_SESSION['user_id']) {
                    throw new Exception('Workout not found or not owned by you.');
                }

                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    $this->render('workout/delete', [ // Fixed: Use relative path
                        'pageTitle' => 'Delete Workout - ' . htmlspecialchars($workout['name']),
                        'workout' => $workout,
                        'csrf_token' => $this->generateCsrfToken(),
                        'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                    ]);
                    return;
                }

                if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                    throw new Exception('Invalid security token.');
                }

                if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'yes') {
                    throw new Exception('Deletion not confirmed.');
                }

                $this->pdo->beginTransaction(); // Fixed: Use $this->pdo
                $this->pdo->exec("UPDATE workout_exercises SET deleted_at = NOW() WHERE workout_id = $id"); // Fixed: Use $this->pdo
                if ($this->workoutModel->deleteWorkout($id)) {
                    $this->pdo->commit();
                    $this->logger->info("Workout deleted", [
                        'id' => $id,
                        'user_id' => $_SESSION['user_id'],
                        'name' => $workout['name']
                    ]);
                    $this->setFlashMessage('success', 'Workout deleted successfully.');
                } else {
                    $this->pdo->rollBack();
                    throw new Exception('Failed to delete workout.');
                }
            } catch (Exception $e) {
                $this->pdo->rollBack(); // Fixed: Use $this->pdo
                $this->logger->error("Deletion error", [
                    'message' => $e->getMessage(),
                    'id' => $id,
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'trace' => $e->getTraceAsString()
                ]);
                $this->setFlashMessage('error', $e->getMessage());
            }
            $this->redirect('/workouts/index'); // Fixed: Redirect to plural "workouts"
        }
    }
}