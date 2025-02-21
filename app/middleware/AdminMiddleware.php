<?php

class AdminMiddleware {
    public function handle($request): void {
        $this->ensureSessionSecurity();

        if ($this->userHasAdminPrivileges()) {
            $this->processAdminRequest($request);
        } else {
            $this->denyAccess();
        }
    }

    private function ensureSessionSecurity(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            session_regenerate_id(true); // Enhances security against session fixation
        }
    }

    private function userHasAdminPrivileges(): bool {
        // Verifies if the current user is an administrator
        return isset($_SESSION['user_id']) && $this->checkAdminRole($_SESSION['role']);
    }

    private function checkAdminRole(string $role): bool {
        // Determines if the role is equivalent to 'admin'
        return $role === 'admin';
    }

    private function denyAccess(): void {
        $this->recordUnauthorizedAttempt();
        $this->redirectUnauthorizedUser ();
    }

    private function recordUnauthorizedAttempt(): void {
        // Records attempts to access resources without proper authorization
        $userId = $_SESSION['user_id'] ?? 'unknown';
        error_log("Unauthorized access attempt by user ID: $userId");
    }

    private function redirectUnauthorizedUser (): void {
        // Redirects users without admin rights to the login page with an error notification
        $_SESSION['error_message'] = "Access denied. Administrator privileges are required.";
        header("Location: /login.php?error=access_denied");
        exit;
    }

    private function processAdminRequest($request): void {
        // Handles requests that need administrative rights
        // This is a placeholder for handling specific admin requests
        // You can add logic here to process the request further
    }
}