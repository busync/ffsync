<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method Not Allowed'
    ]);
    exit;
}

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON'
    ]);
    exit;
}

$headers = getallheaders();
$csrfToken = $headers['X-CSRF'] ?? $headers['x-csrf'] ?? null;

if (!$csrfToken) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'CSRF token required'
    ]);
    exit;
}

require '../../../autoload.php';

use App\Controllers\CsrfController;
use App\Controllers\UserController;
use App\Controllers\FieldsValidator;
use App\Database\Database;

$csrf = new CsrfController();
if (!$csrf->validateToken($csrfToken)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid CSRF token'
    ]);
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$userController = new UserController($pdo);
if ($userController->isAuthenticated()) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'error' => 'Already logged in'
    ]);
    exit;
}

$rules = [
    'username' => [
        'required' => true,
        'minLength' => 4,
        'maxLength' => 255,
        'regex' => '#^[a-zA-Z0-9_]+$#u'
    ],
    'password' => [
        'required' => true,
        'minLength' => 6,
        'maxLength' => 255
    ],
    'repeat_password' => [
        'required' => true,
        'minLength' => 6,
        'maxLength' => 255
    ]
];

$validator = new FieldsValidator($rules, $data);
$validationResult = $validator->validate();

if (!$validationResult['success']) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'errors' => $validationResult['errors']
    ]);
    exit;
}

if ($data['password'] !== $data['repeat_password']) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Passwords do not match'
    ]);
    exit;
}

$result = $userController->register($data['username'], $data['password']);

if ($result['success']) {
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful'
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $result['error']
    ]);
}