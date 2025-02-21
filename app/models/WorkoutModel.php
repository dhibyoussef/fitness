<?php
require_once 'BaseModel.php';

class WorkoutModel extends BaseModel {
    /**
     * Inserts a new workout into the database.
     * @param array $data Details of the workout.
     * @return bool True if successful, false otherwise.
     */
    public function createWorkout(array $data): bool {
        $query = "INSERT INTO workouts (user_id, name, description, category_id, duration, calories) VALUES (:user_id, :name, :description, :category_id, :duration, :calories)";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute($data);
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Fetches workouts based on filters, with pagination.
     * @param array $filter Conditions to filter by.
     * @param int $offset Starting index for fetching records.
     * @param int $pageSize Number of records to fetch.
     * @return array List of workouts.
     */
    public function getFilteredWorkouts(array $filter, int $offset, int $pageSize): array {
        $query = "SELECT * FROM workouts WHERE 1=1";
        $params = [];
        foreach ($filter as $key => $value) {
            $query .= " AND $key LIKE :$key";
            $params[$key] = "%$value%";
        }
        $query .= " LIMIT :offset, :pageSize";
        $params['offset'] = $offset;
        $params['pageSize'] = $pageSize;

        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Counts workouts matching the filter criteria.
     * @param array $filter Conditions to filter by.
     * @return int Count of workouts.
     */
    public function countFilteredWorkouts(array $filter): int {
        $query = "SELECT COUNT(*) FROM workouts WHERE 1=1";
        $params = [];
        foreach ($filter as $key => $value) {
            $query .= " AND $key LIKE :$key";
            $params[$key] = "%$value%";
        }

        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retrieves a workout by its ID.
     * @param int $id Workout ID.
     * @return array|null Workout details or null if not found.
     */
    public function getWorkoutById(int $id): ?array {
        $query = "SELECT * FROM workouts WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Updates workout details in the database.
     * @param int $id Workout ID.
     * @param array $data Updated details.
     * @return bool True if successful, false otherwise.
     */
    public function updateWorkout(int $id, array $data): bool {
        $query = "UPDATE workouts SET name = :name, description = :description, category_id = :category_id, duration = :duration, calories = :calories WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute(array_merge(['id' => $id], $data));
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Deletes a workout from the database.
     * @param int $id Workout ID.
     * @return bool True if successful, false otherwise.
     */
    public function deleteWorkout(int $id): bool {
        $query = "DELETE FROM workouts WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $success = $stmt->execute(['id' => $id]);
        return $success && $stmt->rowCount() > 0;
    }

    /**
     * Retrieves overall statistics for all workouts.
     * @return array Overall workout statistics.
     */
    public function getOverallWorkoutStatistics(): array {
        $query = "SELECT COUNT(*) as total_workouts, SUM(duration) as total_duration, SUM(calories) as total_calories FROM workouts";
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}
?>