<?php
// app/controllers/AdminMiddleware.php
namespace App\Middleware {
    require_once __DIR__ . '/../../vendor/autoload.php';

    use Exception;
    use JetBrains\PhpStorm\NoReturn;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    class AdminMiddleware {
        private Logger $logger;

        public function __construct() {
            $this->logger = new Logger('AdminMiddleware');
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::INFO));
        }

        /**
         * @throws Exception
         */
        public function handle(callable $next): void {
            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
                header('Location: /auth/login');
                exit;
            }
            $next();
        }

        private function recordUnauthorizedAttempt(): void {
            $this->logger->warning("Unauthorized access attempt", [
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'],
                'uri' => $_SERVER['REQUEST_URI']
            ]);
        }

        #[NoReturn] private function redirectUnauthorizedUser(string $message): void {
            $_SESSION['flash_messages']['error'] = $message;
            header("Location: /auth/login", true, 403);
            exit;
        }
    }
}