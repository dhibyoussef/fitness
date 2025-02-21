<?php
require_once '../../models/ProgressModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class ShowController extends BaseController {
    private ProgressModel $progressModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
    }

    public function show(int $id): void {
        if (!$this->isUserAuthorized()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid progress ID.');
            return;
        }

        $progress = $this->progressModel->getProgressById($id);
        if ($progress) {
            $progress = $this->formatProgressMetrics($progress);
            $this->renderView('../../views/progress/show.php', ['progress' => $progress]);
        } else {
            $this->redirectWithError('No progress found for the given ID.');
        }
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error/error.php');
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

    private function formatProgressMetrics(array $progress): array {
        $progress['weight_change'] = $progress['end_weight'] - $progress['start_weight'];
        $progress['workout_achievements'] = $this->formatWorkoutAchievements($progress['workouts']);
        return $progress;
    }

    private function formatWorkoutAchievements(array $workouts): string {
        return implode(', ', $workouts);
    }

    private function isUserAuthorized(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] === $this->progressModel->getUserId();
    }
}