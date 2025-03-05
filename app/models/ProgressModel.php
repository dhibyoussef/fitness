<?php
// app/models/ProgressModel.php
namespace App\Models;
use DateTime;
use Exception;

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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
    public function getAverageProgress(string $column): float {
        $query = "SELECT AVG($column) as avg_value FROM progress WHERE deleted_at IS NULL";
        return $this->fetchSingle($query)['avg_value'] ?? 0;
    }

    /**
     * @throws Exception
     */
    public function getProgressStats(int $userId): array {
        $query = "SELECT AVG(weight) as avg_weight, AVG(body_fat) as avg_body_fat, AVG(muscle_mass) as avg_muscle_mass FROM progress WHERE user_id = :user_id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['user_id' => $userId]) ?? [];
    }

    /**
     * @throws Exception
     */
    public function getTotalProgressEntries(int $userId): int {
        $query = "SELECT COUNT(*) FROM progress WHERE user_id = :user_id AND deleted_at IS NULL";
        return (int)$this->fetchSingle($query, ['user_id' => $userId])['count'];
    }

    /**
     * @throws Exception
     */
    public function getProgressEntries(int $userId): array {
        $query = "SELECT * FROM progress WHERE user_id = :user_id AND deleted_at IS NULL";
        return $this->fetchAll($query, ['user_id' => $userId]);
    }

    /**
     * @throws Exception
     */
    public function getProgressPercentage(int $userId): float {
        $query = "SELECT AVG(body_fat) as avg_body_fat FROM progress WHERE user_id = :user_id AND deleted_at IS NULL";
        $result = $this->fetchColumn($query, ['user_id' => $userId]);
        return $result !== false ? (float) $result : 0.0;
    }

    /**
     * @throws Exception
     */
    public function getMonthlyProgress(int $userId): array {
        $query = "SELECT DATE_FORMAT(date, '%Y-%m') AS week, AVG(body_fat) AS progress 
                  FROM progress 
                  WHERE user_id = :user_id 
                  AND deleted_at IS NULL 
                  AND date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  GROUP BY DATE_FORMAT(date, '%Y-%m') 
                  ORDER BY week ASC";
        $result = $this->fetchAll($query, ['user_id' => $userId]);

        // Ensure 12 months of data, filling gaps with 0
        $months = [];
        $current = new DateTime('now');
        $current->modify('-11 months'); // Start 11 months ago
        for ($i = 0; $i < 12; $i++) {
            $month = $current->format('Y-m');
            $months[$month] = ['week' => $month, 'progress' => 0];
            $current->modify('+1 month');
        }

        // Overlay actual data
        foreach ($result as $row) {
            if (isset($months[$row['week']])) {
                $months[$row['week']]['progress'] = (float) $row['progress'];
            }
        }

        return array_values($months); // Return fixed 12-month array
    }


}