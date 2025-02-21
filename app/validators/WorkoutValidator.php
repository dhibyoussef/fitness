<?php

class WorkoutValidator {
    
    /**
     * Validates the workout entry data.
     *
     * @param array $data The workout data to validate.
     * @return array An array of validation errors, if any.
     */
    public function validateWorkoutEntry(array $data): array {
        $errors = [];

        // Validate workout name
        $this->validateWorkoutName($data['name'] ?? '', $errors);

        // Validate category ID
        $this->validateCategoryId($data['category_id'] ?? null, $errors);

        // Validate duration
        $this->validateDuration($data['duration'] ?? null, $errors);

        // Validate intensity
        $this->validateIntensity($data['intensity'] ?? null, $errors);

        return $errors; // Return an array of errors
    }

    /**
     * Validates the workout name.
     *
     * @param string $name The workout name to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateWorkoutName(string $name, array &$errors): void {
        $trimmedName = trim($name);
        if (empty($trimmedName)) {
            $errors['name'] = 'Workout name is required.';
        } elseif (strlen($trimmedName) < 3) {
            $errors['name'] = 'Workout name must be at least 3 characters long.';
        } elseif (strlen($trimmedName) > 50) {
            $errors['name'] = 'Workout name must not exceed 50 characters.';
        } elseif (!preg_match('/^[\p{L}\p{N}\s]+$/u', $trimmedName)) {
            $errors['name'] = 'Workout name can only contain letters, numbers, and spaces.';
        }
    }

    /**
     * Validates the category ID.
     *
     * @param mixed $categoryId The category ID to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateCategoryId($categoryId, array &$errors): void {
        if (is_null($categoryId) || $categoryId === '') {
            $errors['category_id'] = 'Workout category is required.';
        } elseif (!is_numeric($categoryId)) {
            $errors['category_id'] = 'Workout category must be a valid number.';
        } elseif ((int)$categoryId <= 0) {
            $errors['category_id'] = 'Workout category must be a positive number.';
        } elseif (strlen((string)$categoryId) > 10) {
            $errors['category_id'] = 'Workout category ID must not exceed 10 digits.';
        }
    }

    /**
     * Validates the workout duration.
     *
     * @param mixed $duration The duration to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateDuration($duration, array &$errors): void {
        if (is_null($duration) || $duration === '') {
            $errors['duration'] = 'Workout duration is required.';
        } elseif (!is_numeric($duration)) {
            $errors['duration'] = 'Workout duration must be a valid number.';
        } elseif ((float)$duration <= 0) {
            $errors['duration'] = 'Workout duration must be a positive number.';
        } elseif (preg_match('/\.\d{3,}$/', $duration)) {
            $errors['duration'] = 'Workout duration must not have more than two decimal places.';
        }
    }

    /**
     * Validates the workout intensity.
     *
     * @param mixed $intensity The intensity to validate.
     * @param array &$errors The array to store validation errors.
     */
    private function validateIntensity($intensity, array &$errors): void {
        if (is_null($intensity) || $intensity === '') {
            $errors['intensity'] = 'Workout intensity is required.';
        } elseif (!in_array($intensity, ['low', 'medium', 'high'], true)) {
            $errors['intensity'] = 'Workout intensity must be one of: low, medium, high.';
        }
    }
}
?>