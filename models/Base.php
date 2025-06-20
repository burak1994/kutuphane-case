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

    public function getAllData($limit, $offset) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}



?>
