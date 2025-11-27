<?php
require_once __DIR__ . '/controllers/FinanciamentoController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$avaliacao_id = $_POST['avaliacao_id'] ?? null;
if (!$avaliacao_id) {
    echo json_encode(['error' => 'ID da avaliação não fornecido']);
    exit;
}

$upload_dir = __DIR__ . '/uploads/avaliacoes/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$controller = new FinanciamentoController();
$fotos_salvas = [];

try {
    foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['fotos']['error'][$key] === UPLOAD_ERR_OK) {
            $nome_original = $_FILES['fotos']['name'][$key];
            $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
            $nome_arquivo = uniqid() . '.' . $extensao;
            $caminho_completo = $upload_dir . $nome_arquivo;
            
            if (move_uploaded_file($tmp_name, $caminho_completo)) {
                $controller->salvarFotoAvaliacao($avaliacao_id, $nome_arquivo, 'uploads/avaliacoes/' . $nome_arquivo);
                $fotos_salvas[] = $nome_arquivo;
            }
        }
    }
    
    echo json_encode(['success' => true, 'fotos' => $fotos_salvas]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>