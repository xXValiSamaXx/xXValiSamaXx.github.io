<?php
// Incluir los archivos necesarios de PHPMailer y la conexión
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
require 'Conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreosSiActividadProxima() {
    global $conn; // Usa la conexión de Conexion.php

    // Consulta para verificar si hay actividades académicas que vencen mañana
    $sqlActividades = "SELECT COUNT(*) AS actividadesProximas FROM ActividadesAcademicas WHERE fecha = DATEADD(day, 1, CAST(GETDATE() AS Date))";
    $stmtActividades = sqlsrv_query($conn, $sqlActividades);
    if($stmtActividades === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $rowActividades = sqlsrv_fetch_array($stmtActividades, SQLSRV_FETCH_ASSOC);

    // Si hay actividades próximas a vencer, enviar correos
    if($rowActividades['actividadesProximas'] > 0) {
        // Consulta para obtener los correos electrónicos
        $sqlEmails = "SELECT email FROM InformacionPersonal";
        $stmtEmails = sqlsrv_query($conn, $sqlEmails);
        if($stmtEmails === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        while($rowEmail = sqlsrv_fetch_array($stmtEmails, SQLSRV_FETCH_ASSOC)) {
            $destinatario = $rowEmail['email']; // Email del destinatario
            try {
                
                //Instanciando el objeto "mail"
                $mail = new PHPMailer(true);
                $mail->CharSet = 'UTF-8';

                // Configura el servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'angelrayaviles20@gmail.com'; // Correo electrónico desde el que enviarás los correos
                $mail->Password = 'bjkk dupq lpnq ncxe'; // Contraseña de tu correo electrónico
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;


                $asunto = 'Actividad académica próxima a vencer';
                $mensaje = 'Tienes una actividad académica que está próxima a vencer. Por favor revisa tu agenda.';

                // Configura y envía el correo
                $mail->setFrom('angelrayaviles20@gmail.com', 'Administrador');
                $mail->addAddress($destinatario);
                $mail->Subject = $asunto;
                $mail->Body = $mensaje;
                $mail->send();

                echo "Correo enviado correctamente a {$destinatario}<br>";
            } catch (Exception $e) {
                echo "Error al enviar el correo a {$destinatario}: " . $mail->ErrorInfo . "<br>";
            }
        }
    } else {
        echo "No hay actividades próximas a vencer.";
    }
}

// Uso de la función
enviarCorreosSiActividadProxima();
?>