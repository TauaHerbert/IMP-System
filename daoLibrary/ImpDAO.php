<?php
require_once 'conection.php';
require_once 'modelsLibrary/Imp.php';

class ImpDAO {
    
    public function cadastrar(Imp $impressora) {
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

    public function listarTodas() {
        try {
            $pdo = Conexao::getConexao();
            
            $sql = "SELECT 
                        i.id, 
                        i.modelo,
                        d.nome AS setor, 
                        i.ip, 
                        i.serial, 
                        i.tipo_cor,
                        (SELECT MAX(quantidade_impressoes) FROM historico_leituras hl WHERE hl.id_impressora = i.id) AS ultima_leitura,
                        (SELECT MAX(data_verificacao) FROM historico_leituras hl WHERE hl.id_impressora = i.id) AS ultima_data_leitura,
                        (SELECT MAX(data_troca) FROM historico_trocas ht WHERE ht.id_impressora = i.id) AS ultima_data_troca
                    FROM impressoras i
                    INNER JOIN departamentos d ON i.id_departamento = d.id
                    ORDER BY d.nome ASC, i.modelo ASC";
            
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro interno ao listar impressoras: " . $e->getMessage();
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT i.id, i.modelo, i.serial, d.nome AS setor 
                    FROM impressoras i 
                    INNER JOIN departamentos d ON i.id_departamento = d.id 
                    WHERE i.id = :id";
        
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar impressora: " . $e->getMessage();
            return null;
        }
    }

}
?>