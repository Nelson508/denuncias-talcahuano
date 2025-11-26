<?php
// autocomplete.php

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '' || mb_strlen($q) < 3) {
    echo json_encode([]);
    exit;
}

// URL a Nominatim (OpenStreetMap)
$url = 'https://nominatim.openstreetmap.org/search?format=json'
     . '&addressdetails=1&countrycodes=cl&limit=5&q='
     . urlencode($q);

// Nominatim exige un User-Agent propio
$opts = [
    'http' => [
        'header' => "User-Agent: DenunciasTalca/1.0\r\n"
    ]
];

$context = stream_context_create($opts);
$respuesta = @file_get_contents($url, false, $context);

if ($respuesta === false) {
    echo json_encode([]);
    exit;
}

echo $respuesta;
exit;
