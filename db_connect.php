<?php
class DB_connect{
    private $conn;
    public function connect() {
        require_once 'db_config.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        return $this->conn;
    }
}
?>

