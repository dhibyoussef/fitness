<?php
// app/controllers/UserController/DeleteController.php
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DeleteController extends BaseController {
    private UserModel $userModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->logger = new Logger('UserDeleteController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function delete(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid user ID.');
            }

            if ($id !== (int)$_SESSION['user_id']) {
                throw new Exception('You can only delete your own account.');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->render(__DIR__ . '/../../views/user/delete.php', [
                    'id' => $id,
                    'csrf_token' => $this->generateCsrfToken()
                ]);
                return;
            }

            if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token.');
            }

            if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'yes') {
                throw new Exception('Deletion not confirmed.');
            }

            if ($this->userModel->deleteUser($id)) {
                $this->logger->info("User deleted", ['id' => $id]);
                session_unset();
                session_destroy();
                setcookie(session_name(), '', time() - 3600, '/');
                $this->setFlashMessage('success', 'Account deleted successfully.');
                $this->redirect('/');
            } else {
                throw new Exception('Failed to delete account.');
            }
        } catch (Exception $e) {
            $this->logger->error("Deletion error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/user/profile');
        }
    }
}