<?php
// app/controllers/CsrfMiddleware.php
namespace App\Middleware {
    class CsrfMiddleware {
        public function handle(array $request): void {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $sessionToken = $_SESSION['csrf_token'] ?? null;
            $requestToken = $request['csrf_token'] ?? null;

            if (!$sessionToken || !$requestToken || !hash_equals($sessionToken, $requestToken)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'CSRF token mismatch detected. Access denied.']);
                exit;
            }
        }

        public function isValidCsrfToken(string $requestToken): bool
        {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $sessionToken = $_SESSION['csrf_token'] ?? null;

            return $sessionToken && $requestToken && hash_equals($sessionToken, $requestToken);
        }


    }
}