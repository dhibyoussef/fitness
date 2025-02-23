<?php
// app/models/NutritionModel.php
require_once __DIR__ . '/BaseModel.php';

class NutritionModel extends BaseModel {
    public function createMeal(array $data): bool {
        $query = "INSERT INTO meals (user_id, name, calories, protein, carbs, fat, category_id, created_at, updated_at) 
                  VALUES (:user_id, :name, :calories, :protein, :carbs, :fat, :category_id, :created_at, NOW())";
        return $this->execute($query, [
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'calories' => $data['calories'],
            'protein' => $data['protein'] ?? null,
            'carbs' => $data['carbs'] ?? null,
            'fat' => $data['fat'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s')
        ]);
    }

    public function getNutritionById(int $id): ?array {
        $query = "SELECT m.*, c.name as category_name 
                  FROM meals m 
                  LEFT JOIN categories c ON m.category_id = c.id 
                  WHERE m.id = :id AND m.deleted_at IS NULL";
        return $this->fetchSingle($query, ['id' => $id]);
    }

    public function updateMeal(int $id, array $data): bool {
        $query = "UPDATE meals 
                  SET name = :name, calories = :calories, protein = :protein, carbs = :carbs, fat = :fat, 
                      category_id = :category_id, updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, [
            'id' => $id,
            'name' => $data['name'],
            'calories' => $data['calories'],
            'protein' => $data['protein'] ?? null,
            'carbs' => $data['carbs'] ?? null,
            'fat' => $data['fat'] ?? null,
            'category_id' => $data['category_id'] ?? null
        ]);
    }

    public function deleteMeal(int $id): bool {
        $query = "UPDATE meals SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, ['id' => $id]);
    }

    public function fetchMeals(int $offset, int $limit, string $filter = '', string $sortBy = 'created_at', string $sortOrder = 'DESC', int $userId): array {
        $query = "SELECT m.*, c.name as category_name 
                  FROM meals m 
                  LEFT JOIN categories c ON m.category_id = c.id 
                  WHERE m.user_id = :user_id AND m.name LIKE :filter AND m.deleted_at IS NULL 
                  ORDER BY $sortBy $sortOrder 
                  LIMIT :offset, :limit";
        return $this->fetchAll($query, [
            'user_id' => $userId,
            'filter' => "%$filter%",
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function countFilteredMeals(string $filter = '', int $userId): int {
        $query = "SELECT COUNT(*) FROM meals 
                  WHERE user_id = :user_id AND name LIKE :filter AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $userId, 'filter' => "%$filter%"]);
        return (int)$stmt->fetchColumn();
    }

    public function getOverallNutritionStatistics(): array {
        $query = "SELECT AVG(calories) as avg_calories_per_meal, 
                         SUM(calories) as total_calories, 
                         COUNT(*) as total_meals, 
                         MAX(calories) as max_calories,
                         AVG(protein) as avg_protein,
                         AVG(carbs) as avg_carbs,
                         AVG(fat) as avg_fat
                  FROM meals 
                  WHERE deleted_at IS NULL";
        return $this->pdo->query($query)->fetch(PDO::FETCH_ASSOC) ?: [
            'avg_calories_per_meal' => 0,
            'total_calories' => 0,
            'total_meals' => 0,
            'max_calories' => 0,
            'avg_protein' => 0,
            'avg_carbs' => 0,
            'avg_fat' => 0
        ];
    }

    public function getAllCategories(): array {
        $query = "SELECT id, name FROM categories WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->fetchAll($query);
    }

    public function getCategoryById(?int $categoryId): ?string {
        if (!$categoryId) return null;
        $query = "SELECT name FROM categories WHERE id = :id AND deleted_at IS NULL";
        $result = $this->fetchSingle($query, ['id' => $categoryId]);
        return $result['name'] ?? null;
    }
    public function getNutritionStats(int $userId): array {
        $query = "SELECT COUNT(*) as total_meals, AVG(calories) as avg_calories, SUM(calories) as total_calories FROM meals WHERE user_id = :user_id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['user_id' => $userId]);
    }
}