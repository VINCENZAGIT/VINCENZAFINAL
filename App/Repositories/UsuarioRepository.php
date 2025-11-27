<?php
require_once __DIR__ . '/../Models/Usuario.php';

class UsuarioRepository {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Função para REGISTRAR
    public function criar(Usuario $usuario) {
        try {
            $sql = "INSERT INTO usuarios (nome, data_nascimento, email, telefone, senha) VALUES (:nome, :data, :email, :tel, :senha)";
            
            $stmt = $this->conn->prepare($sql);
            
            // Criptografando a senha antes de salvar (Segurança Básica)
            $senhaSegura = password_hash($usuario->senha, PASSWORD_DEFAULT);

            $stmt->bindValue(':nome', $usuario->nome);
            $stmt->bindValue(':data', $usuario->data_nascimento);
            $stmt->bindValue(':email', $usuario->email);
            $stmt->bindValue(':tel', $usuario->telefone);
            $stmt->bindValue(':senha', $senhaSegura);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            die("ERRO NO BANCO DE DADOS: " . $e->getMessage());
            // return false; // Se der erro (ex: email repetido)
        }
    }

    // Função para ajudar no LOGIN (Busca pelo email)
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        
        // Retorna o objeto Usuario preenchido ou false se não achar
        return $stmt->fetchObject('Usuario');
    }
    
    // Função para atualizar senha
    public function atualizarSenha($userId, $novaSenha) {
        try {
            $sql = "UPDATE usuarios SET senha = :senha WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            
            $senhaSegura = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt->bindValue(':senha', $senhaSegura);
            $stmt->bindValue(':id', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>