<?php
// app/models/UserModel.php
namespace App\Models;
use Exception;
use PDO;
use PDOException;

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    /**
     * @throws Exception
     */
    public function createUser(array $data): bool {
        $query = "INSERT INTO users (username, email, password, role, created_at, updated_at) 
                  VALUES (:username, :email, :password, :role, :created_at, NOW())";
        return $this->execute($query, [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'user',
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s')
        ]);
    }


    /**
     * @throws Exception
     */
    public function getUserById(int $id): ?array {
        $query = "SELECT * FROM users WHERE id = :id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['id' => $id]);
    }

    /**
     * @throws Exception
     */
    public function getUserByEmail(string $email): ?array {
        $query = "SELECT * FROM users WHERE email = :email AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['email' => $email]);
    }

    /**
     * @throws Exception
     */
    public function updateUser(int $id, array $data): bool {
        $query = "UPDATE users 
                  SET username = :username, email = :email, updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, [
            'id' => $id,
            'username' => $data['username'],
            'email' => $data['email']
        ]);
    }

    /**
     * @throws Exception
     */
    public function deleteUser(int $id): bool {
        $query = "UPDATE users SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, ['id' => $id]);
    }

    /**
     * @throws Exception
     */
    public function getAllUsers(int $offset, int $perPage, string $search = ''): array {
        $query = "
            SELECT id, username, email, role, created_at, status 
            FROM users 
            WHERE (username LIKE :search OR email LIKE :search_term) 
            AND deleted_at IS NULL 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ";
        $params = [
            ':search' => "%$search%",
            ':search_term' => "%$search%",  // Explicitly bind the second instance
            ':limit' => $perPage,
            ':offset' => $offset
        ];

        try {
            return $this->fetchAll($query, $params);
        } catch (\Exception $e) {
            $this->logger->error("User fetch error in getAllUsers", [
                'query' => $query,
                'params' => $params,
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getUserCount(string $search = ''): int {
        $query = "
            SELECT COUNT(*) 
            FROM users 
            WHERE (username LIKE :search OR email LIKE :search_term) 
            AND deleted_at IS NULL
        ";
        $params = [
            ':search' => "%$search%",
            ':search_term' => "%$search%"
        ];
        return (int)$this->fetchColumn($query, $params);
    }

    /**
     * @throws Exception
     */
    public function getUsers(): array
    {
        $query = "SELECT id, username, email, role, created_at, status 
                  FROM users 
                  WHERE deleted_at IS NULL 
                  ORDER BY created_at DESC";
        return $this->fetchAll($query);
    }
        


    /**
     * @throws Exception
     */
    public function activateUser(int $id): bool {
        $query = "UPDATE users SET status = 'active', updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL AND status = 'pending'";
        return $this->execute($query, ['id' => $id]);
    }

    /**
     * @throws Exception
     */
    public function storeRememberMeToken(int $userId, string $token): void {
        $query = "INSERT INTO remember_me_tokens (user_id, token, expires_at) 
              VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 DAY)) 
              ON DUPLICATE KEY UPDATE token = :token, expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY)";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error storing remember me token: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function invalidateAllTokens(int $userId): bool {
        $query = "DELETE FROM remember_me_tokens WHERE user_id = :user_id";
        return $this->execute($query, ['user_id' => $userId]);
    }

    /**
     * @throws Exception
     */
    public function storeVerificationToken(string $email, string $token): void {
        $query = "INSERT INTO verification_tokens (email, token, expires_at) 
                  VALUES (:email, :token, NOW() + INTERVAL 1 DAY)";
        $params = ['email' => $email, 'token' => $token];
        try {
            $this->execute($query, $params);
        } catch (Exception $e) {
            throw new Exception("Database execute error: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function verifyUser(string $token): bool {
        $query = "SELECT email FROM verification_tokens WHERE token = :token AND expires_at > NOW()";
        $result = $this->fetchSingle($query, ['token' => $token]);
        if ($result) {
            $updateQuery = "UPDATE users SET status = 'active', updated_at = NOW() 
                            WHERE email = :email AND status = 'pending'";
            $this->execute($updateQuery, ['email' => $result['email']]);
            $this->execute("DELETE FROM verification_tokens WHERE email = :email", ['email' => $result['email']]);
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function getRegistrationStatistics(): array {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                  FROM users WHERE deleted_at IS NULL 
                  GROUP BY month ORDER BY month ASC LIMIT 12";
        $result = $this->fetchAll($query);
        return array_column($result, 'count', 'month');
    }

    /**
     * @throws Exception
     */
    public function getActiveUserStatistics(): array {
        $query = "SELECT DATE(last_activity) as date, COUNT(DISTINCT id) as count 
                  FROM users WHERE last_activity IS NOT NULL AND deleted_at IS NULL 
                  GROUP BY date ORDER BY date DESC LIMIT 7";
        $result = $this->fetchAll($query);
        return array_column($result, 'count', 'date');
    }
    public function getLastInsertedId(): int {
        return $this->pdo->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function getRealTimeUsers(): int {
        $query = "SELECT COUNT(DISTINCT id) FROM users WHERE last_activity > NOW() - INTERVAL 5 MINUTE AND deleted_at IS NULL";
        $result = $this->fetchSingle($query);
        return isset($result['count']) ? (int)$result['count'] : 0;
    }
}