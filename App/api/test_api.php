<?php
// Teste simples da API
require_once 'controllers/FinanciamentoController.php';

try {
    $controller = new FinanciamentoController();
    echo "Conexão com banco OK!<br>";
    
    // Teste de avaliação
    $dados = [
        'proposta_id' => null,
        'modelo' => 'Honda Civic',
        'quilometragem' => 50000,
        'valor_estimado' => 75000
    ];
    
    $id = $controller->salvarAvaliacaoVeiculo($dados);
    echo "Avaliação salva com ID: " . $id;
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>