<?php
require_once '../../models/WorkoutModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class CustomController extends BaseController {
    private WorkoutModel $workoutModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
    }

    public function createCustomWorkout(array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please log in to create a custom workout.');
            return;
        }

        if (!$this->validateWorkoutData($data)) {
            $this->redirectWithError('Validation failed. Please check the input data.');
            return;
        }

        try {
            $this->workoutModel->createWorkout($data);
            $_SESSION['success_message'] = 'Custom workout created successfully.';
            $this->redirect('../../views/workout/custom.php');   
        } catch (Exception $e) {
            error_log('Creation failed: ' . $e->getMessage());
            $this->redirectWithError('Failed to create custom workout. Please try again.');
        }
    }

    private function validateWorkoutData(array $data): bool {
        $requiredFields = ['type', 'duration', 'intensity', 'exercises', 'repetitions'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
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