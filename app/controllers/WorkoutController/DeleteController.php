<?php
// app/controllers/WorkoutController/DeleteController.php
require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DeleteController extends BaseController {
    private WorkoutModel $workoutModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
        $this->logger = new Logger('WorkoutDeleteController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function delete(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid workout ID.');
            }

            $workout = $this->workoutModel->getWorkoutById($id);
            if (!$workout || $workout['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Workout not found or not owned by you.');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->render(__DIR__ . '/../../views/workout/delete.php', [
                    'id' => $id,
                    'name' => $workout['name'],
                    'csrf_token' => $this->generateCsrfToken()
                ]);
                return;
            }

            if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token.');
            }

            if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'yes') {
                throw new Exception('Deletion not confirmed.');
            }

            $this->db->beginTransaction();
            $this->db->exec("UPDATE workout_exercises SET deleted_at = NOW() WHERE workout_id = $id");
            if ($this->workoutModel->deleteWorkout($id)) {
                $this->db->commit();
                $this->logger->info("Workout deleted", [
                    'id' => $id,
                    'user_id' => $_SESSION['user_id']
                ]);
                $this->setFlashMessage('success', 'Workout deleted successfully.');
            } else {
                $this->db->rollBack();
                throw new Exception('Failed to delete workout.');
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->logger->error("Deletion error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
        }
        $this->redirect('/workout/index');
    }
}