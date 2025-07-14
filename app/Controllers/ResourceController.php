<?php
require_once '../core/Database.php';
require_once '../app/Models/Resource.php';

class ResourceController {
    public function listar() {
        $resource = new Resource();
        echo json_encode($resource->getAll());
    }

    public function consumir($data) {
        if (empty($data['clube_id']) || empty($data['recurso_id']) || empty($data['valor_consumo']) || !is_numeric($data['valor_consumo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campos obrigatórios: clube_id, recurso_id, valor_consumo (numérico).']);
            return;
        }

        $resource = new Resource();
        if ($resource->consume($data['clube_id'], $data['recurso_id'], $data['valor_consumo'])) {
            http_response_code(200);
            echo json_encode(['ok' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Erro ao consumir recurso.']);
        }
    }
}
