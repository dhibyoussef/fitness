<?php
require_once 'BaseModel.php';

class ProgressModel extends BaseModel {
    /**
     * Inserts a new progress entry into the database.
     * @param array $data Associative array containing progress data.
     * @return bool Returns true on success or false on failure.
     */
    public function createProgress(array $data): bool {
        $query = "INSERT INTO progress (user_id, weight, body_fat, date) VALUES (:user_id, :weight, :body_fat, :date)";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute($data);
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Retrieves progress entries with pagination and filtering.
     * @param int $offset The offset for pagination.
     * @param int $itemsPerPage The number of records to retrieve.
     * @param string $searchQuery Optional search query for user ID.
     * @param string $sortBy Column to sort by.
     * @param string $sortOrder Order of sorting (ASC or DESC).
     * @return array An array of progress entries.
     */
    public function getProgressWithPagination(int $offset, int $itemsPerPage, string $searchQuery = '', string $sortBy = 'date', string $sortOrder = 'DESC'): array {
        $query = "SELECT * FROM progress WHERE user_id LIKE :searchQuery ORDER BY $sortBy $sortOrder LIMIT :offset, :itemsPerPage";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->bindValue(':searchQuery', '%' . $searchQuery . '%', PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Counts the total number of progress entries.
     * @param string $searchQuery Optional search query for user ID.
     * @return int The total number of progress entries.
     */
    public function countProgressEntries(string $searchQuery = ''): int {
        $query = "SELECT COUNT(*) as total FROM progress WHERE user_id LIKE :searchQuery";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->bindValue(':searchQuery', '%' . $searchQuery . '%', PDO::PARAM_STR);
        $stmt->execute();
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Retrieves a progress entry by its ID.
     * @param int $id The ID of the progress entry.
     * @return array|null The progress entry data or null if not found.
     */
    public function getProgressById(int $id): ?array {
        $query = "SELECT * FROM progress WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Updates a progress entry in the database.
     * @param int $id The ID of the progress entry to update.
     * @param array $data Associative array containing updated progress data.
     * @return bool Returns true on success or false on failure.
     */
    public function updateProgress(int $id, array $data): bool {
        $query = "UPDATE progress SET weight = :weight, body_fat = :body_fat, date = :date WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute(array_merge(['id' => $id], $data));
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Deletes a progress entry from the database.
     * @param int $id The ID of the progress entry to delete.
     * @return bool Returns true on success or false on failure.
     */
    public function deleteProgress(int $id): bool {
        $query = "DELETE FROM progress WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute(['id' => $id]);
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Checks if a progress entry exists by its ID.
     * @param int $id The ID of the progress entry.
     * @return bool True if it exists, false otherwise.
     */
    public function exists(int $id): bool {
        $query = "SELECT EXISTS(SELECT 1 FROM progress WHERE id = :id) as record_exists";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute(['id' => $id]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC)['record_exists'];
    }

    /**
     * Retrieves the user ID associated with the first progress entry.
     * @return int|null The user ID or null if not found.
     */
    public function getUserId(): ?int {
        $query = "SELECT user_id FROM progress LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['user_id'] : null;
    }

    /**
     * Retrieves overall progress statistics.
     * @return array An associative array of overall progress statistics.
     */
    public function getOverallProgressStatistics(): array {
        $query = "SELECT AVG(weight) as avg_weight, AVG(body_fat) as avg_body_fat, MIN(weight) as min_weight, MAX(weight) as max_weight, MIN(body_fat) as min_body_fat, MAX(body_fat) as max_body_fat FROM progress";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'avg_weight' => null,
            'avg_body_fat' => null,
            'min_weight' => null,
            'max_weight' => null,
            'min_body_fat' => null,
            'max_body_fat' => null
        ];
    }
}