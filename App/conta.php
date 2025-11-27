<?php
// Arquivo: conta.php (na raiz)

// Configurações de erro para a gente ver se algo der errado
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CAMINHO CORRIGIDO: 'Controllers' com C maiúsculo
require_once __DIR__ . '/Controllers/UsuarioController.php';

$controller = new UsuarioController();

// Roteamento
$acao = $_GET['acao'] ?? '';

if ($acao === 'registrar') {
    $controller->registrar();
} elseif ($acao === 'login') {
    $controller->login();
} elseif ($acao === 'logout') {
    $controller->logout();
} elseif ($acao === 'alterar_senha') {
    $controller->alterarSenha();
} elseif ($acao === 'salvar_perfil') {
    $controller->salvarPerfil();
} elseif ($acao === 'enviar_feedback') {
    $controller->enviarFeedback();
} else {
    $controller->index();
}
?>