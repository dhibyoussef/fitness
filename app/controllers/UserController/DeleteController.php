<?php
require_once '../../models/UserModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class DeleteController extends BaseController {
    private UserModel $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function delete(int $id): void {
        if (!$this->isUserAuthorized($id)) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid user ID.');
            return;
        }

        if (!$this->confirmDeletion()) {
            $this->redirectWithError('Deletion not confirmed.');
            return;
        }

        try {
            if ($this->userModel->deleteUser ($id)) {
                $_SESSION['success_message'] = 'User  deleted successfully.';
                $this->redirect('../../../index.php');
            } else {
                $this->redirectWithError('Failed to delete user. Please try again.');
            }
        } catch (Exception $e) {
            error_log('User  deletion failed: ' . $e->getMessage());
            $this->redirectWithError('An error occurred while deleting the user. Please try again later.');
        }
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
    }

    private function isUserAuthorized(int $id): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] === $id;
    }

    private function confirmDeletion(): bool {
        return isset($_POST['confirm']) && strtolower(trim($_POST['confirm'])) === 'yes';
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        header('Location: ../../views/error.php');
        exit();
    }

    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }
}