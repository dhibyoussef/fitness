<?php
namespace App\Controllers\WorkoutController;

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

class EditControllerW extends BaseController {
    private WorkoutModel $workoutModel;
    private ExerciseModel $exerciseModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
        $this->exerciseModel = new ExerciseModel($pdo);
        $this->logger = new Logger('WorkoutEditController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function edit(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid workout ID.');
            }

            $workout = $this->workoutModel->getWorkoutById($id);
            if (!$workout || $workout['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Workout not found or not owned by you.');
            }

            $exercises = $this->exerciseModel->getUserExercises((int)$_SESSION['user_id']);
            $linkedExercises = $this->getLinkedExercises($id);
            $this->render('workout/edit', [ // Fixed: Use relative path
                'pageTitle' => 'Edit Workout - ' . htmlspecialchars($workout['name']),
                'workout' => $workout,
                'exercises' => $exercises,
                'linkedExercises' => $linkedExercises,
                'csrf_token' => $this->generateCsrfToken(),
                'categories' => $this->workoutModel->getAllCategories(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Edit fetch error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/workouts/index'); // Fixed: Redirect to plural "workouts"
        }
    }

    public function update(int $id, array $data): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
                throw new Exception('Invalid request or security token.');
            }

            if ($id <= 0) {
                throw new Exception('Invalid workout ID.');
            }

            $workout = $this->workoutModel->getWorkoutById($id);
            if (!$workout || $workout['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Workout not found or not owned by you.');
            }

            $this->validateWorkoutData($data);
            $sanitizedData = [
                'name' => $this->sanitizeText($data['name']),
                'description' => $this->sanitizeText($data['description'] ?? $workout['description']),
                'duration' => (int)$data['duration'],
                'calories' => isset($data['calories']) && $data['calories'] !== '' ? (int)$data['calories'] : null,
                'category_id' => isset($data['category_id']) && $data['category_id'] !== '' ? (int)$data['category_id'] : null
            ];

            $this->pdo->beginTransaction(); // Fixed: Use $this->pdo
            if ($this->workoutModel->updateWorkout($id, $sanitizedData)) {
                if (!empty($data['exercises'])) {
                    $this->updateLinkedExercises($id, $data['exercises']);
                }
                $this->pdo->commit();
                $this->logger->info("Workout updated", [
                    'id' => $id,
                    'user_id' => $_SESSION['user_id'],
                    'name' => $sanitizedData['name']
                ]);
                $this->setFlashMessage('success', 'Workout updated successfully!');
            } else {
                $this->pdo->rollBack();
                throw new Exception('No changes detected or update failed.');
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->logger->error("Update error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
        }
        $this->redirect('/workouts/index'); // Fixed: Redirect to plural "workouts"
    }

    private function validateWorkoutData(array $data): void {
        if (empty($data['name']) || strlen($data['name']) < 3 || strlen($data['name']) > 100) {
            throw new Exception('Workout name must be 3-100 characters.');
        }
        if (empty($data['duration']) || !is_numeric($data['duration']) || $data['duration'] <= 0 || $data['duration'] > 1440) {
            throw new Exception('Duration must be 1-1440 minutes.');
        }
        if (isset($data['calories']) && $data['calories'] !== '' && (!is_numeric($data['calories']) || $data['calories'] < 0 || $data['calories'] > 10000)) {
            throw new Exception('Calories must be 0-10000 if provided.');
        }
        if (isset($data['category_id']) && $data['category_id'] !== '' && !$this->isValidCategory($data['category_id'])) {
            throw new Exception('Invalid category.');
        }
    }

    private function getLinkedExercises(int $workoutId): array {
        $query = "SELECT we.*, e.name FROM workout_exercises we 
                  JOIN exercises e ON we.exercise_id = e.id 
                  WHERE we.workout_id = :workout_id AND we.deleted_at IS NULL";
        $stmt = $this->pdo->prepare($query); // Fixed: Use $this->pdo
        $stmt->execute(['workout_id' => $workoutId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function updateLinkedExercises(int $workoutId, array $exercises): void {
        $this->pdo->exec("UPDATE workout_exercises SET deleted_at = NOW() WHERE workout_id = $workoutId"); // Fixed: Use $this->pdo
        foreach ($exercises as $exercise) {
            if (!isset($exercise['id'], $exercise['sets'], $exercise['reps']) || empty($exercise['id'])) {
                continue;
            }
            $query = "INSERT INTO workout_exercises (workout_id, exercise_id, sets, reps, rest_time) 
                      VALUES (:workout_id, :exercise_id, :sets, :reps, '60s')
                      ON DUPLICATE KEY UPDATE sets = :sets, reps = :reps, deleted_at = NULL";
            $stmt = $this->pdo->prepare($query); // Fixed: Use $this->pdo
            $stmt->execute([
                'workout_id' => $workoutId,
                'exercise_id' => (int)$exercise['id'],
                'sets' => (int)$exercise['sets'],
                'reps' => $this->sanitizeText($exercise['reps'])
            ]);
        }
    }

    private function isValidCategory($categoryId): bool {
        $query = "SELECT COUNT(*) FROM categories WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($query); // Fixed: Use $this->pdo
        $stmt->execute(['id' => (int)$categoryId]);
        return $stmt->fetchColumn() > 0;
    }

    private function sanitizeText(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}