<?php
require_once 'config/Database.php';

class FinanciamentoController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function salvarProposta($dados) {
        try {
            $sql = "INSERT INTO propostas_financiamento 
                    (veiculo_id, cliente_nome, cliente_email, cliente_telefone, 
                     tipo_pagamento, valor_entrada, prazo_meses, taxa_juros, 
                     valor_parcela, valor_total) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $dados['veiculo_id'],
                $dados['cliente_nome'],
                $dados['cliente_email'],
                $dados['cliente_telefone'],
                $dados['tipo_pagamento'],
                $dados['valor_entrada'],
                $dados['prazo_meses'],
                $dados['taxa_juros'],
                $dados['valor_parcela'],
                $dados['valor_total']
            ]);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erro ao salvar proposta: " . $e->getMessage());
        }
    }
    
    public function salvarAvaliacaoVeiculo($dados) {
        try {
            $sql = "INSERT INTO avaliacoes_veiculos 
                    (proposta_id, modelo, quilometragem, valor_estimado) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $dados['proposta_id'],
                $dados['modelo'],
                $dados['quilometragem'],
                $dados['valor_estimado']
            ]);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erro ao salvar avaliação: " . $e->getMessage());
        }
    }
    
    public function salvarFotoAvaliacao($avaliacao_id, $nome_arquivo, $caminho) {
        try {
            $sql = "INSERT INTO fotos_avaliacao (avaliacao_id, nome_arquivo, caminho_arquivo) 
                    VALUES (?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$avaliacao_id, $nome_arquivo, $caminho]);
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao salvar foto: " . $e->getMessage());
        }
    }
    
    public function agendarInspecao($dados) {
        try {
            $sql = "INSERT INTO inspecoes_veiculos 
                    (veiculo_id, cliente_nome, cliente_email, cliente_telefone, 
                     data_agendada, hora_agendada) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $dados['veiculo_id'],
                $dados['cliente_nome'],
                $dados['cliente_email'],
                $dados['cliente_telefone'],
                $dados['data_agendada'],
                $dados['hora_agendada']
            ]);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erro ao agendar inspeção: " . $e->getMessage());
        }
    }
}
?>