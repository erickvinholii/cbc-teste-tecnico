<?php
class Resource {
    private $pdo;

    public function __construct() {
        $this->pdo = getDbConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT id, ds_descricao, val_saldo_disponivel FROM tbl_recursos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consume($clubeId, $recursoId, $valorConsumo) {
        $this->pdo->beginTransaction();
        try {
            $stmtClube = $this->pdo->prepare("SELECT val_saldo_disponivel FROM tbl_clubes WHERE id = :id FOR UPDATE");
            $stmtClube->execute([':id' => $clubeId]);
            $clube = $stmtClube->fetch(PDO::FETCH_ASSOC);
            if (!$clube || $clube['val_saldo_disponivel'] < $valorConsumo) {
                throw new Exception('Saldo insuficiente.');
            }

            $stmtRecurso = $this->pdo->prepare("SELECT val_saldo_disponivel FROM tbl_recursos WHERE id = :id FOR UPDATE");
            $stmtRecurso->execute([':id' => $recursoId]);
            $recurso = $stmtRecurso->fetch(PDO::FETCH_ASSOC);
            if (!$recurso || $recurso['val_saldo_disponivel'] < $valorConsumo) {
                throw new Exception('Saldo insuficiente.');
            }

            $novoSaldoClube = $clube['val_saldo_disponivel'] - $valorConsumo;
            $novoSaldoRecurso = $recurso['val_saldo_disponivel'] - $valorConsumo;

            $this->pdo->prepare("UPDATE tbl_clubes SET val_saldo_disponivel = :saldo WHERE id = :id")
                ->execute([':saldo' => $novoSaldoClube, ':id' => $clubeId]);

            $this->pdo->prepare("UPDATE tbl_recursos SET val_saldo_disponivel = :saldo WHERE id = :id")
                ->execute([':saldo' => $novoSaldoRecurso, ':id' => $recursoId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
