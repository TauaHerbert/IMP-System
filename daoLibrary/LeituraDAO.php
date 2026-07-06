<?php
require_once 'conection.php';
require_once 'modelsLibrary/Leitura.php';

class LeituraDAO {
    
    public function cadastrar(Leitura $leitura) {
        try {
            $pdo = Conexao::getConexao();
            
            $sql = "INSERT INTO historico_leituras (id_impressora, quantidade_impressoes, data_verificacao) 
                    VALUES (:id_impressora, :quantidade_impressoes, CURDATE())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_impressora', $leitura->getIdImpressora(), PDO::PARAM_INT);
            $stmt->bindValue(':quantidade_impressoes', $leitura->getQuantidadeImpressoes(), PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao registrar leitura: " . $e->getMessage();
            return false;
        }
    }
}
?>