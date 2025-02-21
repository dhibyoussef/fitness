<?php
require_once '../../models/WorkoutModel.php';
 require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class EditController extends BaseController {
    private WorkoutModel $workoutModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
    }

    public function edit(int $id, array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please log in to edit workouts.');
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
            if (!$this->workoutModel->updateWorkout($id, $data)) {
                throw new Exception("Failed to update workout with ID: $id");
            }
            $_SESSION['success_message'] = 'Workout updated successfully.';
            $this->redirect('../../views/workout/index.php'); // Successfully updated, redirect to index
        } catch (Exception $e) {
            $this->handleException($e); // Handle exceptions uniformly
        }
    }

    private function validateWorkoutData(array $data): bool {
        return isset($data['type'], $data['duration'], $data['calories']) 
            && !empty($data['type']) 
            && !empty($data['duration']) 
            && is_numeric($data['calories']) 
            && $data['calories'] > 0;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
    }

    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }
    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error.php');
    }

    private function handleException(Exception $exception): void {
        error_log($exception->getMessage()); // Log the error
        $this->redirectWithError('Failed to update workout. Please try again.');
    }
}