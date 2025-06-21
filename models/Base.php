<?php

namespace Models;

use PDO;
use PDOException;
use Helpers\LoggerHelpers;

class Base
{
    protected PDO $conn;
    protected string $table;

    public function __construct(PDO $conn, string $table)
    {
        $this->conn = $conn;
        $this->table = $table;
    }

    public function countAll(string $whereClause = '', array $params = []): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            if (!empty($whereClause)) {
                $sql .= " WHERE " . $whereClause;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            # Log the success message
            LoggerHelpers::info('countAll@Base Model Counted all records in ' . $this->table);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $th) {
            $errorInfo = $th->errorInfo;
            # Log the error message
            LoggerHelpers::error('countAll@Base Model Count failed: ' . $errorInfo[2]);
            return 0;
        }
    }

    public function getAllData($limit, $offset)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            # Log the success message
            LoggerHelpers::info('getAllData@Base Model Fetched all records from ' . $this->table);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            $errorInfo = $th->errorInfo;
            # Log the error message
            LoggerHelpers::error('getAllData@Base Model Fetch failed: ' . $errorInfo[2]);
            return [];
        }
    }
}
