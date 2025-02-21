<?php

class ProgressValidator {
    /**
     * Validates a progress entry containing weight and body fat percentage.
     *
     * @param array $data The progress data to validate.
     * @return array An array of validation errors, if any.
     */
    public function validateProgressEntry(array $data): array {
        $errors = [];

        // Validate weight and body fat percentage
        $this->validateWeight($data['weight'] ?? '', $errors);
        $this->validateBodyFat($data['body_fat'] ?? '', $errors);

        return $errors; // Return an array of errors
    }

    /**
     * Validates the weight input.
     *
     * @param string $weight The weight value to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateWeight(string $weight, array &$errors): void {
        $trimmedWeight = trim($weight);
        if (empty($trimmedWeight)) {
            $errors['weight'] = 'Weight is required.';
        } elseif (!is_numeric($trimmedWeight)) {
            $errors['weight'] = 'Weight must be a valid number.';
        } elseif ((float)$trimmedWeight <= 0) {
            $errors['weight'] = 'Weight must be a positive number.';
        } elseif (preg_match('/\.\d{3,}$/', $trimmedWeight)) {
            $errors['weight'] = 'Weight must not have more than two decimal places.';
        }
    }

    /**
     * Validates the body fat percentage input.
     *
     * @param string $bodyFat The body fat percentage value to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateBodyFat(string $bodyFat, array &$errors): void {
        $trimmedBodyFat = trim($bodyFat);
        if (empty($trimmedBodyFat)) {
            $errors['body_fat'] = 'Body fat percentage is required.';
        } elseif (!is_numeric($trimmedBodyFat)) {
            $errors['body_fat'] = 'Body fat percentage must be a valid number.';
        } elseif ((float)$trimmedBodyFat < 0 || (float)$trimmedBodyFat > 100) {
            $errors['body_fat'] = 'Body fat percentage must be between 0 and 100.';
        } elseif (preg_match('/\.\d{3,}$/', $trimmedBodyFat)) {
            $errors['body_fat'] = 'Body fat percentage must not have more than two decimal places.';
        }
    }
}
?>