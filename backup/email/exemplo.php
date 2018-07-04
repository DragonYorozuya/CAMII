<?php
//https://myaccount.google.com/lesssecureapps
require_once 'class.smtp.php';
require_once 'class.pop3.php';
require_once 'class.phpmailer.php';

$mail = new PHPMailer ();

// $mail->SMTPDebug = 3; // Habilitar saída verborrágica

$mail->isSMTP (); // Manda usar SMTP
$mail->Host = 'smtp.gmail.com'; // Especifica os servidores
$mail->SMTPAuth = true; // Habilita auteticação para SMTP
$mail->Username = 'dragond103@gmail.com'; // usuário SMTP
$mail->Password = 'C@m1is4oG'; // senha SMTP
$mail->SMTPSecure = 'tls'; // Habilita encriptação TLS, SSL também é aceito
$mail->Port = 587; // Porta do servidor de e-mail

$mail->From = 'dragond103@gmail.com'; // Remetente
$mail->FromName = 'Dragon'; // Nome do remetente
$mail->addAddress ( 'dragond103@gmail.com', 'Teste' ); // Destinatário com nome
                                                    // $mail->addAddress('ellen@example.com'); //Destinatário sem nome
                                                    // $mail->addReplyTo('info@example.com', 'Information'); //Responder para...
                                                    // $mail->addCC('cc@example.com'); //Com cópia oculta...
                                                    // $mail->addBCC('bcc@example.com'); //Com cópia oculta...
                                                    
// $mail->addAttachment('/var/tmp/file.tar.gz'); //Adiciona anexo
                                                    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); //Adiciona anexo com outro nome de arquivo
$mail->isHTML ( true ); // Diz que a mensagem é em html

$mail->Subject = 'Here is the subject'; // Assunto
$mail->Body = 'This is the HTML message body <b>in bold!</b>'; // Mensagem em html
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients'; // Mensagem alternativa para leitores que não suportam html
                                                                             
// Manda a mensagem
if (! $mail->send ()) {
	echo 'Não foi possível enviar';
	echo 'Erro: ' . $mail->ErrorInfo;
} else {
	echo 'Mensagem enviada!';
}
?>
