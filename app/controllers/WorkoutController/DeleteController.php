<?php
require_once '../../models/WorkoutModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class DeleteController extends BaseController {
    private WorkoutModel $workoutModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
    }

    public function delete(int $id): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please log in to delete workouts.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid workout ID.');
            return;
        }

        if (!$this->confirmDeletion()) {
            $this->redirectWithError('Deletion not confirmed.');
            return;
        }

        try {
            if ($this->workoutModel->deleteWorkout($id)) {
                $_SESSION['success_message'] = 'Workout deleted successfully.';
                $this->redirect('../../views/workout/index.php');
            } else {
                $this->redirectWithError('Failed to delete workout. Please try again.');
            }
        } catch (Exception $e) {
            error_log("Deletion failed for workout ID $id: " . $e->getMessage());
            $this->redirectWithError('Failed to delete workout. Please try again.');
        }
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    private function confirmDeletion(): bool {
        return isset($_POST['confirm']) && strtolower($_POST['confirm']) === 'yes';
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