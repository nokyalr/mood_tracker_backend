<?php
class Database {
    private $host = "localhost";
    private $db_name = "mood_tracker";
    private $username = "root"; // Sesuaikan dengan konfigurasi XAMPP Anda
    private $password = ""; // Kosongkan jika menggunakan XAMPP default
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
