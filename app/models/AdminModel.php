<?php
// app/models/AdminModel.php
require_once __DIR__ . '/BaseModel.php';

class AdminModel extends BaseModel {
    public function getUserCount(): int {
        $query = "SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL";
        $result = $this->fetchSingle($query);
        return (int)($result['total'] ?? 0);
    }

    public function getActiveUserCount(): int {
        $query = "SELECT COUNT(DISTINCT user_id) as active_users 
                  FROM workouts 
                  WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY) 
                  AND deleted_at IS NULL";
        return (int)$this->pdo->query($query)->fetchColumn();
    }

    public function getWorkoutStatistics(): array {
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

    public function getNutritionData(): array {
        $query = "SELECT AVG(calories) as avg_calories_per_meal, 
                         SUM(calories) as total_calories, 
                         COUNT(*) as total_meals, 
                         MAX(calories) as max_calories 
                  FROM meals 
                  WHERE deleted_at IS NULL";
        return $this->pdo->query($query)->fetch(PDO::FETCH_ASSOC) ?: [
            'avg_calories_per_meal' => 0,
            'total_calories' => 0,
            'total_meals' => 0,
            'max_calories' => 0
        ];
    }

    public function getRegistrationTrends(): array {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                         COUNT(*) as registrations 
                  FROM users 
                  WHERE deleted_at IS NULL 
                  GROUP BY month 
                  ORDER BY month ASC 
                  LIMIT 12";
        $trends = $this->fetchAll($query);
        return array_map(function ($row) {
            return [
                'month' => $row['month'],
                'registrations' => (int)$row['registrations']
            ];
        }, $trends);
    }

    public function getUserActivitySummary(): array {
        $query = "SELECT u.id, u.username, 
                         COUNT(DISTINCT w.id) as workouts, 
                         COUNT(DISTINCT m.id) as meals 
                  FROM users u 
                  LEFT JOIN workouts w ON w.user_id = u.id AND w.deleted_at IS NULL 
                  LEFT JOIN meals m ON m.user_id = u.id AND m.deleted_at IS NULL 
                  WHERE u.deleted_at IS NULL 
                  GROUP BY u.id, u.username 
                  ORDER BY workouts DESC, meals DESC 
                  LIMIT 10";
        return $this->fetchAll($query);
    }
}