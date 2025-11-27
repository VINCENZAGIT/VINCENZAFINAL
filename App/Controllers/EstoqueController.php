<?php
// controllers/EstoqueController.php

// Importa as dependências (ajuste os caminhos conforme sua estrutura de pastas)
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Repositories/VeiculoRepository.php';

class EstoqueController {

    public function index() {
        // 1. Inicializa a conexão com o banco
        $database = new Database();
        $db = $database->getConnection();

        // 2. Inicializa o repositório
        $veiculoRepo = new VeiculoRepository($db);

        // 3. Captura os filtros da URL (Método GET)
        // Usamos o operador '??' para garantir que não dê erro se o campo estiver vazio
        $filtros = [
            'marca'       => $_GET['marca'] ?? null,
            'modelo'      => $_GET['modelo'] ?? null,
            'ano'         => $_GET['ano'] ?? null,
            'combustivel' => $_GET['combustivel'] ?? null,
            'cambio'      => $_GET['cambio'] ?? null
        ];

        // 4. Busca os veículos usando a lógica do Repository
        // Isso retorna uma lista de OBJETOS do tipo Veiculo (graças ao seu Model)
        $veiculos = $veiculoRepo->buscarComFiltros($filtros);

        // 5. Carrega a View (a página visual)
        // A variável $veiculos estará disponível lá dentro automaticamente
        require_once __DIR__ . '/../views/estoque/index.php';
        // 2. MONTAGEM DO SANDUÍCHE
        require_once __DIR__ . '/../views/partials/header.php';
        
        require_once __DIR__ . '/../views/partials/footer.php';
    }
}