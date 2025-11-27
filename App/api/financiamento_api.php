<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../controllers/FinanciamentoController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

$controller = new FinanciamentoController();

try {
    switch ($action) {
        case 'salvar_proposta':
            $proposta_id = $controller->salvarProposta($input['dados']);
            echo json_encode(['success' => true, 'proposta_id' => $proposta_id]);
            break;
            
        case 'avaliar_veiculo':
            $valor_estimado = calcularValorEstimado($input['modelo'], $input['quilometragem']);
            echo json_encode(['success' => true, 'valor_estimado' => $valor_estimado]);
            break;
            
        case 'salvar_avaliacao':
            $avaliacao_id = $controller->salvarAvaliacaoVeiculo($input['dados']);
            echo json_encode(['success' => true, 'avaliacao_id' => $avaliacao_id]);
            break;
            
        case 'agendar_inspecao':
            $inspecao_id = $controller->agendarInspecao($input['dados']);
            echo json_encode(['success' => true, 'inspecao_id' => $inspecao_id]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação não reconhecida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function calcularValorEstimado($modelo, $quilometragem) {
    // Lógica simplificada de avaliação
    $valores_base = [
        'civic' => 80000,
        'corolla' => 85000,
        'onix' => 45000,
        'polo' => 55000,
        'gol' => 35000
    ];
    
    $modelo_lower = strtolower($modelo);
    $valor_base = 50000; // Valor padrão
    
    foreach ($valores_base as $key => $valor) {
        if (strpos($modelo_lower, $key) !== false) {
            $valor_base = $valor;
            break;
        }
    }
    
    // Depreciação por quilometragem
    $depreciacao = ($quilometragem / 10000) * 0.05; // 5% a cada 10k km
    $valor_final = $valor_base * (1 - $depreciacao);
    
    return max($valor_final, $valor_base * 0.3); // Mínimo 30% do valor base
}
?>