<?php
// Configuración de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$host = 'dpg-ctjgc29opnds73fpf86g-a'; // External o Internal URL
$port = '5432'; // Puerto de PostgreSQL
$dbname = 'invernadero_b4gl';
$user = 'invernadero_b4gl_user';
$password = 'y39YXOlTTfBs5Fs28iZrHV8Dj4DJwLYY';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a PostgreSQL.";
} catch (PDOException $e) {
    echo "Error al conectar a PostgreSQL: " . $e->getMessage();
}

// Función para enviar el código de verificación al correo del usuario
function enviarCodigoVerificacion($correo, $codigo) {
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
        $mail->setFrom('sensorwatch99@gmail.com', 'Sistema Monitoreo'); // Cambia a tu remitente
        $mail->addAddress($correo); // Destinatario

        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; // Añade esta línea para establecer la codificación
        $mail->Subject = 'Código de verificación';
        $mail->Body    = "Tu código de verificación es: <b>$codigo</b>. Es válido por 5 minutos.";        

        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>
