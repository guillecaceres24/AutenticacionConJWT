<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$usuarios_permitidos = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

function generarToken($username) {
    $payload = [
        'user' => $username,
        'iat' => time(),
        'exp' => time() + (60 * 60)
    ];
    $token_data = base64_encode(json_encode($payload));
    return $token_data;
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

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin();
        break;

    case 'welcome':
        handleWelcome();
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Endpoint no encontrado."]);
        break;
}

function handleLogin() {
    global $usuarios_permitidos;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["message" => "Método incorrecto."]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    $authenticated = false;
    $found_user = '';

    foreach ($usuarios_permitidos as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $authenticated = true;
            $found_user = $username;
            break;
        }
    }

    if ($authenticated) {
        $token = generarToken($found_user);
        http_response_code(200);
        echo json_encode([
            "message" => "Login exitoso",
            "token" => $token,
            "username" => $found_user
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Credenciales inválidas."]);
    }
}

function handleWelcome() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(["message" => "Método incorrecto."]);
        return;
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
}
?>