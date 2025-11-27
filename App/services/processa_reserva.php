<?php
session_start();
require_once 'config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = "INSERT INTO reservas (usuario_id, veiculo_id, data_reserva, horario, observacoes, valor_total, dias) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    
    $codigo = 'RES' . time() . rand(100, 999);
    
    $resultado = $stmt->execute([
        $_SESSION['usuario_id'],
        $_POST['veiculo_id'],
        $_POST['data_retirada'],
        '09:00:00', // Horário padrão
        $_POST['nome'] . ' - ' . $_POST['email'] . ' - ' . $_POST['telefone'],
        $_POST['valor_total'] ?? 0,
        $_POST['dias'] ?? 1
    ]);
    
    if ($resultado) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Reserva confirmada!', 'codigo' => $codigo]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar reserva']);
    }
    
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno do servidor']);
}
?>