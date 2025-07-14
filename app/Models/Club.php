<?php
class Club {
    private $pdo;

    public function __construct() {
        $this->pdo = getDbConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT id, nm_nome, val_saldo_disponivel FROM tbl_clubes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nome, $saldo) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO tbl_clubes (nm_nome, val_saldo_disponivel) VALUES (:nome, :saldo)");
            $stmt->execute([':nome' => $nome, ':saldo' => $saldo]);
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
