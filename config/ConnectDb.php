<?php

class ConnectDb
{
    private $host = "127.0.0.1";
    private $db = "bth_2";                    //tên CSDL 
    private $user = "root";
    private $password = "";
    private $charset = "utf8mb4";

    private $pdo;

    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}