<?php
// app/controllers/UserController/ProfileController.php
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ProfileController extends BaseController {
    private UserModel $userModel;
    private ProgressModel $progressModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->logger = new Logger('ProfileController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function show(int $id): void {
        try {
            if ($id <= 0 || $id !== (int)$_SESSION['user_id']) {
                throw new Exception('You can only view your own profile.');
            }

            $user = $this->userModel->getUserById($id);
            if (!$user) {
                throw new Exception('User not found.');
            }

            $progress = $this->progressModel->getOverallProgressStatistics($id);
            $this->render(__DIR__ . '/../../views/user/profile.php', [
                'user' => $this->formatUserData($user),
                'progress' => $progress,
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Profile fetch error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/auth/login');
        }
    }

    private function formatUserData(array $user): array {
        $user['display_name'] = ucwords($user['username']);
        $user['joined'] = date('F j, Y', strtotime($user['created_at']));
        return $user;
    }
}