<?php
require_once 'BaseModel.php';

class NutritionModel extends BaseModel {
    /**
     * Inserts a new meal into the database.
     * @param array $data Associative array containing meal data.
     * @return bool Returns true on success or false on failure.
     */
    public function createMeal(array $data): bool {
        $query = "INSERT INTO meals (user_id, name, calories, category_id) VALUES (:user_id, :name, :calories, :category_id)";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute($data);
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Retrieves meals from the database with optional pagination and filtering.
     * @param int $offset The offset for pagination.
     * @param int $limit The number of records to retrieve.
     * @param string $filter Optional filter for meal names.
     * @return array An array of associative arrays representing each meal.
     */
    public function fetchMeals(int $offset = 0, int $limit = 10, string $filter = ''): array {
        $query = "SELECT * FROM meals WHERE name LIKE :filter LIMIT :offset, :limit";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->bindValue(':filter', '%' . $filter . '%', PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Counts the total number of meals in the database with optional filtering.
     * @param string $filter Optional filter for meal names.
     * @return int The total number of meals.
     */
    public function countFilteredMeals(string $filter = ''): int {
        $query = "SELECT COUNT(*) as total FROM meals WHERE name LIKE :filter";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->bindValue(':filter', '%' . $filter . '%', PDO::PARAM_STR);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retrieves a single meal by its ID.
     * @param int $id The ID of the meal.
     * @return array|null An associative array of the meal data, or null if not found.
     */
    public function getNutritionById(int $id): ?array {
        $query = "SELECT * FROM meals WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Updates a meal in the database.
     * @param int $id The ID of the meal to update.
     * @param array $data Associative array containing updated meal data.
     * @return bool Returns true on success or false on failure.
     */
    public function updateMeal(int $id, array $data): bool {
        $query = "UPDATE meals SET name = :name, calories = :calories, category_id = :category_id WHERE id = :id";
        $params = array_merge(['id' => $id], $data);
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute($params);
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Deletes a meal from the database.
     * @param int $id The ID of the meal to delete.
     * @return bool Returns true on success or false on failure.
     */
    public function deleteMeal(int $id): bool {
        $query = "DELETE FROM meals WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute(['id' => $id]);
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Retrieves all meals from the database.
     * @return array An array of associative arrays representing each meal.
     */
    public function fetchAllMeals(): array {
        $query = "SELECT * FROM meals";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves nutritional values for a meal by its ID.
     * @param int $mealId The ID of the meal.
     * @return array|null An associative array of nutritional values, or null if not found.
     */
    public function getNutritionalValuesByMealId(int $mealId): ?array {
        $query = "SELECT * FROM nutritional_values WHERE meal_id = :meal_id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute(['meal_id' => $mealId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Retrieves portion sizes for a meal by its ID.
     * @param int $mealId The ID of the meal.
     * @return array An array of portion sizes.
     */
    public function getPortionSizesByMealId(int $mealId): array {
        $query = "SELECT * FROM portion_sizes WHERE meal_id = :meal_id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute(['meal_id' => $mealId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retrieves ingredients for a meal by its ID.
     * @param int $mealId The ID of the meal.
     * @return array An array of ingredients.
     */
    public function getIngredientsByMealId(int $mealId): array {
        $query = "SELECT * FROM ingredients WHERE meal_id = :meal_id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute(['meal_id' => $mealId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retrieves overall nutrition statistics.
     * @return array An associative array of overall nutrition statistics.
     */
    public function getOverallNutritionStatistics(): array {
        $query = "SELECT SUM(calories) as total_calories, COUNT(*) as total_meals FROM meals";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}