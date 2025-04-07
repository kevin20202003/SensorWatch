<?php
// Configuración de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

require 'conexion.php';

// Función para enviar el correo de notificación sobre el cambio de contraseña
function enviarNotificacionCambioContraseña($correo) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sensorwatch99@gmail.com'; // Cambia a tu correo
        $mail->Password   = 'yrwk zuzt jifl tnhs';       // Cambia a tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Configuración SSL opcional
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remitente y destinatario
        $mail->setFrom('sensorwatch99@gmail.com', 'SensorWatch');
        $mail->addAddress($correo);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Cambio de contraseña realizado';
        $mail->Body    = "
            <h1>Cambio de contraseña exitoso</h1>
            <p>Hola,</p>
            <p>Te informamos que tu contraseña ha sido cambiada exitosamente.</p>
            <p>Si no realizaste este cambio, por favor, ponte en contacto con el administrador del sistema o con el autor del programa a la brevedad.</p>
            <p>Atentamente,<br>El equipo de SensorWatch</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar la notificación: {$mail->ErrorInfo}";
    }
}
?>
