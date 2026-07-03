<?php
require_once 'conection.php';
require_once 'modelsLibrary/Imp.php';

class ImpDAO {
    
    public function cadastrar(Impressora $impressora) {
        try {
            $pdo = Conexao::getConexao();
            
            $sql = "INSERT INTO impressoras (id_departamento, modelo, ip, serial, tipo_cor) 
                    VALUES (:id_departamento, :modelo, :ip, :serial, :tipo_cor)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindValue(':id_departamento', $impressora->getIdDepartamento());
            $stmt->bindValue(':modelo', $impressora->getModelo());
            $stmt->bindValue(':ip', $impressora->getIp());
            $stmt->bindValue(':serial', $impressora->getSerial());
            $stmt->bindValue(':tipo_cor', $impressora->getTipoCor());
            
            return $stmt->execute();

        } catch (PDOException $e) {

            echo "Erro ao cadastrar impressora: " . $e->getMessage();
            return false;
        }
    }
}
?>