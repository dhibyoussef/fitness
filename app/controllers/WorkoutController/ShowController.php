<?php
require_once '../../models/WorkoutModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class ShowController extends BaseController {
    private WorkoutModel $workoutModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
    }

    public function show(int $id): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid workout ID.');
            return;
        }

        $workout = $this->workoutModel->getWorkoutById($id);
        if (!$workout) {
            $this->redirectWithError('Workout not found.');
            return;
        }

        $workout = $this->formatWorkoutData($workout);
        $this->renderView('../../views/workout/show.php', ['workout' => $workout]);
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    private function formatWorkoutData(array $workout): array {
        $workout['formatted_duration'] = gmdate("H:i:s", $workout['duration']);
        $workout['formatted_calories'] = number_format($workout['calories']) . ' kcal';
        return $workout;
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error.php');
    }

    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }
    private function renderView(string $viewPath, array $data): void {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            $this->redirectWithError("View template not found at: $viewPath");
            return;
        }
        extract($data);
        include $viewPath;
    }
}