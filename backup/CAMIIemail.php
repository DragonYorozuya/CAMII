<?php
require_once 'email/class.smtp.php';
require_once 'email/class.pop3.php';
require_once 'email/class.phpmailer.php';

/**
 * Gerenciar envio de email
 * 
 * @author GustavoLucena
 * @version 2.0 
 */
class CAMIIemail {
	
	/**
	 * Email qeu vai enviar
	 * 
	 * @var CAMIIemail
	 */
	private $email = "etecthejobs@gmail.com";
	
	/**
	 * Senha do Email
	 * 
	 * @var CAMIIemail
	 */
	private $senha = "jujuba157";
	
	/**
	 * Guarda o nome para quem sera enviado o email
	 * 
	 */
	private $destNome;
	
	/**
	 * Guarda o email para qume sera enviado o email
	 * 
	 */
	private $destEmail;
	private $mensagem, $anexo,$assunto;
	public function destinatario($nome, $email,$assunto="") {
		$this->destNome = $nome;
		$this->destEmail = $email;
		$this->assunto = $assunto;
	}
	public function textoDoEmail($texto) {
		$this->mensagem = $texto;
	}
	public function geraAnexo($arq,$nome="",$mimeType='', $cript='base64'){
	    $this->anexo = [$arq, $nome,$cript,$mimeType];
	    //var_dump($this->anexo);
	   // $this->anexo = [dirname(__FILE__) .'/d.sql.gz','d.sql.gz','base64','application/x-gzip'];
	}
	public function enviarEmail() {
		$mail = new PHPMailer ();
		
		// $mail->SMTPDebug = 3; // Habilitar sa�da verborr�gica
		
		$mail->isSMTP (); // Manda usar SMTP
		$mail->Host = 'smtp.gmail.com'; // Especifica os servidores
		$mail->SMTPAuth = true; // Habilita autetica��o para SMTP
		$mail->Username = $this->email; // usu�rio SMTP
		$mail->Password = $this->senha; // senha SMTP
		$mail->SMTPSecure = 'tls'; // Habilita encripta��o TLS, SSL tamb�m � aceito
		$mail->Port = 587; // Porta do servidor de e-mail
		
		$mail->From = $this->email; // Remetente
		$mail->FromName = 'CAMII'; // Nome do remetente
		$mail->addAddress ( $this->destEmail, $this->destNome ); // Destinat�rio com nome
		                                                      // $mail->addAddress('ellen@example.com'); //Destinat�rio sem nome
		                                                      // $mail->addReplyTo('info@example.com', 'Information'); //Responder para...
		                                                      // $mail->addCC('cc@example.com'); //Com c�pia oculta...
		                                                      // $mail->addBCC('bcc@example.com'); //Com c�pia oculta...
		                                                      
		$mail->addAttachment($this->anexo[0],$this->anexo[1],$this->anexo[2],$this->anexo[3]); //Adiciona anexo
		                                                      // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); //Adiciona anexo com outro nome de arquivo
		$mail->isHTML ( true ); // Diz que a mensagem � em html
		
		$mail->Subject = $this->assunto; // Assunto
		$mail->Body = $this->mensagem; // Mensagem em html
		$mail->AltBody = 'This is the body in plain text for non-HTML mail clients'; // Mensagem alternativa para leitores que n�o suportam html
		                                                                             
		// Manda a mensagem
		if ($mail->send ()) {
			// echo 'Mensagem enviada!';
			return true;
		} else {
			// echo 'Não foi possovel enviar';
			// echo 'Erro: ' . $mail->ErrorInfo;
			return false;
		}
	}
}

