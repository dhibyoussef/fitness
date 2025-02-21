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
            $this->authenticateAdmin(); // Ensure the user is an admin
            $statistics = $this->getDashboardStatistics();
            $this->renderView('../../views/admin/statistics.php', $statistics);
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
            $statistics['activeUser Labels'] = array_keys($activeUserData);
            $statistics['activeUser Data'] = array_values($activeUserData);

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

    private function authenticateAdmin(): void {
        if (!$this->isAdminAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Admins only.');
            exit;
        }
    }

    private function isAdminAuthenticated(): bool {
        return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error.php');
    }
    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }

    private function handleError(Exception $exception): void {
        error_log($exception->getMessage());
        $this->redirectWithError('An error occurred while processing your request.');
    }

    private function renderView(string $viewPath, array $data): void {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            $this->redirectWithError("View template not found: $viewPath");
            return;
        }
        extract($data);
        include $viewPath;
    }
}