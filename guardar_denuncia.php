<?php
session_start();

$denuncia = $_SESSION['denuncia'] ?? null;

if (!$denuncia) {
    // Si no hay nada en sesión, vuelve al inicio
    header('Location: index.php');
    exit;
}

$archivo = __DIR__ . '/data/denuncias.json';
$lista = [];

// Cargamos archivo actual
if (file_exists($archivo)) {
    $json = file_get_contents($archivo);
    $lista = json_decode($json, true) ?: [];
}

// Generamos nuevo ID
$nuevoId = 1;
if (!empty($lista)) {
    $ids = array_column($lista, 'id');
    $nuevoId = max($ids) + 1;
}

// Completamos campos adicionales
$denuncia['id']            = $nuevoId;
$denuncia['fecha_ingreso'] = date('d-m-Y');
$denuncia['fecha_cierre']  = '-';
$denuncia['estado']        = 'Creado';

// Obtener coordenadas reales de la dirección
list($lat, $lon) = geocodeDireccion($denuncia['direccion'] ?? '');

// Guardamos lat/lng (pueden ser null si no se encontró)
$denuncia['lat'] = $lat;
$denuncia['lng'] = $lon;

// Función para obtener lat/lon desde la dirección usando Nominatim (OpenStreetMap)
function geocodeDireccion($direccion) {
    $direccion = trim($direccion);
    if ($direccion === '') {
        return [null, null];
    }

    $direccionLimpia = limpiarDireccion($direccion);

    // Generar consulta solo con lo necesario
    $q = urlencode($direccionLimpia . ', Chile');
    $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=cl&q=$q";

    // Nominatim exige un User-Agent propio
    $opts = [
        'http' => [
            'header' => "User-Agent: DenunciasTalca/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    $resp = @file_get_contents($url, false, $context);
    if ($resp === false) {
        return [null, null];
    }

    $data = json_decode($resp, true);
    if (!$data || count($data) === 0) {
        return [null, null];
    }

    $lat = isset($data[0]['lat']) ? (float)$data[0]['lat'] : null;
    $lon = isset($data[0]['lon']) ? (float)$data[0]['lon'] : null;

    return [$lat, $lon];
}

function limpiarDireccion($dir) {
    // 1. Eliminar código postal (cualquier número grande)
    $dir = preg_replace('/\b\d{6,}\b/', '', $dir);

    // 2. Normalizar acentos
    $dir = str_replace(['í','Í'], ['i','I'], $dir);
    
    // 3. Corregir casos típicos de la región
    $dir = str_ireplace('Region del Bio Bio', '', $dir);
    $dir = str_ireplace('Bio Bio', '', $dir);
    $dir = str_ireplace('Bío Bío', '', $dir);
    $dir = str_ireplace('Biobio', '', $dir);

    // 4. Remover espacios dobles
    $dir = trim(preg_replace('/\s+/', ' ', $dir));

    return $dir;
}

// Agregamos a la lista
$lista[] = $denuncia;

// Guardamos de vuelta en el JSON
file_put_contents($archivo, json_encode($lista, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Limpiamos la denuncia en sesión
unset($_SESSION['denuncia']);

// Redirigimos a seguimiento
header('Location: seguimiento.php');
exit;
