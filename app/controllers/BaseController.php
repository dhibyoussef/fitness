<?php
// app/controllers/BaseController.php
namespace App\Controllers {
    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../models/BaseModel.php';
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../middleware/AuthMiddleware.php';
    require_once __DIR__ . '/../middleware/CsrfMiddleware.php';

    use AllowDynamicProperties;
    use App\Middleware\AuthMiddleware;

    use App\Middleware\CsrfMiddleware;
    use JetBrains\PhpStorm\NoReturn;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use PDO;

    #[AllowDynamicProperties] class BaseController {
        protected PDO $pdo;
        protected Logger $logger;
        protected AuthMiddleware $authMiddleware;
        protected CsrfMiddleware $csrfMiddleware;

        public function __construct(PDO $pdo) {
            $this->pdo = $pdo;
            $this->logger = new Logger('BaseController');
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log'    ));
            $this->authMiddleware = new AuthMiddleware();
            $this->csrfMiddleware = new CsrfMiddleware();

        }




        public function render(string $view, array $data = []): void {
            // Use the application root directory
            $appRoot = realpath(__DIR__ . '/../../'); // C:\xampp\htdocs\fitness-app
            $viewPath = $appRoot . '/app/views/' . trim($view, '/\\') . '.php';
            $resolvedPath = realpath($viewPath);
            $this->logger->info("Attempting to render view", ['path' => $viewPath, 'resolved' => $resolvedPath ?: 'Not found']);

            if ($resolvedPath && file_exists($resolvedPath)) {
                extract($data, EXTR_SKIP);
                require $resolvedPath;
            } else {
                $this->logger->error("View not found", ['path' => $viewPath]);
                $this->renderError("View not found: " . htmlspecialchars($view));
            }
        }
        #[NoReturn] public function renderError(string $message): void {
            http_response_code(500);
            $appRoot = realpath(__DIR__ . '/../../');
            $errorViewPath = $appRoot . '/app/views/error/error.php';
            $this->logger->info("Attempting to render error view", ['path' => $errorViewPath, 'resolved' => realpath($errorViewPath) ?: 'Not found']);
            if (file_exists($errorViewPath)) {
                $data = ['message' => $message];
                extract($data, EXTR_SKIP);
                require $errorViewPath;
            } else {
                echo "Error: " . htmlspecialchars($message);
            }
            exit;
        }

        #[NoReturn] protected function redirect(string $url): void {
            header("Location: $url");
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

        protected function setFlashMessage(string $type, string $message): void {
            $_SESSION['flash'][$type] = $message;
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
                    'same site' => 'Strict'
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
}