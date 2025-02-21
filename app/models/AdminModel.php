<?php
require_once 'BaseModel.php';

class AdminModel extends BaseModel {
    /**
     * Gets the dashboard statistics including total users and active users.
     * @return array An associative array containing total user count and active user count.
     */
    public function getDashboardStatistics(): array {
        
        $totalUsersQuery = "SELECT COUNT(*) as count FROM users"; 
        $activeUsersQuery = "SELECT COUNT(*) as count FROM users WHERE active = 1"; 

        $totalUsers = $this->fetchSingleValue($totalUsersQuery, []);
        $activeUsers = $this->fetchSingleValue($activeUsersQuery, []);

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'workout_stats' => $this->getWorkoutStats(),
            'registration_trends' => $this->getRegistrationTrends(),
            'user_growth' => $this->calculateUserGrowth(),
            'average_duration' => $this->getAverageDuration(),
            'total_workouts' => $this->getTotalWorkouts(),
            'user_count' => $this->getUserCount(),


        ];
    }
    public function getAverageDuration(): float {
        $query = "SELECT AVG(duration) as average_duration FROM workouts";
        return $this->fetchSingleValue($query, []);
    }
    public function getTotalWorkouts(): int {
        $query = "SELECT COUNT(*) as total_workouts FROM workouts";
        return $this->fetchSingleValue($query, []);
    }
    /**
     * Gets workout statistics including total workouts and average duration.
     * @return array An associative array containing total workout count and average duration.
     */
    public function getWorkoutStats(): array {
        $totalWorkoutsQuery = "SELECT COUNT(*) as count FROM workouts"; 
        $averageDurationQuery = "SELECT AVG(duration) as average_duration FROM workouts"; 

        $totalWorkouts = $this->fetchSingleValue($totalWorkoutsQuery, []);
        $averageDuration = $this->fetchSingleValue($averageDurationQuery, []);

        return [
            'total_workouts' => $totalWorkouts,
            'average_duration' => $averageDuration
        ];
    }

    // Other methods remain unchanged...
    /**
     * Calculates user growth based on the number of users over time.
     * @return float The percentage of user growth.
     */
    public function calculateUserGrowth(): float {
        $query = "SELECT COUNT(*) as count FROM users"; 
        $currentCount = $this->fetchSingleValue($query, []);
        
        // Assuming we have a way to get the count from a previous period
        $previousCount = $this->fetchSingleValue("SELECT COUNT(*) as count FROM users WHERE created_at < NOW() - INTERVAL 1 YEAR", []);
        
        if ($previousCount == 0) {
            return 0; // Avoid division by zero
        }

        return (($currentCount - $previousCount) / $previousCount) * 100; // Return growth percentage
    }

    /**
     * Gets the total count of users in the database.
     * @return int The total user count.
     */
    public function getUserCount(): int {
        $query = "SELECT COUNT(*) as count FROM users"; 
        return $this->fetchSingleValue($query, []);
    }

    /**
     * Gets the count of active users in the database.
     * @return int The count of active users.
     */
    public function getActiveUserCount(): int {
        $query = "SELECT COUNT(*) as count FROM users WHERE active = 1"; 
        return $this->fetchSingleValue($query, []);
    }

    /**
     * Retrieves all users from the database.
     * @return array An array of user data.
     */
    public function getAllUsers(): array {
        $query = "SELECT * FROM users"; 
        return $this->fetchAll($query, []);
    }

    /**
     * Deletes a user by ID.
     * @param int $id The ID of the user to delete.
     * @return bool True if the user was deleted, false otherwise.
     */
    public function deleteUser (int $id): bool {
        $query = "DELETE FROM users WHERE id = ?"; 
        return $this->executeUpdate($query, [$id], "Error deleting user ID $id");
    }

    /**
     * Retrieves a user by ID.
     * @param int $id The ID of the user.
     * @return array|null The user data or null if not found.
     */
    public function getUserById(int $id): ?array {
        $query = "SELECT * FROM users WHERE id = ?"; 
        return $this->fetchSingle($query, [$id], "Error fetching user ID $id");
    }

    /**
     * Updates a user's information.
     * @param int $id The ID of the user to update.
     * @param array $data The new user data.
     * @return bool True if the user was updated, false otherwise.
     */
    public function updateUser (int $id, array $data): bool {
        if (empty($data['username']) || empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false; 
        }

        $query = "UPDATE users SET username = ?, email = ? WHERE id = ?"; 
        return $this->executeUpdate($query, [$data['username'], $data['email'], $id], "Error updating user ID $id");
    }

    /**
     * Retrieves registration trends over time.
     * @return array An array of registration trends.
     */
    public function getRegistrationTrends(): array {
        $query = "SELECT DATE(created_at) as date, COUNT(*) as count FROM users GROUP BY DATE(created_at)"; 
        return $this->fetchAll($query, []);
    }

    /**
     * Fetches all records from the database.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array An array of results.
     */
    protected function fetchAll(string $query, array $params = []): array {
        try {
            $stmt = $this->pdo->prepare($query);
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . implode(' ', $this->pdo->errorInfo()));
            }

            if (!$stmt->execute($params)) {
                throw new Exception("Query execution failed: " . implode(' ', $stmt->errorInfo()));
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching data: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetches a single record from the database.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @param string $errorMsg The error message to log on failure.
     * @return array|null The record data or null if not found.
     */
    private function fetchSingle(string $query, array $params, string $errorMsg): ?array {
        try {
            $stmt = $this->executeQuery($query, $params);
            return $stmt instanceof PDOStatement ? $stmt->fetch(PDO::FETCH_ASSOC) : null; 
        } catch (Exception $e) {
            error_log("$errorMsg: " . $e->getMessage());
            return null; 
        }
    }

    /**
     * Fetches a single value from the database.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return int The fetched value.
     */
    protected function fetchSingleValue(string $query, array $params = []): mixed {
        try {
            $stmt = $this->pdo->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement.");
            }
            if (!$stmt->execute($params)) {
                throw new Exception("Query execution failed.");
            }
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Error fetching single value: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Executes an update query.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @param string $errorMsg The error message to log on failure.
     * @return bool True if the update was successful, false otherwise.
     */
    private function executeUpdate(string $query, array $params, string $errorMsg): bool {
        try {
            $stmt = $this->executeQuery($query, $params);
            return $stmt instanceof PDOStatement && $stmt->rowCount() > 0; 
        } catch (Exception $e) {
            error_log("$errorMsg: " . $e->getMessage());
            return false; 
        }
    }
}