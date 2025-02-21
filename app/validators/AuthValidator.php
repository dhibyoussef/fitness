<?php

class AuthValidator {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Validates login data.
     *
     * @param array $data The login data containing email and password.
     * @return array An array of validation errors, if any.
     */
    public function validateLogin(array $data): array {
        $errors = [];

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        // Validate password
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters long.';
        } elseif (!preg_match('/[A-Za-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one letter and one number.';
        }

        return $errors; // Return an array of errors
    }

    /**
     * Validates signup data.
     *
     * @param array $data The signup data containing username, email, and password.
     * @return array An array of validation errors, if any.
     */
    public function validateSignup(array $data): array {
        $errors = $this->validateLogin($data); // Reuse login validation

        // Validate username
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters long.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
        } elseif ($this->isUsernameTaken($data['username'])) {
            $errors['username'] = 'Username is already taken.';
        }

        // Check if email is already registered
        if ($this->isEmailRegistered($data['email'])) {
            $errors['email'] = 'Email is already registered.';
        }

        return $errors; // Return an array of errors
    }

    /**
     * Checks if the username is already taken.
     *
     * @param string $username The username to check.
     * @return bool True if the username is taken, false otherwise.
     */
    private function isUsernameTaken(string $username): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Checks if the email is already registered.
     *
     * @param string $email The email to check.
     * @return bool True if the email is registered, false otherwise.
     */
    private function isEmailRegistered(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }
}
?>