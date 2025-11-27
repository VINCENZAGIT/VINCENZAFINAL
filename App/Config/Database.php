<?php
// Removi o namespace para facilitar a importação no seu controller atual

class Database {
    private $host = "localhost";
    private $db_name = "vincenza";
    private $username = "root";
    private $password = "mobilelegends@";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // AQUI ESTÁ A CORREÇÃO: Adicionei o ";port=3307" dentro da string de conexão
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=3307;dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>