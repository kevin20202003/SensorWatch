<?php
require '../modelo/conexion.php';

$id_usuario = $_SESSION['id_usuario'];

// Obtener los datos del umbral para el usuario actual
$sql_umbral = "SELECT * FROM umbral_ambiente WHERE id_usuario = $id_usuario";
$result_umbral = $conexion->query($sql_umbral);

// Configuración de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Obtener el correo electrónico del usuario
$sql_usuario = "SELECT correo_electronico FROM usuarios WHERE id_usuario = $id_usuario";
$result_usuario = $conexion->query($sql_usuario);
$correo_usuario = '';
if ($result_usuario->num_rows > 0) {
    $correo_usuario = $result_usuario->fetch_assoc()['correo_electronico'];
}

// Procesar cada umbral
while ($umbral = $result_umbral->fetch_assoc()) {
    $humedad_min = $umbral['humedad_min'];
    $humedad_max = $umbral['humedad_max'];
    $temperatura_min = $umbral['temperatura_min'];
    $temperatura_max = $umbral['temperatura_max'];

    // Obtener los datos más recientes de la tabla datos_suelo
    $sql_datos = "SELECT * FROM datos_ambiente ORDER BY created_at DESC LIMIT 1";
    $result_datos = $conexion->query($sql_datos);

    if ($result_datos->num_rows > 0) {
        $datos = $result_datos->fetch_assoc();
        $humedad = $datos['humedad_amb'];
        $temperatura = $datos['temperatura_amb'];

        // Preparar el cuerpo del correo electrónico
        $mensaje_email = "";
        if ($humedad < $humedad_min) {
            $mensaje_email .= "<p>⚠️ La humedad está por debajo del umbral mínimo. (Sensor ambiente)</p>";
        }
        if ($humedad > $humedad_max) {
            $mensaje_email .= "<p>⚠️ La humedad está por encima del umbral máximo. (Sensor ambiente)</p>";
        }
        if ($temperatura < $temperatura_min) {
            $mensaje_email .= "<p>⚠️ La temperatura está por debajo del umbral mínimo. (Sensor ambiente)</p>";
        }
        if ($temperatura > $temperatura_max) {
            $mensaje_email .= "<p>⚠️ La temperatura está por encima del umbral máximo. (Sensor ambiente)</p>";
        }

        // Enviar correo electrónico si hay alertas
        if (!empty($mensaje_email)) {
            $mail = new PHPMailer(true);

            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'sensorwatch99@gmail.com'; // Cambia a tu correo
                $mail->Password   = 'yrwk zuzt jifl tnhs';       // Cambia a tu contraseña (considera usar variables de entorno)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Configuración SSL opcional (deshabilita la validación del certificado si es necesario)
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                // Remitente y destinatario
                $mail->setFrom('sensorwatch99@gmail.com', 'Sistema Monitoreo'); // Cambia a tu remitent
                $mail->addAddress($correo_usuario);

                // Contenido del hala900@correo
                $mail->isHTML(true);
                $mail->Subject = 'Alerta de Monitoreo - Sensor Suelo';
                $mail->Body    = "
                <html>
                <head><style>body { font-family: Arial, sans-serif; }</style></head>
                <body>
                    <h2>Notificaciones de Monitoreo</h2>
                    $mensaje_email
                    <p>Por favor, revisa el sistema y toma las medidas necesarias.</p>
                </body>
                </html>";

                $mail->send();
            } catch (Exception $e) {
                error_log("No se pudo enviar el correo: {$mail->ErrorInfo}");
            }
        }
    }
}

// Cerrar conexión
$conexion->close();
