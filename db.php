<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        try {
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $this->db = $client->tahfizh_db;
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getDb() {
        return $this->db;
    }
}
?>