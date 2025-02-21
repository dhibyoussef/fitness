<?php

class UserValidator {
    /**
     * Validates user registration data.
     *
     * @param array $data The registration data to validate.
     * @return array An array of validation errors, if any.
     */
    public function validateRegistration(array $data): array {
        $errors = [];

        // Validate username
        $this->validateUsername($data['username'] ?? '', $errors);

        // Validate email
        $this->validateEmail($data['email'] ?? '', $errors);

        // Validate password
        $this->validatePassword($data['password'] ?? '', $errors);

        return $errors; // Return an array of errors
    }

    /**
     * Validates user profile update data.
     *
     * @param array $data The profile update data to validate.
     * @return array An array of validation errors, if any.
     */
    public function validateProfileUpdate(array $data): array {
        $errors = [];

        // Validate username
        $this->validateUsername($data['username'] ?? '', $errors);

        // Validate email
        $this->validateEmail($data['email'] ?? '', $errors);

        // Optionally validate password for profile updates
        if (!empty($data['password'])) {
            $this->validatePassword($data['password'], $errors);
        }

        return $errors; // Return an array of errors
    }

    /**
     * Validates the username input.
     *
     * @param string $username The username to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateUsername(string $username, array &$errors): void {
        $trimmedUsername = trim($username);
        if (empty($trimmedUsername)) {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($trimmedUsername) < 3 || strlen($trimmedUsername) > 20) {
            $errors['username'] = 'Username must be between 3 and 20 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $trimmedUsername)) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
        }
    }

    /**
     * Validates the email input.
     *
     * @param string $email The email to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateEmail(string $email, array &$errors): void {
        $trimmedEmail = trim($email);
        if (empty($trimmedEmail)) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        } elseif (strlen($trimmedEmail) > 255) {
            $errors['email'] = 'Email must not exceed 255 characters.';
        }
    }

    /**
     * Validates the password input.
     *
     * @param string $password The password to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validatePassword(string $password, array &$errors): void {
        $trimmedPassword = trim($password);
        if (empty($trimmedPassword)) {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($trimmedPassword) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        } elseif (!preg_match('/[A-Z]/', $trimmedPassword)) {
            $errors['password'] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $trimmedPassword)) {
            $errors['password'] = 'Password must contain at least one lowercase letter.';
        } elseif (!preg_match('/[0-9]/', $trimmedPassword)) {
            $errors['password'] = 'Password must contain at least one number.';
        } elseif (!preg_match('/[\W_]/', $trimmedPassword)) {
            $errors['password'] = 'Password must contain at least one special character.';
        } elseif (preg_match('/\s/', $trimmedPassword)) {
            $errors['password'] = 'Password must not contain spaces.';
        }
    }
}
?>