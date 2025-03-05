<?php
// app/models/BaseModel.php
namespace App\Models;
require_once __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;
use PDOException;

class BaseModel {
    protected PDO $pdo;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->logger = new Logger('BaseModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::INFO));
    }

    protected function fetchSingle(string $query, array $params = []): ?array {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->logger->error("Database fetch single error", [
                'query' => $query,
                'params' => $params,
                'message' => $e->getMessage()
            ]);
            throw new Exception("Error fetching single record: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * @throws Exception
     */
    protected function fetchAll(string $query, array $params = []): array {
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logger->error("Database fetch all error", [
                'query' => $query,
                'params' => $params,
                'message' => $e->getMessage()
            ]);
            throw new Exception("Error fetching all records: " . $e->getMessage());
        }
    }

    public function execute(string $query, array $params = []): bool {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Database execute error: " . $e->getMessage());
        }
    }

    protected function getLastInsertId(): int {
        return (int)$this->pdo->lastInsertId();
    }

    protected function beginTransaction(): void {
        $this->pdo->beginTransaction();
        $this->logger->debug("Transaction begun");
    }

    protected function commit(): void {
        $this->pdo->commit();
        $this->logger->debug("Transaction committed");
    }

    protected function rollBack(): void {
        $this->pdo->rollBack();
        $this->logger->debug("Transaction rolled back");
    }

    /**
     * @throws Exception
     */
    protected function fetchPaginated(string $query, array $params = [], int $limit, int $offset): array {
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        return $this->fetchAll($query, $params);
    }
    protected function fetchColumn(string $query, array $array)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($array);
        return $stmt->fetchColumn();
    }

}