<?php
class BaseController {
    protected PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
        $this->initializeSession();
    }

    protected function loadModel(string $model): object {
        $modelPath = "../models/{$model}.php";
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model($this->db);
        } else {
            throw new Exception("Model file '{$model}' not found.");
        }
    }

    protected function render(string $view, array $data = []): void {
        $viewPath = "../views/{$view}.php";
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            throw new Exception("View file '{$view}' not found.");
        }
    }

    protected function redirect(string $url): void {
        header("Location: $url");
        exit();
    }

    protected function isAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth(): void {
        if (!$this->isAuthenticated()) {
            $this->redirect('/app/views/auth/login.php');
        }
    }

    protected function setFlashMessage(string $key, string $message): void {
        $_SESSION['flash_messages'][$key] = $message;
    }

    protected function getFlashMessage(string $key): ?string {
        if (isset($_SESSION['flash_messages'][$key])) {
            $message = $_SESSION['flash_messages'][$key];
            unset($_SESSION['flash_messages'][$key]);
            return $message;
        }
        return null;
    }

    private function initializeSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionParams = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => $sessionParams['lifetime'],
                'path' => '/',
                'domain' => $sessionParams['domain'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
            if (!session_start()) {
                $this->handleSessionError('Failed to start session');
            }
        }
        $this->regenerateSessionId();
    }

    private function regenerateSessionId(): void {
        if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 1800) {
            if (!session_regenerate_id(true)) {
                $this->handleSessionError('Failed to regenerate session ID');
            }
            $_SESSION['last_regeneration'] = time();
        }
    }

    private function handleSessionError(string $message): void {
        error_log("Session error: " . $message);
        http_response_code(500);
        $_SESSION['error_message'] = $message;
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'animation' => 'slideInDown',
            'code' => 500
        ]);
        exit();
    }

    protected function isValidCsrfToken(string $token): bool {
        return !empty($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}