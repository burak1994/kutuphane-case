<?php
class Base {
    protected PDO $conn;
    protected string $table;

    public function __construct(PDO $conn, string $table) {
        $this->conn = $conn;
        $this->table = $table;
    }

    public function countAll(string $whereClause = '', array $params = []): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    
}



?>
