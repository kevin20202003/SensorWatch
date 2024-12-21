<?php
// Configuración de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Incluir el archivo de conexión
require '../modelo/conexion.php'; // Asegúrate de incluir tu archivo de conexión a la base de datos

// Función para enviar el código de verificación al correo del usuario
function enviarCodigoVerificacion($correo, $codigo) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sensorwatch99@gmail.com'; 
        $mail->Password   = 'yrwk zuzt jifl tnhs';      
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;        

        // Configuración SSL (deshabilita la validación del certificado si es necesario)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remitente y destinatario
        $mail->setFrom('sensorwatch99@gmail.com', 'Sistema Monitoreo'); 
        $mail->addAddress($correo); 

        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; 
        $mail->Subject = 'Código de verificación';
        $mail->Body    = "Tu código de verificación es: <b>$codigo</b>. Es válido por 5 minutos.";        

        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>
