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

    public function listarHistorico($id_impressora) {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT quantidade_impressoes as quantidade, data_verificacao 
                    FROM historico_leituras 
                    WHERE id_impressora = :id_impressora 
                    ORDER BY data_verificacao DESC";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_impressora', $id_impressora, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarUltimaLeitura($id_impressora) {
        try {

            $pdo = Conexao::getConexao();
            
            $sql = "SELECT quantidade_impressoes as quantidade 
                    FROM historico_leituras 
                    WHERE id_impressora = :id_impressora 
                    ORDER BY data_verificacao DESC LIMIT 1";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_impressora', $id_impressora, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado ? $resultado['quantidade'] : 0; 
            
        } catch (PDOException $e) {
            echo "Erro ao buscar última leitura: " . $e->getMessage();
            return 0;
        }
    }

    public function listarPorImpressora($id_impressora) {
        try {
            $sql = "SELECT quantidade_impressoes, data_verificacao FROM historico_leituras WHERE id_impressora = :id ORDER BY data_verificacao DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id_impressora, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function registrarLeitura($id_impressora, $quantidade) {
        try {
            $pdo = Conexao::getConexao();
            $sql = "INSERT INTO historico_leituras (id_impressora, data_verificacao, quantidade_impressoes) 
                    VALUES (:id_impressora, NOW(), :quantidade)";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_impressora', $id_impressora, PDO::PARAM_INT);
            $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao registrar leitura: " . $e->getMessage();
            return false;
        }
    }
}
?>