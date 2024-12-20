<?php
session_start(); // Inicia la sesión
$url = 'http://192.168.100.3/Api/login.php';

$data = array('nombre' => 'Salome', 'pass' => '123');
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        'timeout' => 180, // Timeout de 60 segundos
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    die('Error al hacer la solicitud');
}

// Imprimir la respuesta completa para ver qué devuelve el servidor
echo "Respuesta del servidor: ";
var_dump($result);

// Decodificar la respuesta
$response = json_decode($result, true);

if (isset($response['status']) && $response['status'] === 'success') {
    // Si el login es exitoso, almacenar el id_usuario en la sesión
    $_SESSION['id_usuario'] = $response['id_usuario'];

    // Redirigir a get_soil_sensor.php
    header('Location: get_sensor_flores.php');
    exit();
} else {
    // Si la respuesta no fue exitosa, muestra el mensaje de error
    echo isset($response['error']) ? $response['error'] : 'Login fallido';
}
?>
