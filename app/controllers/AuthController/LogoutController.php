<?php
namespace App\Controllers\AuthController;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../../config/database.php';

use App\Controllers\BaseController;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;
use App\Models\UserModel;

class LogoutController extends BaseController {
    private UserModel $userModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->logger = new Logger('LogoutController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
    }

    public function logout(): void {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJsonResponse('error', 'Invalid request method.', 'window-shake', true);
                return;
            }

            if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                $this->logger->warning("CSRF token validation failed", [
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'ip' => $_SERVER['REMOTE_ADDR']
                ]);
                $this->sendJsonResponse('error', 'Invalid security token.', 'window-shake', true);
                return;
            }

            if (!isset($_SESSION['user_id'])) {
                $this->sendJsonResponse('error', 'Not logged in.', 'window-shake', true);
                return;
            }

            $userId = $_SESSION['user_id'];
            if (isset($_POST['logout_all_devices']) && $_POST['logout_all_devices'] === 'on') { // Check for checkbox value
                $this->userModel->invalidateAllTokens($userId);
                $this->logger->info("Invalidated all sessions", ['user_id' => $userId]);
            }

            session_unset();
            session_destroy();
            setcookie(session_name(), '', time() - 3600, '/');

            $this->logger->info("User logged out", ['user_id' => $userId, 'ip' => $_SERVER['REMOTE_ADDR']]);
            $this->sendJsonResponse('success', 'Logged out successfully.', 'window-fade', false, '/auth/login');
        } catch (Exception $e) {
            $this->logger->error("Logout error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->sendJsonResponse('error', 'Logout failed: ' . $e->getMessage(), 'window-shake', true);
        }
    }

    #[NoReturn] private function sendJsonResponse(string $status, string $message, string $animation, bool $stayOnPage, string $redirect = ''): void {
        echo json_encode([
            'status' => $status,
            'message' => htmlspecialchars($message),
            'animation' => $animation,
            'stayOnPage' => $stayOnPage,
            'redirect' => $redirect
        ]);
        exit;
    }
}