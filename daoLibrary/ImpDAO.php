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

    public function buscarId($id) {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT i.*, d.nome as setor 
                    FROM impressoras i 
                    LEFT JOIN departamentos d ON i.id_departamento = d.id 
                    WHERE i.id = :id";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar impressora pelo ID: " . $e->getMessage();
            return null;
        }
    }

    public function listarTodasComStatus() {
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
                        (SELECT MAX(data_troca) FROM historico_trocas ht WHERE ht.id_impressora = i.id) AS ultima_data_troca,
                        (SELECT quantidade_impressoes FROM historico_leituras hl WHERE hl.id_impressora = i.id ORDER BY data_verificacao DESC LIMIT 1) AS leitura_atual,
                        (SELECT leitura_na_troca FROM historico_trocas ht WHERE ht.id_impressora = i.id ORDER BY data_troca DESC LIMIT 1) AS marco_zero
                    FROM impressoras i
                    INNER JOIN departamentos d ON i.id_departamento = d.id
                    WHERE i.status = 'ATIVA' 
                    ORDER BY d.nome ASC, i.modelo ASC";
            
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro interno ao listar impressoras com status: " . $e->getMessage();
            return [];
        }
    }

    public function arquivar($id) {
        
        try {
            $pdo = Conexao::getConexao();

            $sql = "UPDATE impressoras SET status = 'INATIVA' WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao arquivar: " . $e->getMessage();
            return false;
        }
    }

    public function reativar($id) {
        
        try {
            $pdo = Conexao::getConexao();

            $sql = "UPDATE impressoras SET status = 'ATIVA' WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao reativar: " . $e->getMessage();
            return false;
        }
    }

    public function excluirDefinitivo($id) {
        
        try {
            $pdo = Conexao::getConexao();

            $sql = "DELETE FROM impressoras WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao excluir: " . $e->getMessage();
            return false;
        }
    }

    public function listarInativas() {

    try{
        $pdo = Conexao::getConexao();
        $sql = "SELECT * FROM impressoras WHERE status = 'INATIVA' ORDER BY modelo ASC";
        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }catch (PDOException $e){
        echo "Erro ao listar impressoras inativas: " . $e->getMessage();

    }
        
    }

}
?>