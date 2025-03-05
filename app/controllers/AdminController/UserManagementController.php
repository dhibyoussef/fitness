<?php
namespace App\Controllers\AdminController;

require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use App\Controllers\BaseController;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;
use App\Models\UserModel;

class UserManagementController extends BaseController {
    private UserModel $userModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->logger = new Logger('UserManagementController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function index(int $page = 1, int $perPage = 10, string $search = ''): void {
        try {
            $this->checkAdminPermissions();
            $offset = max(0, ($page - 1) * $perPage);
            $users = $this->userModel->getAllUsers($offset, $perPage, $search);
            $totalUsers = $this->userModel->getUserCount($search);

            $this->render('admin/user_management', [ // Fixed: Use relative path
                'pageTitle' => 'User Management - Fitness Tracker',
                'users' => $users,
                'currentPage' => $page,
                'totalPages' => max(1, ceil($totalUsers / $perPage)),
                'perPage' => $perPage,
                'search' => htmlspecialchars($search),
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("User fetch error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', "Failed to load users: " . $e->getMessage());
            $this->redirect('/admin/dashboard');
        }
    }

    public function viewUserDetails(int $id): void {
        try {
            $this->checkAdminPermissions();
            $user = $this->userModel->getUserById($id);
            if ($user) {
                $this->render('admin/user_details', [
                    'pageTitle' => 'User Details - Fitness Tracker',
                    'user' => $user,
                    'csrf_token' => $this->generateCsrfToken(),
                    'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                ]);
            } else {
                $this->logger->warning("User not found", ['id' => $id]);
                $this->setFlashMessage('error', "User ID $id not found.");
                $this->redirect('/admin/user_management');
            }
        } catch (Exception $e) {
            $this->logger->error("User details error", [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', "Failed to load user details: " . $e->getMessage());
            $this->redirect('/admin/user_management');
        }
    }

    public function bulkActivateUsers(): void {
        try {
            $this->checkAdminPermissions();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception("Invalid request or security token.");
            }

            $userIds = $_POST['user_ids'] ?? [];
            if (!is_array($userIds) || empty($userIds)) {
                throw new Exception("No users selected for activation.");
            }

            $this->pdo->beginTransaction();
            $activated = 0;
            foreach ($userIds as $id) {
                if ($this->userModel->activateUser((int)$id)) {
                    $activated++;
                }
            }
            $this->pdo->commit();
            $this->logger->info("Bulk user activation", ['count' => $activated, 'user_ids' => $userIds]);
            $this->setFlashMessage('success', "$activated users activated successfully.");
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->logger->error("Bulk activation error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', "Failed to activate users: " . $e->getMessage());
        }
        $this->redirect('/admin/user_management');
    }

    private function checkAdminPermissions(): void {
        if (!$this->isAdmin()) {
            $this->logger->warning("Unauthorized access attempt", [
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            $this->setFlashMessage('error', 'Admin access required.');
            $this->redirect('/auth/login');
        }
    }

    public function isAdmin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}