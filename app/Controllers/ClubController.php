<?php
require_once '../core/Database.php';
require_once '../app/Models/Club.php';

class ClubController {
    public function listar() {
        $club = new Club();
        echo json_encode($club->getAll());
    }

    public function cadastrar($data) {
        if (empty($data['clube']) || empty($data['saldo_disponivel']) || !is_numeric($data['saldo_disponivel'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campos obrigatórios: clube e saldo_disponivel (numérico).']);
            return;
        }

        $club = new Club();
        if ($club->create($data['clube'], $data['saldo_disponivel'])) {
            http_response_code(200);
            echo json_encode(['ok' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao cadastrar.']);
        }
    }
}
