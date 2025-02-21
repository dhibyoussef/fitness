<?php
require_once 'BaseModel.php';

class UserModel extends BaseModel {
    /**
     * Creates a new user in the database.
     * @param array $data Associative array containing user data.
     * @return bool Returns true on success or false on failure.
     */
    public function createUser (array $data): bool {
        $this->requireAdminAuth();
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $params = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ];
        $stmt = $this->executeQuery($query, $params);
        return $stmt instanceof PDOStatement && $stmt->rowCount() > 0;
    }

    /**
     * Retrieves a user by ID.
     * @param int $id The ID of the user.
     * @return array|null The user data or null if not found.
     */
    public function getUserById(int $id): ?array {
        $this->requireAdminAuth();
        $query = "SELECT * FROM users WHERE id = :id";
        return $this->fetchSingle($query, ['id' => $id]);
    }

    /**
     * Retrieves a user by email.
     * @param string $email The email of the user.
     * @return array|null The user data or null if not found.
     */
    public function getUserByEmail(string $email): ?array {
        $this->requireAdminAuth();
        $query = "SELECT * FROM users WHERE email = :email";
        return $this->fetchSingle($query, ['email' => $email]);
    }

    /**
     * Updates a user's information.
     * @param int $id The ID of the user to update.
     * @param array $data Associative array containing updated user data.
     * @return bool Returns true on success or false on failure.
     */
    public function updateUser (int $id, array $data): bool {
        $this->requireAdminAuth();
        $query = "UPDATE users SET username = :username, email = :email WHERE id = :id";
        $params = [
            'username' => $data['username'],
            'email' => $data['email'],
            'id' => $id
        ];
        $stmt = $this->executeQuery($query, $params);
        return $stmt instanceof PDOStatement && $stmt->rowCount() > 0;
    }

    /**
     * Deletes a user from the database.
     * @param int $id The ID of the user to delete.
     * @return bool Returns true on success or false on failure.
     */
    public function deleteUser (int $id): bool {
        $this->requireAdminAuth();
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->executeQuery($query, ['id' => $id]);
        return $stmt instanceof PDOStatement && $stmt->rowCount() > 0;
    }

    /**
     * Retrieves all users from the database.
     * @return array An array of user data.
     */
    public function getAllUsers(): array {
        $this->requireAdminAuth();
        $query = "SELECT * FROM users";
        return $this->fetchAll($query);
    }

    /**
     * Activates a user by ID.
     * @param int $id The ID of the user to activate.
     * @return bool Returns true on success or false on failure.
     */
    public function activateUser (int $id): bool {
        $this->requireAdminAuth();
        $query = "UPDATE users SET active = 1 WHERE id = :id";
        $stmt = $this->executeQuery($query, ['id' => $id]);
        return $stmt instanceof PDOStatement && $stmt->rowCount() > 0;
    }

    /**
     * Stores a remember me token for a user.
     * @param int $userId The ID of the user.
     * @param string $token The remember me token.
     * @return bool Returns true on success or false on failure.
     */
    public function storeRememberMeToken(int $userId, string $token): bool {
        $query = "INSERT INTO remember_me_tokens (user_id, token) VALUES (:user_id, :token)";
        $params = [
            'user_id' => $userId,
            'token' => $token
        ];
        $stmt = $this->executeQuery($query, $params);
        return $stmt instanceof PDOStatement && $stmt->rowCount() > 0;
    }

    /**
     * Stores a verification token for a user.
     * @param string $email The email of the user.
     * @param string $token The verification token.
     * @return bool Returns true on success or false on failure.
     */
    public function storeVerificationToken(string $email, string $token): bool {
        $query = "UPDATE users SET verification_token = :token WHERE email = :email";
        $params = [
            'email' => $email,
            'token' => $token
        ];
        $stmt = $this->executeQuery($query, $params);
        return $stmt instanceof PDOStatement && $stmt->rowCount() > 0;
    }

    /**
     * Retrieves the last inserted ID.
     * @return string|null The last inserted ID or null if not found.
     */
    public function getLastInsertId(): ?string {
        return $this->pdo->lastInsertId();
    }

    /**
     * Retrieves registration statistics.
     * @return array An array of registration statistics.
     */
    public function getRegistrationStatistics(): array {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM users GROUP BY month";
        return $this->fetchAll($query);
    }

    /**
     * Retrieves active user statistics.
     * @return array An array of active user statistics.
     */
    public function getActiveUserStatistics(): array {
        $query = "SELECT DATE_FORMAT(last_login, '%Y-%m') as month, COUNT(*) as count FROM users WHERE active = 1 GROUP BY month";
        return $this->fetchAll($query);
    }

    /**
     * Fetches a single record from the database.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array|null The record data or null if not found.
     */
    private function fetchSingle(string $query, array $params): ?array {
        $stmt = $this->executeQuery($query, $params);
        return $stmt instanceof PDOStatement ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    /**
     * Fetches all records from the database.
     * @param string $query The SQL query to execute.
     * @return array An array of records.
     */
    protected function fetchAll(string $query, array $params = []): array {
        $stmt = $this->executeQuery($query, $params);
        return $stmt instanceof PDOStatement ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Checks if the user has admin privileges.
     * @throws Exception if the user is not authorized.
     */
    private function requireAdminAuth(): void {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            throw new Exception('Unauthorized access');
        }
    }
}
?>