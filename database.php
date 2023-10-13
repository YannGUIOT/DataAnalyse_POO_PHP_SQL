<?php
    class Database {
        private $servername = "localhost";
        private $username = "root";
        private $password = "root";
        private $database = "ForceWan3";
        public $conn;

        public function __construct() {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

            if ($this->conn->connect_error) {
                die("La connexion à la base de données a échoué : " . $this->conn->connect_error);
            }
        }

        public function closeConnection() {
            $this->conn->close();
        }
    }
?>
