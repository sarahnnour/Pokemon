<?php

class Bolsa {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Itens que o usuário tem na bolsa
    public function listar($usuarioId) {
        $sql = "SELECT i.id, i.nome, i.descricao, i.tipo, b.quantidade
                FROM bolsa b
                INNER JOIN item i ON i.id = b.item_id
                WHERE b.usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guarda mais uma unidade do item
    public function adicionar($usuarioId, $itemId) {
        $sql = "SELECT quantidade FROM bolsa WHERE usuario_id = :usuario_id AND item_id = :item_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'item_id' => $itemId]);

        if ($stmt->fetchColumn() === false) {
            $sql = "INSERT INTO bolsa (usuario_id, item_id, quantidade) VALUES (:usuario_id, :item_id, 1)";
        } else {
            $sql = "UPDATE bolsa SET quantidade = quantidade + 1
                    WHERE usuario_id = :usuario_id AND item_id = :item_id";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'item_id' => $itemId]);
    }

    // Gasta uma unidade do item (lança Exception se não tiver).
    // Se era a última unidade, o item é apagado da bolsa.
    public function remover($usuarioId, $itemId) {
        $sql = "SELECT quantidade FROM bolsa WHERE usuario_id = :usuario_id AND item_id = :item_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'item_id' => $itemId]);
        $quantidade = $stmt->fetchColumn();

        if ($quantidade === false) {
            throw new Exception("Você não tem esse item na bolsa");
        }

        if ($quantidade == 1) {
            $sql = "DELETE FROM bolsa WHERE usuario_id = :usuario_id AND item_id = :item_id";
        } else {
            $sql = "UPDATE bolsa SET quantidade = quantidade - 1
                    WHERE usuario_id = :usuario_id AND item_id = :item_id";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'item_id' => $itemId]);
    }
}