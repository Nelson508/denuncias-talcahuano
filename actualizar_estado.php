<?php
// actualizar_estado.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

if ($id <= 0 || $estado === '') {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$archivo = __DIR__ . '/data/denuncias.json';
if (!file_exists($archivo)) {
    echo json_encode(['ok' => false, 'error' => 'Archivo de denuncias no encontrado']);
    exit;
}

$data = json_decode(file_get_contents($archivo), true);
if (!is_array($data)) {
    echo json_encode(['ok' => false, 'error' => 'Error al leer denuncias']);
    exit;
}

$encontrado = false;
foreach ($data as &$d) {
    if ((int)$d['id'] === $id) {
        $d['estado'] = $estado;
        $encontrado = true;
        break;
    }
}
unset($d);

if (!$encontrado) {
    echo json_encode(['ok' => false, 'error' => 'Denuncia no encontrada']);
    exit;
}

// Guardar archivo
file_put_contents($archivo, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['ok' => true]);
exit;
