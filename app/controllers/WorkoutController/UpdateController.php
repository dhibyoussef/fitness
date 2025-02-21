<?php
require_once '../../models/WorkoutModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class UpdateController extends BaseController {
    private WorkoutModel $workoutModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
    }

    public function update(int $id, array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid workout ID.');
            return;
        }

        if (!$this->validateWorkoutData($data)) {
            $this->redirectWithError('Validation failed. Please check the input data.');
            return;
        }

        try {
            if ($this->workoutModel->updateWorkout($id, $data)) {
                $_SESSION['success_message'] = 'Workout updated successfully.';
                $this->redirect('../../views/workout/index.php');
            } else {
                $this->redirectWithError('No changes were detected in the workout.');
            }
        } catch (Exception $e) {
            error_log('Update failed: ' . $e->getMessage());
            $this->redirectWithError('Failed to update workout. Please try again.');
        }
    }

    private function validateWorkoutData(array $data): bool {
        return isset($data['name'], $data['duration'], $data['calories']) 
            && !empty($data['name']) 
            && is_numeric($data['duration']) && $data['duration'] > 0
            && is_numeric($data['calories']) && $data['calories'] > 0;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
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