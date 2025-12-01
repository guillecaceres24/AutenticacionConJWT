<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

function validarToken() {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (empty($auth_header) || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
        return null;
    }

    $token = $matches[1];
    $payload_json = base64_decode($token);
    $payload = json_decode($payload_json, true);

    if (isset($payload['user']) && isset($payload['exp']) && $payload['exp'] > time()) {
        return $payload['user'];
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Método incorrecto."]);
    exit();
}

$username = validarToken();

if ($username) {
    $hora_actual = date("H:i:s");
    http_response_code(200);
    echo json_encode([
        "message" => "Datos de bienvenida obtenidos con éxito.",
        "username" => $username,
        "hora_actual" => $hora_actual,
        "mensaje_adicional" => "Tu sesión sin estado (stateless) ha sido verificada con éxito."
    ]);
} else {
    http_response_code(403);
    echo json_encode(["message" => "Acceso denegado. Token inválido o expirado."]);
}
?>
