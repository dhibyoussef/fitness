<?php
class CsrfMiddleware {
    public function handle(array $request): void {
        $this->ensureSessionIsActive();
        $this->verifyCsrfToken($request);
    }

    private function ensureSessionIsActive(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function verifyCsrfToken(array $request): void {
        $sessionToken = $_SESSION['csrf_token'] ?? null;
        $requestToken = $request['csrf_token'] ?? null;

        if (!$sessionToken || !$requestToken) {
            throw new Exception("CSRF token is missing from either the session or the request.");
        }

        if (!hash_equals($sessionToken, $requestToken)) {
            http_response_code(403);
            exit("CSRF token mismatch detected. Access has been denied.");
        }
    }
}
?>