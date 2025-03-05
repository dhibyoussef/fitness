<?php
namespace App\Controllers\AuthController;
use App\Controllers\BaseController;
use PDO;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Models\UserModel;
use Random\RandomException;

require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

class LoginController extends BaseController {
    private UserModel $userModel;
    protected Logger $logger;
    private const RATE_LIMIT_ATTEMPTS = 5;
    private const RATE_LIMIT_WINDOW = 900; // 15 minutes

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->logger = new Logger('LoginController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log'));
    }

// app/controllers/AuthController/LoginController.php
    public function login(array $data): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->logger->warning("Invalid request method", ['ip' => $_SERVER['REMOTE_ADDR']]);
                $this->setFlashMessage('error', 'Invalid request.');
                $this->redirect('/auth/login');
                return;
            }

            $email = $this->sanitizeEmail($data['email'] ?? '');
            $password = trim($data['password'] ?? '');
            $this->logger->info("Login attempt for email: $email");

            if ($this->isInputInvalid($email, $password)) {
                return;
            }

            if ($this->isRateLimited($email)) {
                $this->logger->warning("Rate limit exceeded", ['email' => $email]);
                $this->setFlashMessage('error', 'Too many login attempts. Please wait 15 minutes.');
                $this->redirect('/auth/login');
                return;
            }

            $user = $this->userModel->getUserByEmail($email);
            if (!$user) {
                $this->logger->warning("User not found for email: $email");
                $this->logLoginAttempt($email, false);
                $this->setFlashMessage('error', 'Invalid credentials.');
                $this->redirect('/auth/login');
                return;
            }

            if (!$this->verifyPassword($password, $user['password'])) {
                $this->logger->warning("Password verification failed for email: $email");
                $this->logLoginAttempt($email, false);
                $this->setFlashMessage('error', 'Invalid credentials.');
                $this->redirect('/auth/login');
                return;
            }

            $this->logger->info("User authenticated, storing session for email: $email");
            $this->storeUserSession($user);
            if (!empty($data['remember_me'])) {
                $this->setRememberMeCookie($user['id']); // Exception caught here doesn’t stop login
            }

            $this->logger->info("Login successful", ['user_id' => $user['id'], 'email' => $email, 'role' => $user['role']]);
            $this->setFlashMessage('success', "Welcome back, {$user['username']}!");
            if ($user['role'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/user/profile');
            }
        } catch (Exception $e) {
            $this->logger->error("Login error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', 'An unexpected error occurred.');
            $this->redirect('/auth/login');
        }
    }

    private function isRateLimited(string $email): bool {
        $query = "SELECT COUNT(*) FROM login_attempts WHERE email = :email AND attempt_time > NOW() - INTERVAL :window SECOND";
        $stmt = $this->pdo->prepare($query); // Error: $this->pdo is null
        $stmt->execute(['email' => $email, 'window' => self::RATE_LIMIT_WINDOW]);
        return (int)$stmt->fetchColumn() >= self::RATE_LIMIT_ATTEMPTS;
    }

    private function logLoginAttempt(string $email, bool $success): void {
        $query = "INSERT INTO login_attempts (email, attempt_time, success) VALUES (:email, NOW(), :success)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email, 'success' => (int)$success]);
    }

    /**
     * @throws Exception
     */
    private function setRememberMeCookie(int $userId): void {
        try {
            $token = bin2hex(random_bytes(32));
            $hashedToken = password_hash($token, PASSWORD_BCRYPT);
            setcookie('remember_me', "$userId:$token", time() + (86400 * 30), "/", "", true, true);
            $this->userModel->storeRememberMeToken($userId, $hashedToken);
        } catch (Exception $e) {
            $this->logger->error("Failed to set remember me cookie", [
                'user_id' => $userId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don’t re-throw; let login proceed without remember-me functionality
        }
    }

    private function storeUserSession(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
    }

    private function verifyPassword(string $password, string $hashedPassword): bool {
        return password_verify($password, $hashedPassword);
    }

    private function isInputInvalid(string $email, string $password): bool {
        if (empty($email) || empty($password)) {
            $this->setFlashMessage('error', 'Email and password are required.');
            $this->redirect('/auth/login');
            return true;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlashMessage('error', 'Invalid email format.');
            $this->redirect('/auth/login');
            return true;
        }
        return false;
    }

    private function sanitizeEmail(string $email): string {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

}