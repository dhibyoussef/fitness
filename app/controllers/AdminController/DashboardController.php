<?php
require_once '../../../config/database.php'; 
require_once '../../controllers/BaseController.php';
require_once '../../models/AdminModel.php'; 

class DashboardController extends BaseController {
    private AdminModel $adminModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->adminModel = new AdminModel($pdo); 
        $this->requireAuth(); // Ensure only authorized users can access
    }

    public function index(): void {
        try {
            $this->checkAdminPermissions(); // Ensure the user has admin permissions
            $dashboardData = $this->getDashboardStatistics();
            $this->renderView('../../views/admin/dashboard.php', $dashboardData);
        } catch (Exception $e) {
            $this->logError("Error fetching dashboard data: " . $e->getMessage());
            $this->handleViewError(); 
        }
    }

    public function getDashboardStatistics(): array {
        return [
            'userCount' => $this->adminModel->getUserCount(),
            'activeUsers' => $this->adminModel->getActiveUserCount(),
            'workoutStats' => $this->adminModel->getWorkoutStats(),
            'registrationTrends' => $this->adminModel->getRegistrationTrends(),
            'userGrowth' => $this->adminModel->calculateUserGrowth(),
            'averageDuration' => $this->adminModel->getAverageDuration(),
            'totalWorkouts' => $this->adminModel->getTotalWorkouts()
        ];
    }

    public function getAverageDuration(): float {
        return $this->adminModel->getAverageDuration();
    }
    public function getTotalWorkouts(): int {
        return $this->adminModel->getTotalWorkouts();
    }
    
    
    private function renderView(string $viewPath, array $data): void {
        if ($this->isViewReadable($viewPath)) {
            extract($data, EXTR_SKIP); // Extract data for use in the view, avoiding variable name collisions
            include $viewPath; 
        } else {
            $this->logError("View not found: " . $viewPath);
            $this->handleViewError();
        }
    }

    private function isViewReadable(string $viewPath): bool {
        return is_readable($viewPath);
    }

    private function logError(string $message): void {
        error_log($message);
    }

    private function handleViewError(): void {
        http_response_code(404); 
        echo "<h1>Error 404: Not Found</h1>";
        echo "<p>The requested view could not be found. Please check the URL or contact support.</p>";
        exit();
    }

    private function checkAdminPermissions(): void {
        if (!$this->isAdmin()) {
            $this->logError("Unauthorized access attempt to admin dashboard.");
            $this->redirect('../../views/auth/login.php');
        }
    }

    private function isAdmin(): bool {
        return $_SESSION['user_role'] === 'admin';
    }
}