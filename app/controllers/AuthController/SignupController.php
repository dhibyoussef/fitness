<?php
require_once '../../models/UserModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

// Start a secure session if not already started
if (session_status() === PHP_SESSION_NONE) {
    $sessionParams = session_get_cookie_params();
    session_set_cookie_params(
        $sessionParams["lifetime"],
        $sessionParams["path"],
        $sessionParams["domain"],
        true, // secure
        true  // httponly
    );

    if (session_start() === false) {
        throw new Exception('Unable to start session.');
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

class SignupController extends BaseController {
    private UserModel $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function signup(array $data ): void {
        // Validate CSRF token
        if (!$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
            return $this->setErrorMessage('Invalid or missing security token. Please try again.');
        }

        // Check if email already exists
        if ($this->userModel->getUserByEmail($data['email'])) {
            return $this->setErrorMessage('Email already exists. Please use a different email.');
        }

        // Validate and sanitize input
        $name = $this->sanitizeInput($data['name'] ?? '');
        $email = $this->sanitizeEmail($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        // Validate required fields
        if ($this->isInputInvalid($name, $email, $password)) {
            return; // Error message already set in isInputInvalid
        }

        // Validate password strength
        if (!$this->isPasswordStrong($password)) {
            return $this->setErrorMessage('Password must be at least 8 characters long and contain at least one uppercase letter and one number.');
        }

        // Create user
        $userData = [
            'username' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT) // Hash the password before storing
        ];

        if ($this->userModel->createUser ($userData)) {
            // Automatically log the user in
            $_SESSION['user_id'] = $this->userModel->getLastInsertId();
            $_SESSION['username'] = $name;

            // Send verification email
            $_SESSION['success_message'] = 'Signup successful! Please check your email to verify your account.';
            header('Location: ../../views/dashboard.php');
            exit();
        } else {
            return $this->setErrorMessage('Failed to create account. Please try again.');
        }
    }

    protected function isValidCsrfToken(string $token): bool {
        return !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
    }

    private function sanitizeInput(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    private function sanitizeEmail(string $email): string {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    private function isInputInvalid(string $name, string $email, string $password): bool {
        if (empty($name) || empty($email) || empty($password)) {
            $this->setErrorMessage('All fields are required.');
            return true;
        }
        return false;
    }

    private function isPasswordStrong(string $password): bool {
        return strlen($password) >= 8 && preg_match('/[A-Z]/', $password) && preg_match('/[0-9]/', $password);
    }

    private function setErrorMessage(string $message): void {
        $_SESSION['error_message'] = $message;
        header('Location: ../../views/auth/signup.php');
        exit();
    }
}