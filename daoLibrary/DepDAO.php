<?php

require_once 'conection.php';
require_once 'modelsLibrary/Dep.php';

class DepDAO {
    
    public function cadastrar(Dep $dep) {
        try {
            $pdo = Conexao::getConexao();
            
            $sql = "INSERT INTO departamentos (nome) VALUES (:nome)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $dep->getNome());
            
            return $stmt->execute();

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                return "duplicado";
            }
            
            echo "Erro interno: " . $e->getMessage();
            return false;
        }
    }

    public function listarTodos() {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT id, nome FROM departamentos ORDER BY id ASC";
            $stmt = $pdo->query($sql);
            
            // Retorna um array associativo com todos os departamentos
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            echo "Erro interno ao listar departamentos: " . $e->getMessage();
            return [];
        }
    }

}
?>