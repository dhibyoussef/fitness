<?php
namespace App\Models;
use Exception;
use PDO;

require_once 'BaseModel.php';
class ProfileModel extends BaseModel {
    /**
     * Retrieves a user profile by user ID.
     *
     * @param int $userId The user's ID.
     * @return array The user's profile data as an associative array.
     * @throws Exception If the profile is not found.
     */
    public function getProfileByUserId(int $userId): array {
        $query = "SELECT * FROM users WHERE id = :userId";
        $result = $this->executeQuery($query, ['userId' => $userId]);
        if ($result === null || empty($result)) {
            throw new Exception("Profile not found for user ID: {$userId}");
        }
        return $result;
    }
    /**
     * Executes a prepared statement with the provided SQL query and parameters.
     *
     * @param string $query The SQL query to execute.
     * @param array $params Associative array of parameters to bind to the query.
     * @return array|null The result set as an associative array, or null if no rows are found.
     * @throws Exception If the query fails to execute.
     */
    protected function executeQuery(string $query, array $params): ?array {
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare the SQL statement.");
        }

        if (!$stmt->execute($params)) {
            throw new Exception("Query execution failed.");
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Updates a user's profile information.
     *
     * @param int $userId The ID of the user.
     * @param array $data An associative array containing 'username', 'email', and optionally 'fitness_goals'.
     * @throws Exception If the update fails.
     */
    public function updateProfile(int $userId, array $data): void {
        $query = "UPDATE users SET username = :username, email = :email, fitness_goals = :fitness_goals WHERE id = :userId";
        $parameters = [
            'username' => $data['username'],
            'email' => $data['email'],
            'fitness_goals' => $data['fitness_goals'] ?? null, // Handle optional fitness goals
            'userId' => $userId
        ];
        
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement.");
        }

        $success = $stmt->execute($parameters);
        if (!$success || $stmt->rowCount() === 0) {
            throw new Exception("Failed to update profile for user ID: {$userId}");
        }
    }
}