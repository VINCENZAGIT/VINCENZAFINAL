<?php

class Veiculo {

    public $id;
    public $marca;
    public $modelo;
    public $ano;
    public $combustivel;
    public $cambio;
    public $preco;
    public $foto;
    public $pasta;
    public $total_imagens;
    public $categoria;

 
    public function getPrecoFormatado() {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }


    public function getNomeCompleto() {
        return $this->marca . ' ' . $this->modelo;
    }
}