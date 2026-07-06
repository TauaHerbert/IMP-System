<?php

class Leitura {
    private $id;
    private $id_impressora;
    private $quantidade_impressoes;
    private $data_verificacao;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getIdImpressora() { return $this->id_impressora; }
    public function setIdImpressora($id_impressora) { $this->id_impressora = $id_impressora; }

    public function getQuantidadeImpressoes() { return $this->quantidade_impressoes; }
    public function setQuantidadeImpressoes($quantidade_impressoes) { $this->quantidade_impressoes = $quantidade_impressoes; }

    public function getDataVerificacao() { return $this->data_verificacao; }
    public function setDataVerificacao($data_verificacao) { $this->data_verificacao = $data_verificacao; }
}
?>