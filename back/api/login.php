<?php
// Permitir CORS y establecer headers para la API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar solicitudes preflight de CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Lista de usuarios permitidos
$usuarios_permitidos = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

// Función para generar token JWT (sin firma para simplificar)
function generarToken($username) {
    $payload = [
        'user' => $username,
        'iat' => time(),
        'exp' => time() + (60 * 60)  // Token válido por 1 hora
    ];
    $token_data = base64_encode(json_encode($payload));
    return $token_data;
}

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Método incorrecto."]);
    exit();
}

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$authenticated = false;
$found_user = '';

// Verificar credenciales contra la lista de usuarios
foreach ($usuarios_permitidos as $user) {
    if ($user['username'] === $username && $user['password'] === $password) {
        $authenticated = true;
        $found_user = $username;
        break;
    }
}

// Retornar respuesta
if ($authenticated) {
    $token = generarToken($found_user);
    http_response_code(200);
    echo json_encode([
        "message" => "Login exitoso",
        "token" => $token,
        "username" => $found_user
    ]);
} else {
    // Credenciales inválidas
    http_response_code(401);
    echo json_encode(["message" => "Credenciales inválidas."]);
}
?>
