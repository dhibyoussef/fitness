<?php
declare(strict_types=1);

class BaseModel {
    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Executes a SQL query with prepared statements and fetches a single record from the database.
     * 
     * @param string $query SQL query to be executed.
     * @param array $params Parameters to bind to the query.
     * @return array|null The single record fetched from the database or null if no record is found.
     * @throws Exception If there is an error in preparing or executing the query.
     */
    protected function executeQuery(string $query, array $params = []): ?array {
        $stmt = $this->prepareAndExecuteStatement($query, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Prepares and executes a SQL statement with given parameters.
     * 
     * @param string $query SQL query to be prepared and executed.
     * @param array $params Parameters to bind to the SQL query.
     * @return PDOStatement The prepared and executed statement.
     * @throws Exception If preparing or executing the statement fails.
     */
    private function prepareAndExecuteStatement(string $query, array $params): PDOStatement {
        $stmt = $this->pdo->prepare($query);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . implode(' ', $this->pdo->errorInfo()));
        }

        if (!$stmt->execute($params)) {
            throw new Exception("Query execution failed: " . implode(' ', $stmt->errorInfo()));
        }

        return $stmt;
    }

    /**
     * Fetches all records from the database based on a given query.
     * 
     * @param string $query SQL query to be executed.
     * @param array $params Parameters to bind to the query.
     * @return array An array of records fetched from the database.
     * @throws Exception If there is an error in preparing or executing the query.
     */
    protected function fetchAll(string $query, array $params = []): array {
        $stmt = $this->prepareAndExecuteStatement($query, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches a single value from the database.
     * 
     * @param string $query SQL query to be executed.
     * @param array $params Parameters to bind to the query.
     * @return mixed The single value fetched from the database.
     * @throws Exception If there is an error in preparing or executing the query.
     */
    protected function fetchSingleValue(string $query, array $params = []): mixed {
        $stmt = $this->prepareAndExecuteStatement($query, $params);
        return $stmt->fetchColumn();
    }
}