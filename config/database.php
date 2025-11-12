<?php


class Database
{
    private $host = 'mysql';
    private $dbname = 'd6_db';
    private $username = 'user';
    private $password = 'password';
    private $conn = null;

    /**
     * Get database connection
     * @return PDO
     * @throws PDOException
     */
    public function getConnection()
    {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new PDOException("Connection failed: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
