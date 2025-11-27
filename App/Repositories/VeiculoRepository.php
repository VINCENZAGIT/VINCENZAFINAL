<?php
// Arquivo: repositories/VeiculoRepository.php

require_once __DIR__ . '/../Models/Veiculo.php';

class VeiculoRepository {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Busca veículos com filtros dinâmicos
     * @param array $filtros (marca, modelo, ano, combustivel, cambio)
     * @return Veiculo[] Retorna um array de objetos Veiculo
     */
    public function buscarComFiltros($filtros = []) {
        $sql = "SELECT * FROM veiculos WHERE 1=1";
        $params = [];

        // 1. Montagem dinâmica da Query
        if (!empty($filtros['marca'])) {
            $sql .= " AND marca LIKE :marca";
            $params[':marca'] = '%' . $filtros['marca'] . '%';
        }

        if (!empty($filtros['modelo'])) {
            $sql .= " AND modelo LIKE :modelo";
            $params[':modelo'] = '%' . $filtros['modelo'] . '%';
        }

        if (!empty($filtros['ano'])) {
            $sql .= " AND ano = :ano";
            $params[':ano'] = $filtros['ano'];
        }

        if (!empty($filtros['combustivel'])) {
            $sql .= " AND combustivel = :combustivel";
            $params[':combustivel'] = $filtros['combustivel'];
        }

        if (!empty($filtros['cambio'])) {
            $sql .= " AND cambio = :cambio";
            $params[':cambio'] = $filtros['cambio'];
        }

        // Ordenar por mais recentes primeiro (opcional)
        $sql .= " ORDER BY id DESC";

        // 2. Preparação e Execução
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            // 3. Mágica do PDO: Mapeia direto para a classe Veiculo
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'Veiculo');
            
        } catch (PDOException $e) {
            // Em produção, grave isso num log, não mostre na tela
            echo "Erro na consulta: " . $e->getMessage();
            return [];
        }
    }
}