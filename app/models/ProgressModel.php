<?php
// app/models/ProgressModel.php
require_once __DIR__ . '/BaseModel.php';

class ProgressModel extends BaseModel {
    public function createProgress(array $data): bool {
        $query = "INSERT INTO progress (user_id, weight, body_fat, muscle_mass, date, created_at, updated_at) 
                  VALUES (:user_id, :weight, :body_fat, :muscle_mass, :date, NOW(), NOW())";
        return $this->execute($query, [
            'user_id' => $data['user_id'],
            'weight' => $data['weight'],
            'body_fat' => $data['body_fat'],
            'muscle_mass' => $data['muscle_mass'] ?? null,
            'date' => $data['date']
        ]);
    }

    public function getProgressById(int $id): ?array {
        $query = "SELECT * FROM progress WHERE id = :id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['id' => $id]);
    }

    public function updateProgress(int $id, array $data): bool {
        $query = "UPDATE progress 
                  SET weight = :weight, body_fat = :body_fat, muscle_mass = :muscle_mass, date = :date, updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, [
            'id' => $id,
            'weight' => $data['weight'],
            'body_fat' => $data['body_fat'],
            'muscle_mass' => $data['muscle_mass'] ?? null,
            'date' => $data['date']
        ]);
    }

    public function deleteProgress(int $id): bool {
        $query = "UPDATE progress SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, ['id' => $id]);
    }

    public function getProgressWithPagination(int $offset, int $limit, string $filter = '', string $sortBy = 'date', string $sortOrder = 'DESC', int $userId): array {
        $query = "SELECT * FROM progress 
                  WHERE user_id = :user_id AND date LIKE :filter AND deleted_at IS NULL 
                  ORDER BY $sortBy $sortOrder 
                  LIMIT :offset, :limit";
        return $this->fetchAll($query, [
            'user_id' => $userId,
            'filter' => "%$filter%",
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function countProgressEntries(string $filter = '', int $userId): int {
        $query = "SELECT COUNT(*) FROM progress 
                  WHERE user_id = :user_id AND date LIKE :filter AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $userId, 'filter' => "%$filter%"]);
        return (int)$stmt->fetchColumn();
    }

    public function getOverallProgressStatistics(int $userId = 0): array {
        $query = "SELECT AVG(weight) as avg_weight, 
                         MIN(weight) as min_weight, 
                         MAX(weight) as max_weight, 
                         AVG(body_fat) as avg_body_fat, 
                         AVG(muscle_mass) as avg_muscle_mass 
                  FROM progress 
                  WHERE deleted_at IS NULL" . ($userId ? " AND user_id = :user_id" : "");
        $params = $userId ? ['user_id' => $userId] : [];
        return $this->fetchSingle($query, $params) ?: [
            'avg_weight' => 0,
            'min_weight' => 0,
            'max_weight' => 0,
            'avg_body_fat' => 0,
            'avg_muscle_mass' => 0
        ];
    }
    public function getAverageProgress(string $column): float {
        $query = "SELECT AVG($column) as avg_value FROM progress WHERE deleted_at IS NULL";
        return $this->fetchSingle($query)['avg_value'] ?? 0;
    }
    public function getProgressStats(int $userId): array {
        $query = "SELECT AVG(weight) as avg_weight, AVG(body_fat) as avg_body_fat, AVG(muscle_mass) as avg_muscle_mass FROM progress WHERE user_id = :user_id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['user_id' => $userId]) ?? [];
    }
    public function getTotalProgressEntries(int $userId): int {
        $query = "SELECT COUNT(*) FROM progress WHERE user_id = :user_id AND deleted_at IS NULL";
        return (int)$this->fetchSingle($query, ['user_id' => $userId])['count'];
    }
    public function getProgressEntries(int $userId): array {
        $query = "SELECT * FROM progress WHERE user_id = :user_id AND deleted_at IS NULL";
        return $this->fetchAll($query, ['user_id' => $userId]);
    }
}