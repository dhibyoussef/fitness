<?php
// app/controllers/WorkoutController/ShowController.php
namespace App\Controllers\WorkoutController {
    require_once __DIR__ . '/../../models/WorkoutModel.php';
    require_once __DIR__ . '/../../models/ExerciseModel.php';
    require_once __DIR__ . '/../../controllers/BaseController.php';
    require_once __DIR__ . '/../../../config/database.php';

    use App\Controllers\BaseController;
    use Exception;
    use App\Models\ExerciseModel;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use PDO;
    use App\Models\WorkoutModel;

    class ShowControllerW extends BaseController {
        private WorkoutModel $workoutModel;
        private ExerciseModel $exerciseModel;
        protected Logger $logger;

        public function __construct(PDO $pdo) {
            parent::__construct($pdo);
            $this->workoutModel = new WorkoutModel($pdo);
            $this->exerciseModel = new ExerciseModel($pdo);
            $this->logger = new Logger('WorkoutShowController');
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
            $this->requireAuth();
        }

        public function show(int $id): void {
            try {
                if ($id <= 0) {
                    throw new Exception('Invalid workout ID.');
                }

                $workout = $this->workoutModel->getWorkoutById($id);
                if (!$workout || ($workout['user_id'] !== (int)$_SESSION['user_id'] && !$workout['is_predefined'])) {
                    throw new Exception('Workout not found or not owned by you.');
                }

                $linkedExercises = $this->getLinkedExercises($id);
                $workout['formatted_duration'] = sprintf('%d:%02d', floor($workout['duration'] / 60), $workout['duration'] % 60);
                $workout['formatted_calories'] = $workout['calories'] ? number_format($workout['calories']) . ' kcal' : 'N/A';

                $this->render('workout/show', [
                    'pageTitle' => 'Workout Details - ' . htmlspecialchars($workout['name']),
                    'workout' => $workout,
                    'exercises' => $this->exerciseModel->getUserExercises((int)$_SESSION['user_id']),
                    'linkedExercises' => $linkedExercises,
                    'csrf_token' => $this->generateCsrfToken(),
                    'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                ]);
            } catch (Exception $e) {
                $this->logger->error("Workout show error", [
                    'message' => $e->getMessage(),
                    'id' => $id,
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'trace' => $e->getTraceAsString()
                ]);
                $this->setFlashMessage('error', $e->getMessage());
                $this->redirect('/workouts/index'); // Fixed: Redirect to plural "workouts"
            }
        }

        private function getLinkedExercises(int $workoutId): array {
            $query = "SELECT we.*, e.name, e.video_url FROM workout_exercises we 
                  JOIN exercises e ON we.exercise_id = e.id 
                  WHERE we.workout_id = :workout_id AND we.deleted_at IS NULL";
            $stmt = $this->pdo->prepare($query); // Fixed: Use $this->pdo instead of $this->db
            $stmt->execute(['workout_id' => $workoutId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
    }
}