<?php
require_once '../../models/WorkoutModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class IndexController extends BaseController {
    private WorkoutModel $workoutModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
    }

    public function index(int $page = 1, string $filter = ''): void {
        if (!$this->isUseAuthenticated()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        $pageSize = 10; // Number of records per page
        $offset = ($page - 1) * $pageSize;

        try {
            $workouts = $this->workoutModel->getFilteredWorkouts(json_decode($filter, true), $offset, $pageSize);
            $totalWorkouts = (int) $this->workoutModel->countFilteredWorkouts(json_decode($filter, true));
            $totalPages = ceil($totalWorkouts / $pageSize);

            $this->renderView('../../views/workout/index.php', [
                'workouts' => $workouts,
                'currentPage' => $page,
                'totalPages' => $totalPages
            ]);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    private function renderView(string $viewPath, array $data): void {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            $this->redirectWithError("View template not found at: $viewPath");
            return;
        }
        extract($data);
        include $viewPath;
    }

    private function handleError(Exception $exception): void {
        error_log($exception->getMessage());
        $this->redirectWithError('An error occurred while processing your request.');
    }

    private function isUseAuthenticated(): bool {
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