<?php
require_once 'conection.php';
require_once __DIR__ . '/../modelsLibrary/H_troca.php';

class H_trocaDAO {
    private $conn;

    public function __construct() {
        $this->conn = Conexao::getConexao();
    }

    public function registrarTroca(Troca $troca) { 
        $sql = "INSERT INTO historico_trocas (id_impressora, data_troca, leitura_na_troca, observacao) 
                VALUES (:id_impressora, NOW(), :leitura_na_troca, :observacao)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_impressora', $troca->getIdImpressora());
        $stmt->bindValue(':leitura_na_troca', $troca->getLeituraNaTroca());
        $stmt->bindValue(':observacao', $troca->getObservacao());

        return $stmt->execute();
    }

    public function buscarUltimaTroca($id_impressora) {
        $sql = "SELECT * FROM historico_trocas 
                WHERE id_impressora = :id_impressora 
                ORDER BY data_troca DESC LIMIT 1";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_impressora', $id_impressora);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarHistorico($id_impressora) {
        $sql = "SELECT data_troca, leitura_na_troca, observacao 
                FROM historico_trocas 
                WHERE id_impressora = :id_impressora 
                ORDER BY data_troca DESC";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_impressora', $id_impressora);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}