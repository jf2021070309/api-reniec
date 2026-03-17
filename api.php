<?php
header('Content-Type: application/json; charset=utf-8');

$dni = $_GET['dni'] ?? null;

if (!$dni || strlen($dni) !== 8 || !is_numeric($dni)) {
    sendResponse(false, 'DNI inválido. Debe tener 8 dígitos numéricos.', 400);
}

const SEARCH_URL = 'https://eldni.com/pe/buscar-datos-por-dni';
$cookieFile = tempnam(sys_get_temp_dir(), 'cookie');
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';

// PASO 1: Obtener el Token CSRF
$ch = curl_init(SEARCH_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR      => $cookieFile,
    CURLOPT_USERAGENT      => $userAgent,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$html = curl_exec($ch);
curl_close($ch);

if (!preg_match('/name="_token"\s+value="([^"]+)"/i', $html, $tokenMatches)) {
    cleanupAndExit($cookieFile, 'No se pudo obtener el token de seguridad.', 500);
}
$csrfToken = $tokenMatches[1];

// PASO 2: Realizar la búsqueda por POST
$ch = curl_init(SEARCH_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query(['_token' => $csrfToken, 'dni' => $dni]),
    CURLOPT_COOKIEFILE     => $cookieFile,
    CURLOPT_USERAGENT      => $userAgent,
    CURLOPT_REFERER        => SEARCH_URL,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

unlink($cookieFile);

if ($err) {
    sendResponse(false, 'Error al conectar con el servidor externo: ' . $err, 500);
}

// PASO 3: Extraer datos
// Intento 1: Tabla de resultados
if (preg_match('/<td>'.$dni.'<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>/is', $response, $m)) {
    sendResponse(true, null, 200, [
        'nombres'         => clean($m[1]),
        'apellidoPaterno' => clean($m[2]),
        'apellidoMaterno' => clean($m[3])
    ]);
} 
// Intento 2: Clase samp (formato alternativo)
elseif (preg_match('/<samp class="inline-block">(.*?)<\/samp>/is', $response, $sm)) {
    $fullName = clean($sm[1]);
    $parts = explode(' ', $fullName);
    
    if (count($parts) >= 3) {
        $am = array_pop($parts);
        $ap = array_pop($parts);
        $n  = implode(' ', $parts);
        sendResponse(true, null, 200, ['nombres' => $n, 'apellidoPaterno' => $ap, 'apellidoMaterno' => $am]);
    } else {
        sendResponse(true, null, 200, ['nombres' => $fullName, 'apellidoPaterno' => '', 'apellidoMaterno' => '']);
    }
}

sendResponse(false, "No se encontraron resultados para el DNI $dni", 404);

// --- Helpers ---

function clean($str) {
    return trim(strip_tags($str));
}

function sendResponse($success, $message = null, $code = 200, $data = null) {
    http_response_code($code);
    $res = ['success' => $success];
    if ($message) $res['message'] = $message;
    if ($data) $res['data'] = $data;
    echo json_encode($res);
    exit;
}

function cleanupAndExit($file, $msg, $code) {
    if (file_exists($file)) unlink($file);
    sendResponse(false, $msg, $code);
}
?>
