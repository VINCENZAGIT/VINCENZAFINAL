<?php
// Arquivo: controllers/UsuarioController.php
session_start();

// 1. PASTAS COM LETRA MAIÚSCULA (Padrão do seu projeto)
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Models/Usuario.php'; // Model é Singular
require_once __DIR__ . '/../Repositories/UsuarioRepository.php';
require_once __DIR__ . '/../repositories/EmailRepository.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../services/EmailService.php';

class UsuarioController {
    private $repo;
    private $emailRepo;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->repo = new UsuarioRepository($db);
        $this->emailRepo = new EmailRepository($db);
    }

    // 2. CAMINHO DA VIEW CORRIGIDO
    public function index() {
        // Pasta 'Views' (Maiúscula) -> Pasta 'Usuarios' (Maiúscula e Plural)
        require_once __DIR__ . '/../Views/Usuarios/index.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validação completa usando ValidationService
            $validacao = ValidationService::validarRegistroCompleto($_POST);
            
            if (!$validacao['valido']) {
                echo "<script>alert('{$validacao['mensagem']}'); window.history.back();</script>";
                return;
            }

            $usuario = new Usuario();
            $usuario->nome = $_POST['nome'];
            $usuario->data_nascimento = $_POST['nascimento'];
            $usuario->email = $_POST['email'];
            $usuario->telefone = $_POST['telefone'];
            $usuario->senha = $_POST['senha'];

            if ($this->repo->criar($usuario)) {
                // Buscar o usuário recém-criado para pegar o ID
                $usuarioCriado = $this->repo->buscarPorEmail($usuario->email);
                
                if ($usuarioCriado) {
                    // Enviar email de boas-vindas
                    $resultadoEmail = EmailService::emailBoasVindas($usuario->nome, $usuario->email);
                    
                    // Log do email
                    $this->emailRepo->logEmail(
                        $usuarioCriado->id, 
                        'boas_vindas', 
                        'Bem-vindo à VINCENZA!', 
                        $resultadoEmail['sucesso'] ? 'enviado' : 'erro'
                    );
                    
                    // Salvar preferências padrão de email
                    $this->emailRepo->salvarPreferencias($usuarioCriado->id, true, ['ofertas', 'novidades']);
                }
                
                echo "<script>alert('Cadastro realizado com sucesso! Verifique seu email.'); window.location.href='../index.php';</script>";
            } else {
                echo "<script>alert('Erro: Este email já está cadastrado!'); window.history.back();</script>";
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $usuarioEncontrado = $this->repo->buscarPorEmail($email);

            if ($usuarioEncontrado && password_verify($senha, $usuarioEncontrado->senha)) {
                $_SESSION['usuario_id'] = $usuarioEncontrado->id;
                $_SESSION['usuario_nome'] = $usuarioEncontrado->nome;
                $_SESSION['usuario_email'] = $usuarioEncontrado->email;
                
                // Redireciona para perfil após login
                header('Location: ../perfil.php');
                exit;
            } else {
                echo "<script>alert('Email ou senha incorretos!'); window.history.back();</script>";
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: ../index.php');
    }
    
    public function alterarSenha() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
            return;
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado']);
            return;
        }
        
        // Buscar usuário atual
        $usuario = $this->repo->buscarPorEmail($_SESSION['usuario_email']);
        if (!$usuario) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
            return;
        }
        
        // Validar usando ValidationService
        $validacao = ValidationService::validarRedefinicaoSenha(
            $_POST['senha_atual'],
            $_POST['nova_senha'], 
            $_POST['confirmar_senha'],
            $usuario->senha
        );
        
        if (!$validacao['valido']) {
            echo json_encode(['sucesso' => false, 'mensagem' => $validacao['mensagem']]);
            return;
        }
        
        // Atualizar senha
        if ($this->repo->atualizarSenha($_SESSION['usuario_id'], $_POST['nova_senha'])) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Senha alterada com sucesso!']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao alterar senha']);
        }
    }
    
    public function salvarPerfil() {
        header('Content-Type: application/json');
        
        // Debug temporário
        file_put_contents('debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
        file_put_contents('debug.log', "Session: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
            return;
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado']);
            return;
        }
        
        try {
            // Salvar preferências de email
            $emailsPromocionais = isset($_POST['emails_promocionais']) && $_POST['emails_promocionais'] === 'true';
            $tiposEmailString = $_POST['tipos_email'] ?? '';
            $tiposEmail = !empty($tiposEmailString) ? explode(',', $tiposEmailString) : [];
            
            // Debug adicional
            file_put_contents('debug.log', "Emails promocionais: " . ($emailsPromocionais ? 'true' : 'false') . "\n", FILE_APPEND);
            file_put_contents('debug.log', "Tipos email: " . print_r($tiposEmail, true) . "\n", FILE_APPEND);
            
            $resultado = $this->emailRepo->salvarPreferencias(
                $_SESSION['usuario_id'], 
                $emailsPromocionais, 
                $tiposEmail
            );
            
            file_put_contents('debug.log', "Resultado salvamento: " . ($resultado ? 'sucesso' : 'erro') . "\n", FILE_APPEND);
            
            if ($resultado) {
                echo json_encode(['sucesso' => true, 'mensagem' => 'Preferências salvas com sucesso!']);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar preferências']);
            }
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno do servidor']);
        }
    }
    
    public function enviarFeedback() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
            return;
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado']);
            return;
        }
        
        $texto = trim($_POST['feedback_texto'] ?? '');
        $tipo = $_POST['feedback_tipo'] ?? 'sugestao';
        
        if (empty($texto)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, escreva sua mensagem']);
            return;
        }
        
        try {
            $titulo = ucfirst($tipo) . ': ' . substr($texto, 0, 50) . '...';
            $resultado = $this->emailRepo->logEmail(
                $_SESSION['usuario_id'],
                'feedback_' . $tipo,
                $titulo,
                'enviado'
            );
            
            if ($resultado) {
                echo json_encode(['sucesso' => true, 'mensagem' => 'Feedback enviado com sucesso!']);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao enviar feedback']);
            }
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno']);
        }
    }
}
?>