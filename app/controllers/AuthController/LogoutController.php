<?php
header('Content-Type: application/json');

try {
    // Ensure session is started securely
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params(
            $sessionParams["lifetime"],
            $sessionParams["path"],
            $sessionParams["domain"],
            true, // secure
            true  // httponly
        );

        if (session_start() === false) {
            throw new Exception('Unable to start session.');
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    require_once '../BaseController.php';
    require_once '../../models/UserModel.php';
    require_once '../../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (empty($csrfToken) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
            error_log(sprintf(
                "CSRF token validation failed for user ID: %s | IP: %s | User-Agent: %s",
                $_SESSION['user_id'] ?? 'unknown',
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ));

            unset($_SESSION['csrf_token']);

            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid security token. Please try again.',
                'animation' => 'window-shake',
                'stayOnPage' => true
            ]);
            exit();
        }

        if (!session_regenerate_id(true)) {
            throw new Exception('Unable to regenerate session ID.');
        }
    }

    if (isset($_SESSION['user_id'])) {
        // Confirm user's intent to log out
        if ($_POST['confirm_logout'] ?? false) {
            session_unset();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            if (!session_destroy()) {
                throw new Exception('Unable to destroy session.');
            }

            // Optionally log out from all devices
            if ($_POST['logout_all_devices'] ?? false) {
                // Implement logic to invalidate sessions on all devices
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Successfully logged out.',
                'animation' => 'window-fade',
                'redirect' => '../../../index.php'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Logout not confirmed.',
                'animation' => 'window-shake',
                'stayOnPage' => true
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'You must be logged in to log out.',
            'animation' => 'window-shake',
            'stayOnPage' => true
        ]);
    }
} catch (Exception $e) {
    error_log(sprintf(
        'LogoutController Error: %s | User ID: %s | IP: %s',
        $e->getMessage(),
        $_SESSION['user_id'] ?? 'unknown',
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ));

    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage(),
        'animation' => 'window-shake',
        'stayOnPage' => true
    ]);
}