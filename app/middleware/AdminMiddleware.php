<?php
// app/controllers/AdminMiddleware.php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminMiddleware {
    private Logger $logger;

    public function __construct() {
        $this->logger = new Logger('AdminMiddleware');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::INFO));
    }

    public function handle(callable $next): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new Exception('Session not started.');
        }

        if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT) {
            $this->logger->info("Session timeout", ['user_id' => $_SESSION['user_id'] ?? 'unknown']);
            session_destroy();
            $this->redirectUnauthorizedUser('Session expired.');
        }

        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
            $next();
        } else {
            $this->recordUnauthorizedAttempt();
            $this->redirectUnauthorizedUser('Admin access required.');
        }
    }

    private function recordUnauthorizedAttempt(): void {
        $this->logger->warning("Unauthorized access attempt", [
            'user_id' => $_SESSION['user_id'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'],
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    private function redirectUnauthorizedUser(string $message): void {
        $_SESSION['flash_messages']['error'] = $message;
        header("Location: /auth/login", true, 403);
        exit;
    }
}