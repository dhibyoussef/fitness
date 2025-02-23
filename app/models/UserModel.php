<?php
// app/models/UserModel.php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
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

    public function getUserById(int $id): ?array {
        $query = "SELECT * FROM users WHERE id = :id AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['id' => $id]);
    }

    public function getUserByEmail(string $email): ?array {
        $query = "SELECT * FROM users WHERE email = :email AND deleted_at IS NULL";
        return $this->fetchSingle($query, ['email' => $email]);
    }

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

    public function deleteUser(int $id): bool {
        $query = "UPDATE users SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->execute($query, ['id' => $id]);
    }

    public function getAllUsers(int $offset, int $limit, string $search = ''): array {
        $query = "SELECT id, username, email, role, created_at, status 
                  FROM users 
                  WHERE (username LIKE :search OR email LIKE :search) AND deleted_at IS NULL 
                  ORDER BY created_at DESC 
                  LIMIT :offset, :limit";
        return $this->fetchAll($query, [
            'search' => "%$search%",
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function getUserCount(string $search = ''): int {
        $query = "SELECT COUNT(*) FROM users 
                  WHERE (username LIKE :search OR email LIKE :search) AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['search' => "%$search%"]);
        return (int)$stmt->fetchColumn();
    }

    public function activateUser(int $id): bool {
        $query = "UPDATE users SET status = 'active', updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL AND status = 'pending'";
        return $this->execute($query, ['id' => $id]);
    }

    public function storeRememberMeToken(int $userId, string $token): bool {
        $query = "INSERT INTO remember_me_tokens (user_id, token, expires_at) 
                  VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 DAY)) 
                  ON DUPLICATE KEY UPDATE token = :token, expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY)";
        return $this->execute($query, ['user_id' => $userId, 'token' => $token]);
    }

    public function invalidateAllTokens(int $userId): bool {
        $query = "DELETE FROM remember_me_tokens WHERE user_id = :user_id";
        return $this->execute($query, ['user_id' => $userId]);
    }

    public function storeVerificationToken(string $email, string $token): bool {
        $query = "INSERT INTO verification_tokens (email, token, expires_at) 
                  VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 24 HOUR)) 
                  ON DUPLICATE KEY UPDATE token = :token, expires_at = DATE_ADD(NOW(), INTERVAL 24 HOUR)";
        return $this->execute($query, ['email' => $email, 'token' => $token]);
    }

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

    public function getRegistrationStatistics(): array {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                  FROM users WHERE deleted_at IS NULL 
                  GROUP BY month ORDER BY month ASC LIMIT 12";
        $result = $this->fetchAll($query);
        return array_column($result, 'count', 'month');
    }

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
}