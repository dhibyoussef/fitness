<?php
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