<?php

class NutritionValidator {
    /**
     * Validates a nutrition entry.
     *
     * @param array $data The nutrition data containing meal name and calories.
     * @return array An array of validation errors, if any.
     */
    public function validateNutritionEntry(array $data): array {
        $errors = [];

        // Validate meal name
        $this->validateMealName($data['name'] ?? '', $errors);
        
        // Validate calories
        $this->validateCalories($data['calories'] ?? '', $errors);

        return $errors; // Return an array of errors
    }

    private function validateMealName(string $name, array &$errors): void {
        $trimmedName = trim($name);
        if (empty($trimmedName)) {
            $errors['name'] = 'Meal name is required.';
        } elseif (strlen($trimmedName) < 3) {
            $errors['name'] = 'Meal name must be at least 3 characters long.';
        } elseif (strlen($trimmedName) > 50) {
            $errors['name'] = 'Meal name must not exceed 50 characters.';
        } elseif (!preg_match('/^[\p{L}\p{N}\s]+$/u', $trimmedName)) {
            $errors['name'] = 'Meal name can only contain letters, numbers, and spaces.';
        }
    }

    private function validateCalories(string $calories, array &$errors): void {
        $trimmedCalories = trim($calories);
        if (empty($trimmedCalories)) {
            $errors['calories'] = 'Calories are required.';
        } elseif (!is_numeric($trimmedCalories)) {
            $errors['calories'] = 'Calories must be a valid number.';
        } elseif ((float)$trimmedCalories < 0) {
            $errors['calories'] = 'Calories must be a positive number.';
        } elseif ((float)$trimmedCalories > 10000) {
            $errors['calories'] = 'Calories must not exceed 10,000.';
        } elseif (preg_match('/\.\d{3,}$/', $trimmedCalories)) {
            $errors['calories'] = 'Calories must not have more than two decimal places.';
        }
    }
}
?>