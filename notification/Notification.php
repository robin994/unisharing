<?

require_once 'PHPMailerAutoload.php';
require_once('class.phpmailer.php');

class Notification{
	
	private $send;
	
	function __construct(){
		
		//istanziamo la classe
		$this->send = new PHPmailer();
		$this->send->isSMTP(); 
		$this->send->IsHTML(true);      
		$this->send->CharSet = 'UTF-8';                               // Set mailer to use SMTP
		$this->send->Host = 'mail.quidfacis.it';              // Specify main and backup SMTP servers
		$this->send->SMTPAuth = true;                               // Enable SMTP authentication
		$this->send->Username = 'info@quidfacis.it';                 // SMTP username
		$this->send->Password = 'neonato2000';                           // SMTP password
		//$this->send->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$this->send->Port = 587;  
	}
	
	function send($from, $to, $object, $message){
		
		//definiamo le intestazioni e il corpo del messaggio
		$this->send->From = $from;
		$this->send->AddAddress($to);
		$this->send->Subject=$object;
		$this->send->Body=$message;	
		
		$result;
		
		//definiamo i comportamenti in caso di invio corretto 
		//o di errore
		if(!$this->send->Send()){ 
		  $result = $this->send->ErrorInfo; 
		}else{ 
		  $result = true;
		}
		
		//chiudiamo la connessione
		$this->send->SmtpClose();
		return $result;
	}
	
}





?>