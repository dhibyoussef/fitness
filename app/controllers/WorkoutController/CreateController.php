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

class CreateControllerW extends BaseController {
    private WorkoutModel $workoutModel;
    private ExerciseModel $exerciseModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
        $this->exerciseModel = new ExerciseModel($pdo);
        $this->logger = new Logger('WorkoutCreateController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function showCreateForm(): void {
        try {
            $this->render('workout/create', [
                'pageTitle' => 'Create Workout',
                'csrf_token' => $this->generateCsrfToken(),
                'exercises' => $this->exerciseModel->getUserExercises((int)$_SESSION['user_id']),
                'categories' => $this->workoutModel->getAllCategories(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Show create form error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', 'Error loading create form.');
            $this->redirect('/workouts/index');
        }
    }

    public function create(array $data): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
                throw new Exception('Invalid request or security token.');
            }

            $this->validateWorkoutData($data);
            $sanitizedData = [
                'user_id' => (int)$_SESSION['user_id'],
                'name' => $this->sanitizeText($data['name']),
                'description' => $this->sanitizeText($data['description'] ?? ''),
                'category_id' => isset($data['category_id']) && !empty($data['category_id']) ? (int)$data['category_id'] : null,
                'duration' => (int)$data['duration'],
                'calories' => isset($data['calories']) && !empty($data['calories']) ? (int)$data['calories'] : null,
            ];

            $this->pdo->beginTransaction();
            if ($this->workoutModel->createWorkout($sanitizedData)) {
                $workoutId = $this->pdo->lastInsertId();
                if (!empty($data['exercises'])) {
                    $this->linkExercises($workoutId, $data['exercises']);
                }
                $this->pdo->commit();
                $this->logger->info("Workout created", [
                    'user_id' => $_SESSION['user_id'],
                    'name' => $sanitizedData['name']
                ]);
                $this->setFlashMessage('success', 'Workout created successfully!');
                $this->redirect('/workouts/index');
            } else {
                $this->pdo->rollBack();
                throw new Exception('Failed to create workout.');
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->logger->error("Workout creation error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/workout/create');
        }
    }

    private function validateWorkoutData(array $data): void {
        if (empty($data['name']) || strlen($data['name']) < 3 || strlen($data['name']) > 100) {
            throw new Exception('Workout name must be 3-100 characters.');
        }
        if (empty($data['duration']) || !is_numeric($data['duration']) || $data['duration'] <= 0 || $data['duration'] > 1440) {
            throw new Exception('Duration must be 1-1440 minutes.');
        }
        if (isset($data['calories']) && (!is_numeric($data['calories']) || $data['calories'] < 0 || $data['calories'] > 10000)) {
            throw new Exception('Calories must be 0-10000 if provided.');
        }
        if (isset($data['category_id']) && !empty($data['category_id']) && !$this->isValidCategory($data['category_id'])) {
            throw new Exception('Invalid category.');
        }
    }

    private function linkExercises(int $workoutId, array $exercises): void {
        foreach ($exercises as $exercise) {
            if (!isset($exercise['id'], $exercise['sets'], $exercise['reps']) || empty($exercise['id'])) {
                continue;
            }
            $query = "INSERT INTO workout_exercises (workout_id, exercise_id, sets, reps, rest_time) 
                      VALUES (:workout_id, :exercise_id, :sets, :reps, '60s')";
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