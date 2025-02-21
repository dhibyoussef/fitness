<?php
require_once __DIR__ . '/../../models/WorkoutModel.php';
require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

class StatisticsController extends BaseController {
    private WorkoutModel $workoutModel;
    private NutritionModel $nutritionModel;
    private ProgressModel $progressModel;
    private UserModel $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->workoutModel = new WorkoutModel($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function index(): void {
        try {
            $this->authenticateAdmin();
            $statistics = $this->getDashboardStatistics();
            $this->renderStatisticsView('../../views/admin/statistics.php', $statistics);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    public function getDashboardStatistics(): array {
        $statistics = [];
        
        try {
            // Get user registration statistics
            $registrationData = $this->userModel->getRegistrationStatistics();
            $statistics['registrationLabels'] = array_keys($registrationData);
            $statistics['registrationData'] = array_values($registrationData);

            // Get active user statistics
            $activeUserData = $this->userModel->getActiveUserStatistics();
            $statistics['activeUserLabels'] = array_keys($activeUserData);
            $statistics['activeUserData'] = array_values($activeUserData);

            // Get workout statistics
            $statistics['workoutStats'] = $this->workoutModel->getOverallWorkoutStatistics();

            // Get nutrition statistics
            $statistics['nutritionStats'] = $this->nutritionModel->getOverallNutritionStatistics();

            // Get progress statistics
            $statistics['progressStats'] = $this->progressModel->getOverallProgressStatistics();

        } catch (Exception $e) {
            $this->handleError($e);
        }

        return $statistics;
    }

    private function authenticateAdmin() {
        if (!$this->isAdminAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Admins only.');
            exit;
        }
    }

    private function isAdminAuthenticated(): bool {
        return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    private function redirectWithError(string $message) {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error.php');
    }

    protected function redirect($url) {
        header("Location: $url");
        exit();
    }

    private function handleError(Exception $exception) {
        error_log($exception->getMessage());
        $this->redirectWithError('An error occurred while processing your request.');
    }

    private function renderStatisticsView(string $viewPath, array $data): void {
        if ($this->isViewReadable($viewPath)) {
            extract($data, EXTR_SKIP);
            include $viewPath;
        } else {
            $this->handleError(new Exception("View not found: " . $viewPath));
        }
    }

    private function isViewReadable(string $viewPath): bool {
        return is_readable($viewPath);
    }
}