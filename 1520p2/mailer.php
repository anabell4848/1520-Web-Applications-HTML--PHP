<?php
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- class for sending emails out to users and administrators when a new ticket is submitted


- TA needs to change $username, $basemail, and $password


*/
require '../PHPMailer-master/PHPMailerAutoload.php';
class mailer{
	public function __construct()
	{ }
	public function sendmail($email, $name, $subject, $body, $mailer){

		$mail = new PHPMailer;
		//echo "$email, $name, $subject, $body</br>";

		$username = "hax12";				//CHANGE USERNAME
		$basemail="hax12@pitt.edu";			//CHANGE MAILER
		$password="107036n968au%";			//CHANGE PASSWORD

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp.pitt.edu:587';  					// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = "$username";           	  		    // SMTP username
		$mail->Password = "$password";                           	// SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted


		$mail->From = "$basemail";
		$mail->FromName = "$mailer";
		$mail->addAddress("$email", "$name");     // Add a recipient		

		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);                                  // Set email format to HTML

	
		
		//$body='This is <a href="google.com/reset.php?id=salim">reset password</a>  d!</b>';

		$mail->Subject = "$subject";  							
		$mail->Body    = "$body";		
		$mail->AltBody = "$body";	

		if(!$mail->send()) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			//echo 'Message has been sent </br>';
		}
	}
}
?>