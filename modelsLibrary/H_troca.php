<?php

class Troca {
    
    private $id;
    private $id_impressora;
    private $data_troca;
    private $leitura_na_troca;
    private $observacao;


    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getIdImpressora() { return $this->id_impressora; }
    public function setIdImpressora($id_impressora) { $this->id_impressora = $id_impressora; }

    public function getDataTroca() { return $this->data_troca; }
    public function setDataTroca($data_troca) { $this->data_troca = $data_troca; }

    public function getLeituraNaTroca() { return $this->leitura_na_troca; }
    public function setLeituraNaTroca($leitura_na_troca) { $this->leitura_na_troca = $leitura_na_troca; }

    public function getObservacao() { return $this->observacao; }
    public function setObservacao($observacao) { $this->observacao = $observacao; }

}