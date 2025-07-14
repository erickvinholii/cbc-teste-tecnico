<?php
require_once 'database.php';

function listarClubes() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT id, nm_nome, val_saldo_disponivel FROM tbl_clubes");
    $clubes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clubes);
}

function cadastrarClube($data) {
    if (empty($data['clube']) || empty($data['saldo_disponivel']) || !is_numeric($data['saldo_disponivel'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos obrigatórios: clube e saldo_disponivel (numérico).']);
        return;
    }

    $pdo = getDbConnection();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO tbl_clubes (nm_nome, val_saldo_disponivel) VALUES (:clube, :saldo)");
        $stmt->execute([':clube' => $data['clube'], ':saldo' => (float)$data['saldo_disponivel']]);
        $pdo->commit();
        http_response_code(200);
        echo json_encode(['ok' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Falha ao cadastrar: ' . $e->getMessage()]);
    }
}

function consumirRecurso($data) {
    if (empty($data['clube_id']) || empty($data['recurso_id']) || empty($data['valor_consumo']) || !is_numeric($data['valor_consumo'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos obrigatórios: clube_id, recurso_id, valor_consumo (numérico).']);
        return;
    }

    $valorConsumo = (float)$data['valor_consumo'];
    if ($valorConsumo <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Valor de consumo deve ser positivo.']);
        return;
    }

    $pdo = getDbConnection();
    $pdo->beginTransaction();
    try {
        $stmtClube = $pdo->prepare("SELECT val_saldo_disponivel, nm_nome FROM tbl_clubes WHERE id = :id FOR UPDATE");
        $stmtClube->execute([':id' => $data['clube_id']]);
        $clube = $stmtClube->fetch(PDO::FETCH_ASSOC);
        if (!$clube) throw new Exception('Clube não encontrado.');
        $saldoClube = (float)$clube['val_saldo_disponivel'];

        $stmtRecurso = $pdo->prepare("SELECT val_saldo_disponivel FROM tbl_recursos WHERE id = :id FOR UPDATE");
        $stmtRecurso->execute([':id' => $data['recurso_id']]);
        $recurso = $stmtRecurso->fetch(PDO::FETCH_ASSOC);
        if (!$recurso) throw new Exception('Recurso não encontrado.');
        $saldoRecurso = (float)$recurso['val_saldo_disponivel'];

        if ($saldoClube < $valorConsumo) throw new Exception('O saldo disponível do clube é insuficiente.');
        if ($saldoRecurso < $valorConsumo) throw new Exception('O saldo disponível do recurso é insuficiente.');

        $novoSaldoClube = $saldoClube - $valorConsumo;
        $novoSaldoRecurso = $saldoRecurso - $valorConsumo;

        $pdo->prepare("UPDATE tbl_clubes SET val_saldo_disponivel = :saldo WHERE id = :id")
            ->execute([':saldo' => $novoSaldoClube, ':id' => $data['clube_id']]);

        $pdo->prepare("UPDATE tbl_recursos SET val_saldo_disponivel = :saldo WHERE id = :id")
            ->execute([':saldo' => $novoSaldoRecurso, ':id' => $data['recurso_id']]);

        $pdo->commit();
        http_response_code(200);
        echo json_encode([
            'clube' => $clube['nm_nome'],
            'saldo_anterior' => number_format($saldoClube, 2, ',', ''),
            'saldo_atual' => number_format($novoSaldoClube, 2, ',', '')
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function listarRecursos() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT id, ds_descricao, val_saldo_disponivel FROM tbl_recursos");
    $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($recursos);
}
