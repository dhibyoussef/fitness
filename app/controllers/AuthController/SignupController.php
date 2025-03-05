<?php
namespace App\Controllers\AuthController;

use App\Controllers\BaseController;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;
use App\Models\UserModel;

class SignupController extends BaseController {
    private UserModel $userModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->logger = new Logger('SignupController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::DEBUG));
    }

    public function signup(array $data): void {
        $this->logger->info("Signup method called", [
            'method' => $_SERVER['REQUEST_METHOD'],
            'data' => $data
        ]);

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->logger->warning("Invalid request method", ['method' => $_SERVER['REQUEST_METHOD']]);
                $this->setFlashMessage('error', 'Invalid request.');
                $this->redirect('/auth/signup');
                return;
            }

            $name = $this->sanitizeInput($data['name'] ?? '');
            $email = $this->sanitizeEmail($data['email'] ?? '');
            $password = trim($data['password'] ?? '');

            $this->logger->info("Input received", ['name' => $name, 'email' => $email]);

            if ($this->isInputInvalid($name, $email, $password)) {
                $this->logger->info("Input validation failed");
                return;
            }

            if (!$this->isPasswordStrong($password)) {
                $this->logger->info("Password validation failed");
                $this->setFlashMessage('error', 'Password must be 8+ characters with uppercase, lowercase, number, and special character.');
                $this->redirect('/auth/signup');
                return;
            }

            if ($this->userModel->getUserByEmail($email)) {
                $this->logger->info("Email already exists", ['email' => $email]);
                $this->setFlashMessage('error', 'Email already exists.');
                $this->redirect('/auth/signup');
                return;
            }

            $userData = [
                'username' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                'role' => 'user',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];

            if ($this->userModel->createUser($userData)) {
                $userId = $this->userModel->getLastInsertedId();
                $this->storeUserSession([
                    'id' => $userId,
                    'email' => $email,
                    'username' => $name,
                    'role' => 'user'
                ]);
                $this->sendVerificationEmail($email);
                $this->logger->info("User signed up", ['user_id' => $userId, 'email' => $email]);
                $this->setFlashMessage('success', 'Signup successful! Check your email to verify.');
                $this->redirect('/dashboard');
            } else {
                throw new Exception('User creation failed.');
            }
        } catch (Exception $e) {
            $this->logger->error("Signup error", [
                'message' => $e->getMessage(),
                'email' => $email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', 'Failed to create account.');
            $this->redirect('/auth/signup');
        }
    }

        private function sendVerificationEmail(string $email): void {
            $token = bin2hex(random_bytes(16));
            $this->userModel->storeVerificationToken($email, $token);
            $verificationUrl = BASE_URL . "/verify?token=$token";
            $subject = "Verify Your Fitness Tracker Account";
            $message = "Click here to verify your account: $verificationUrl";
            $headers = "From: noreply@fitnesstracker.com\r\n";
            if (!mail($email, $subject, $message, $headers)) {
                $this->logger->warning("Failed to send verification email", ['email' => $email]);
            }
        }

        private function isPasswordStrong(string $password): bool {
            return strlen($password) >= 8 &&
                preg_match('/[A-Z]/', $password) &&
                preg_match('/[a-z]/', $password) &&
                preg_match('/[0-9]/', $password) &&
                preg_match('/[\W_]/', $password);
        }

        private function isInputInvalid(string $name, string $email, string $password): bool {
            if (empty($name) || empty($email) || empty($password)) {
                $this->setFlashMessage('error', 'All fields are required.');
                $this->redirect('/auth/signup');
                return true;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlashMessage('error', 'Invalid email format.');
                $this->redirect('/auth/signup');
                return true;
            }
            if (strlen($name) < 3 || strlen($name) > 50) {
                $this->setFlashMessage('error', 'Username must be 3-50 characters.');
                $this->redirect('/auth/signup');
                return true;
            }
            return false;
        }

        private function sanitizeInput(string $input): string {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }

        private function sanitizeEmail(string $email): string {
            return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
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

}