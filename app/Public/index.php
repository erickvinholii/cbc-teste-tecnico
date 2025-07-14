<?php
header('Content-Type: application/json');
require_once '../app/Controllers/ClubController.php';
require_once '../app/Controllers/ResourceController.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($path) {
    case 'clubes':
        $controller = new ClubController();
        if ($method === 'GET') {
            $controller->listar();
        } elseif ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $controller->cadastrar($data);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido.']);
        }
        break;
    case 'consumir':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $controller = new ResourceController();
            $controller->consumir($data);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido.']);
        }
        break;
    case 'recursos':
        $controller = new ResourceController();
        if ($method === 'GET') {
            $controller->listar();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido.']);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint não encontrado.']);
        break;
}
