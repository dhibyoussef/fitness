<?php
// app/controllers/AuthMiddleware.php
namespace App\Middleware {
    require_once __DIR__ . '/../../vendor/autoload.php';

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    class AuthMiddleware {
        private Logger $logger;

        public function __construct() {
            $this->logger = new Logger('AuthMiddleware');
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::INFO));
        }

        public function handle(callable $next): void {
            if (!isset($_SESSION['user_id']) || $_SESSION['logged_in'] !== true) {
                $this->logger->info("Redirecting to login", ['ip' => $_SERVER['REMOTE_ADDR']]);
                $_SESSION['flash_messages']['error'] = 'Please log in to continue.';
                header("Location: /auth/login", true, 401);
                exit;
            }

            $this->logger->info("Access logged", [
                'user_id' => $_SESSION['user_id'],
                'uri' => $_SERVER['REQUEST_URI'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            $next();
        }
    }
}