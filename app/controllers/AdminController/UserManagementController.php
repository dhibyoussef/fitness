<?php
require_once '../../models/UserModel.php';
require_once '../../controllers/BaseController.php';

class UserManagementController extends BaseController {
    private UserModel $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function index(): void {
        try {
            $this->verifyAdminAccess();
            $users = $this->userModel->getAllUsers();
            $this->renderView('../../views/admin/user_management.php', ['users' => $users]);
        } catch (Exception $e) {
            $this->handleError('Error fetching users: ' . $e->getMessage());
        }
    }

    public function viewUserDetails(int $id): void {
        try {
            $this->verifyAdminAccess();
            $user = $this->userModel->getUserById($id);
            if ($user) {
                $this->renderView('../../views/admin/user_details.php', ['user' => $user]);
            } else {
                $this->handleError('User not found.');
            }
        } catch (Exception $e) {
            $this->handleError('Error fetching user details: ' . $e->getMessage());
        }
    }

    public function bulkActivateUsers(array $userIds): void {
        try {
            $this->verifyAdminAccess();
            foreach ($userIds as $id) {
                $this->userModel->activateUser($id);
            }
            $_SESSION['success'] = 'Users activated successfully.';
            $this->redirect('../../views/admin/user_management.php');
        } catch (Exception $e) {
            $this->handleError('Error activating users: ' . $e->getMessage());
        }
    }

    private function handleError(string $message): void {
        error_log($message);
        $_SESSION['error'] = $message;
        $this->redirectToErrorPage($message);
    }

    private function renderView(string $viewPath, array $data): void {
        if ($this->isViewReadable($viewPath)) {
            extract($data, EXTR_SKIP);
            include $viewPath;
        } else {
            $this->handleError('View not found: ' . $viewPath);
        }
    }

    private function isViewReadable(string $viewPath): bool {
        return is_readable($viewPath);
    }

    private function redirectToErrorPage(string $message): void {
        header("Location: ../../views/error/error.php?message=" . urlencode($message));
        exit();
    }

    private function verifyAdminAccess(): void {
        if (!$this->isAdmin()) {
            $this->handleError('Unauthorized access attempt.');
            $this->redirect('../../views/auth/login.php');
        }
    }

    private function isAdmin(): bool {
        return $_SESSION['user_role'] === 'admin';
    }
}