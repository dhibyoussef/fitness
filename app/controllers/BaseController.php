<?php
// app/controllers/BaseController.php
require_once __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class BaseController {
    protected PDO $db;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
        $this->logger = new Logger('BaseController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log'    ));
        $this->initializeSession();
    }

    public function render(string $view, array $data = []): void {
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewPath) && is_readable($viewPath)) {
            extract($data, EXTR_SKIP);
            require $viewPath;
        } else {
            $this->logger->error("View not found", ['view' => $view]);
            $this->renderError("View '{$view}' not found.");
        }
    }

    public function renderError(string $message): void {
        http_response_code(500);
        $this->render('error/error', [
            'message' => $message,
            'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
        ]);
        exit;
    }

    protected function redirect(string $url, int $statusCode = 302): void {
        header("Location: $url", true, $statusCode);
        exit;
    }

    protected function requireAuth(): void {
        if (!$this->isAuthenticated()) {
            $this->logger->warning("Auth required", ['ip' => $_SERVER['REMOTE_ADDR']]);
            $this->setFlashMessage('error', 'Please log in to continue.');
            $this->redirect('/auth/login');
        }
        if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT) {
            $this->logger->info("Session timeout", ['user_id' => $_SESSION['user_id']]);
            session_destroy();
            $this->setFlashMessage('error', 'Session expired. Please log in again.');
            $this->redirect('/auth/login');
        }
        $_SESSION['last_activity'] = time();
    }

    public function isAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true;
    }

    public function isAdmin(): bool {
        return $this->isAuthenticated() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    protected function setFlashMessage(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    public function getFlashMessage(string $type): ?string {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }

    private function initializeSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            if (!session_start()) {
                $this->logger->error("Session start failed");
                $this->renderError("Failed to initialize session.");
            }
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
        $this->regenerateSession();
    }

    private function regenerateSession(): void {
        if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }

    public function isValidCsrfToken(string $token): bool {
        return !empty($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
protected function fetchFromCache(string $key): mixed {
    $cacheFile = __DIR__ . '/../../cache/' . md5($key) . '.cache'; // Adjusted path
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 300) {
        return unserialize(file_get_contents($cacheFile));
    }
    return false;
}

protected function storeInCache(string $key, mixed $value, int $ttl): void {
    $cacheDir = __DIR__ . '/../../cache/'; // Adjusted path
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }
    file_put_contents($cacheDir . md5($key) . '.cache', serialize($value));
}


}