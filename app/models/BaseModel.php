<?php
// app/models/BaseModel.php
require_once __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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

    protected function fetchAll(string $query, array $params = []): array {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new Exception("No records found.");
            }
            return $result;
        } catch (PDOException $e) {
            $this->logger->error("Database fetch all error", [
                'query' => $query,
                'params' => $params,
                'message' => $e->getMessage()
            ]);
            throw new Exception("Error fetching all records: " . $e->getMessage(), (int)$e->getCode());
        } catch (Exception $e) {
            $this->logger->error("General fetch all error", [
                'query' => $query,
                'params' => $params,
                'message' => $e->getMessage()
            ]);
            throw new Exception("Error fetching all records: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    protected function execute(string $query, array $params = []): bool {
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->logger->error("Database execute error", [
                'query' => $query,
                'params' => $params,
                'message' => $e->getMessage()
            ]);
            throw new Exception("Error executing query: " . $e->getMessage(), (int)$e->getCode());
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

    protected function fetchPaginated(string $query, array $params = [], int $limit, int $offset): array {
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        return $this->fetchAll($query, $params);
    }

}