<?php
require_once '../../models/ProgressModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class DeleteController extends BaseController {
    private ProgressModel $progressModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
    }

    public function delete(int $id): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please login to delete progress entries.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid progress ID provided. Please try again.');
            return;
        }

        // Verify the progress entry exists
        if (!$this->progressModel->exists($id)) {
            $this->redirectWithError('Progress entry not found. It may have been already deleted.');
            return;
        }

        // Check for deletion confirmation
        if (!$this->confirmDeletion()) {
            $this->redirectWithError('Please confirm that you want to delete this progress entry.');
            return;
        }

        try {
            if ($this->progressModel->deleteProgress($id)) {
                error_log("Progress entry {$id} deleted successfully by user {$_SESSION['user_id']}");
                $_SESSION['success_message'] = 'Progress entry deleted successfully. You can add new progress entries anytime.';
                $this->redirect('../../views/progress/index.php');
            } else {
                $this->redirectWithError('Unable to delete progress entry. Please try again.');
            }
        } catch (Exception $e) {
            error_log('Progress deletion failed: ' . $e->getMessage());
            $this->redirectWithError('An error occurred while deleting the progress entry. Please try again later.');
        }
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0 || !filter_var($id, FILTER_VALIDATE_INT);
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    private function confirmDeletion(): bool {
        return isset($_POST['confirm']) && strtolower(trim($_POST['confirm'])) === 'yes';
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        header('Location: ../../views/error/error.php');
        exit();
    }

    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }
}