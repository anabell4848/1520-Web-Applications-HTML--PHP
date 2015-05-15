<?php
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu
*/?>
<!DOCTYPE html>
<html>
<head>
<title>Send Mail Results</title>
</head>
<body>
<?php
if(!isset($_SESSION)){
    session_start();
}
require '../PHPMailer-master/PHPMailerAutoload.php';

$mail = new PHPMailer;

$username = "hax12";				//CHANGE USERNAME
$password="107036n968au%";			//CHANGE PASSWORD

$subject = strip_tags($_POST["subject"]);
$body = strip_tags($_POST["msg"]);

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.pitt.edu:587';  					// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = "$username";           	  		    // SMTP username
$mail->Password = "$password";                           	// SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted


$mailer=$_SESSION['email'];
$basemail=$_SESSION['adminemail'];
$email=$_SESSION['email'];
$name =$_SESSION['name'];

$mail->From = "$basemail";
$mail->FromName = "$mailer";
$mail->addAddress("$email", "$name");     // Add a recipient		

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = "$subject";  							
$mail->Body    = "$body";		
$mail->AltBody = "$body";	

if(!$mail->send()) {
	echo 'Message could not be sent.';
	echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
	//echo 'Message has been sent </br>';
}
	

?>