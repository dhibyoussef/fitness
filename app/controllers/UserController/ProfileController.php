<?php
namespace App\Controllers\UserController;

require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use App\Controllers\BaseController;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;
use App\Models\UserModel;
use App\Models\ProgressModel;

class ProfileControllerU extends BaseController {
    private UserModel $userModel;
    private ProgressModel $progressModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->logger = new Logger('UserProfileController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function show(int $id): void {
        try {
            if ($id !== (int)$_SESSION['user_id']) {
                throw new Exception('You can only view your own profile.');
            }

            $user = $this->userModel->getUserById($id);
            if (!$user) {
                throw new Exception('User not found.');
            }

            $progress = [
                'avg_weight' => $this->progressModel->getAverageProgress('weight', $id),
                'avg_body_fat' => $this->progressModel->getAverageProgress('body_fat', $id),
                'avg_muscle_mass' => $this->progressModel->getAverageProgress('muscle_mass', $id)
            ];

            $this->render('user/profile', [
                'pageTitle' => 'User Profile',
                'user' => $user,
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
            $this->redirect('/dashboard');
        }
    }
}