<?php
// app/models/WorkoutModel.php
require_once __DIR__ . '/BaseModel.php';

class WorkoutModel extends BaseModel {
    public function createWorkout(array $data): bool {
        $query = "INSERT INTO workouts (user_id, name, description, category_id, duration, calories, is_custom, created_at, updated_at) 
                  VALUES (:user_id, :name, :description, :category_id, :duration, :calories, :is_custom, NOW(), NOW())";
        return $this->execute($query, [
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?? null,
            'duration' => $data['duration'],
            'calories' => $data['calories'] ?? null,
            'is_custom' => $data['is_custom'] ?? 0
        ]);
    }
    public function getLinkedExercises(int $workoutId): array {
        $query = "SELECT e.id, e.name FROM exercises e JOIN workout_exercises we ON e.id = we.exercise_id WHERE we.workout_id = :workout_id";
        return $this->fetchAll($query, ['workout_id' => $workoutId]);
    }
    public function getWorkouts(): array {
        $query = "SELECT * FROM workouts WHERE deleted_at IS NULL";
        return $this->fetchAll($query);
    }


    public function getWorkoutById(int $id): ?array {
        $query = "SELECT w.*, c.name as category_name 
                  FROM workouts w 
                  LEFT JOIN categories c ON w.category_id = c.id 
                  WHERE w.id = :id AND w.deleted_at IS NULL";
        return $this->fetchSingle($query, ['id' => $id]);
    }

    public function updateWorkout(int $id, array $data): bool {
        $query = "UPDATE workouts 
                  SET name = :name, description = :description, category_id = :category_id, 
                      duration = :duration, calories = :calories, updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, [
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?? null,
            'duration' => $data['duration'],
            'calories' => $data['calories'] ?? null
        ]);
    }

    public function deleteWorkout(int $id): bool {
        $query = "UPDATE workouts SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, ['id' => $id]);
    }

    public function getFilteredWorkouts(string $filter, int $offset, int $limit, string $sortBy, string $sortOrder, int $userId, bool $showPredefined): array {
        $query = "SELECT w.*, c.name as category_name 
                  FROM workouts w 
                  LEFT JOIN categories c ON w.category_id = c.id 
                  WHERE (w.user_id = :user_id OR (:show_predefined AND w.is_predefined = 1)) 
                  AND w.name LIKE :filter AND w.deleted_at IS NULL 
                  ORDER BY $sortBy $sortOrder 
                  LIMIT :offset, :limit";
        return $this->fetchAll($query, [
            'user_id' => $userId,
            'show_predefined' => (int)$showPredefined,
            'filter' => "%$filter%",
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function countFilteredWorkouts(string $filter, int $userId, bool $showPredefined): int {
        $query = "SELECT COUNT(*) FROM workouts 
                  WHERE (user_id = :user_id OR (:show_predefined AND is_predefined = 1)) 
                  AND name LIKE :filter AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'user_id' => $userId,
            'show_predefined' => (int)$showPredefined,
            'filter' => "%$filter%"
        ]);
        return (int)$stmt->fetchColumn();
    }

    public function getOverallWorkoutStatistics(): array {
        $query = "SELECT COUNT(*) as total_workouts, 
                         AVG(duration) as avg_duration, 
                         SUM(calories) as total_calories, 
                         COUNT(DISTINCT category_id) as categories_used 
                  FROM workouts 
                  WHERE deleted_at IS NULL";
        return $this->pdo->query($query)->fetch(PDO::FETCH_ASSOC) ?: [
            'total_workouts' => 0,
            'avg_duration' => 0,
            'total_calories' => 0,
            'categories_used' => 0
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
    public function getoWorkoutStats(int $userId): array {
        $query = "SELECT COUNT(*) as total_workouts, AVG(duration) as avg_duration, SUM(calories) as total_calories, COUNT(DISTINCT category_id) as categories_used FROM workouts WHERE user_id = :user_id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['user_id' => $userId]);
    }
    public function getCategoryTrends(): array {
        $query = "SELECT c.name, COUNT(w.id) as workouts, COUNT(m.id) as meals FROM categories c LEFT JOIN workouts w ON w.category_id = c.id AND w.deleted_at IS NULL LEFT JOIN meals m ON m.category_id = c.id AND m.deleted_at IS NULL GROUP BY c.id, c.name";
        return $this->fetchAll($query);
    }
    public function getExercises() {
        $query = "SELECT id, name FROM exercises WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->fetchAll($query);
    }
    public function getCategories() {
        $query = "SELECT id, name FROM categories WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->fetchAll($query);
    }
    
}