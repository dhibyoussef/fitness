<?php
// app/models/ExerciseModel.php
namespace App\Models;
require_once __DIR__ . '/BaseModel.php';

class ExerciseModel extends BaseModel {
    /**
     * @throws \Exception
     */
    public function getUserExercises(int $userId): array {
        $query = "SELECT * FROM exercises WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY name ASC";
        return $this->fetchAll($query, ['user_id' => $userId]);
    }

    /**
     * @throws \Exception
     */
    public function getExerciseById(int $id, int $userId): ?array {
        $query = "SELECT * FROM exercises WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['id' => $id, 'user_id' => $userId]);
    }

    public function createExercise(array $data): bool {
        $query = "INSERT INTO exercises (user_id, name, one_rm, video_url, created_at) 
                  VALUES (:user_id, :name, :one_rm, :video_url, NOW())";
        return $this->execute($query, [
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'one_rm' => $data['one_rm'] ?? 0.0,
            'video_url' => $data['video_url'] ?? null
        ]);
    }

    public function updateExercise(int $id, array $data): bool {
        $query = "UPDATE exercises SET name = :name, one_rm = :one_rm, video_url = :video_url, updated_at = NOW() 
                  WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL";
        return $this->execute($query, [
            'id' => $id,
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'one_rm' => $data['one_rm'],
            'video_url' => $data['video_url'] ?? null
        ]);
    }

    /**
     * @throws \Exception
     */
    public function deleteExercise(int $id, int $userId): bool {
        $query = "UPDATE exercises SET deleted_at = NOW() WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL";
        return $this->execute($query, ['id' => $id, 'user_id' => $userId]);
    }

    public function getGoals(int $userId): array {
        $query = "SELECT g.*, e.name FROM goals g 
                  JOIN exercises e ON g.exercise_id = e.id 
                  WHERE g.user_id = :user_id AND g.deleted_at IS NULL";
        return $this->fetchAll($query, ['user_id' => $userId]);
    }

    /**
     * @throws \Exception
     */
    public function setGoal(array $data): bool {
        $query = "INSERT INTO goals (user_id, exercise_id, target_weight, attempts, created_at) 
                  VALUES (:user_id, :exercise_id, :target_weight, :attempts, NOW())
                  ON DUPLICATE KEY UPDATE target_weight = :target_weight, attempts = :attempts, updated_at = NOW()";
        return $this->execute($query, [
            'user_id' => $data['user_id'],
            'exercise_id' => $data['exercise_id'],
            'target_weight' => $data['target_weight'],
            'attempts' => json_encode($data['attempts'] ?? [])
        ]);
    }
}