<?php

require_once '../../models/UserModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    $sessionParams = session_get_cookie_params();
    session_set_cookie_params(
        $sessionParams["lifetime"],
        $sessionParams["path"],
        $sessionParams["domain"],
        true, // secure
        true  // httponly
    );
    session_start();

    // Initialize CSRF token if not set
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

class LoginController extends BaseController {
    private UserModel $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function login(array $data): void {
        // CSRF token validation
        if (!$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
            return $this->setErrorMessage('Invalid security token. Please try again.');
        }

        // Regenerate session ID and CSRF token for enhanced security
        $this->regenerateSession();

        // Validate and sanitize input
        $email = $this->sanitizeEmail($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($this->isInputInvalid($email, $password)) {
            return; // Error message already set in isInputInvalid
        }

        // Rate limiting to prevent brute-force attacks
        if ($this->isRateLimited($email)) {
            return $this->setErrorMessage('Too many login attempts. Please try again later.');
        }

        // Retrieve user by email using prepared statements
        $user = $this->userModel->getUserByEmail($email);
        
        if (!$user) {
            $this->logLoginAttempt($email, false);
            return $this->setErrorMessage('Invalid credentials. Please check your email and password.');
        }

        // Verify password
        if (!$this->verifyPassword($password, $user['password'])) {
            $this->logLoginAttempt($email, false);
            return $this->setErrorMessage('Invalid credentials. Please check your email and password.');
        }

        // Store user information in session
        $this->storeUserSession($user);

        // Handle "Remember Me" functionality
        if (!empty($data['remember_me'])) {
            $this->setRememberMeCookie($user['id']);
        }
        
        // Set success message and redirect
        $_SESSION['success_message'] = 'Login successful! Welcome back!';
        header('Location: ../../views/user/profile.php?id=' . $_SESSION['user_id']);
        exit();
    }

    protected function isValidCsrfToken(string $token): bool {
        return !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
    }

    private function regenerateSession(): void {
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function sanitizeEmail(string $email): string {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    private function isInputInvalid(string $email, string $password): bool {
        if (empty($email) || empty($password)) {
            $this->setErrorMessage('Both email and password are required.');
            return true; // Return true to indicate invalid input
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorMessage('Please enter a valid email address.');
            return true; // Return true to indicate invalid input
        }

        return false; // Input is valid
    }

    private function verifyPassword(string $password, string $hashedPassword): bool {
        return password_verify($password, $hashedPassword);
    }

    private function storeUserSession(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
    }

    private function setErrorMessage(string $message): void {
        $_SESSION['error_message'] = $message;
        header('Location: ../../views/auth/login.php');
        exit();
    }

    private function setRememberMeCookie(int $userId): void {
        $token = bin2hex(random_bytes(16));
        setcookie('remember_me', $token, time() + (86400 * 30), "/", "", true, true);
        // Store the token in the database associated with the user ID
        $this->userModel->storeRememberMeToken($userId, $token);
    }

    private function logLoginAttempt(string $email, bool $success): void {
        $status = $success ? 'successful' : 'failed';
        error_log("Login attempt for email: $email was $status.");
    }

    private function isRateLimited(string $email): bool {
        // Implement rate limiting logic here
        // For example, check the number of failed attempts in a given time frame
        return false; // Placeholder return value
    }
}