<?php
require_once '../../models/WorkoutModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class CreateController extends BaseController {
    private WorkoutModel $workoutModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
    }

    public function create(array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please log in to create a workout.');
            return;
        }

        if (!$this->validateWorkoutData($data)) {
            $this->redirectWithError('Validation failed. Please check the input data.');
            return;
        }

        try {
            $this->workoutModel->createWorkout($data);
            $_SESSION['success_message'] = 'Workout created successfully.';
            $this->redirect('../../views/workout/index.php'); // Redirect to workout overview after successful creation
        } catch (Exception $e) {
            error_log('Creation failed: ' . $e->getMessage());
            $this->redirectWithError('Failed to create workout. Please try again.');
        }
    }

    private function validateWorkoutData(array $data): bool {
        return isset($data['name'], $data['exercises'], $data['duration']) 
            && !empty($data['name']) 
            && is_array($data['exercises']) && count($data['exercises']) > 0
            && is_numeric($data['duration']) && $data['duration'] > 0;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error.php');
    }

    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }
}
?>