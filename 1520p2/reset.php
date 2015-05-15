<?php	
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu


- redirected from admin.php to allow admin to reset password
- asks admin to enter username and email, if they match from the database 
  then sends the user an email using mailer class with a randomly generated 
  token that's also stored in the admin database

- TA change database connection username and password 2x here

*/
include_once "mailer.php"; 

?>
<!DOCTYPE html> 
<html> 
<head> 
<title>Administrator Reset </title> 
</head> 
<body> 
<?php 

if (check_set()):			//fields not filled out
	get_info();
else:		//fields filled out
	if (check_info()):		//verified user info
		// Create connection
		$con=mysqli_connect("localhost","hax12","123","project2db");	//USERNAME AND PASSWORD
		if (mysqli_connect_errno()) {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		//get user input id and email 
		$email=$_POST['email'];
		$name=$_POST['id'];
		$subject='Administrator password reset';
		$token = bin2hex(mcrypt_create_iv(10, MCRYPT_DEV_RANDOM));
		//insert token into admin table 
		$passquery="UPDATE admin SET token='$token' WHERE id='$name'";
		$result = mysqli_query($con, $passquery);
		//echo "$token";
		//create the unique URL
		$url = "http://localhost:8080/1520p2/resetpassword.php?token=$token";
		$body="Here's the link to reset your password:"."$url";
		//send the email using email class
		$mailer='Reset Password Center';
		$mailreset=new mailer();
		$mailreset->sendmail($email, $name, $subject, $body, $mailer);
		//link to go to login page
		?><form action  = "clear.php" method = "post">
			<a href="http://localhost:8080/1520p2/clear.php">Done, back to login</a> <br />
		</form><?php
	else:
		echo "Your information was not correct, please enter your ID and Email";
		get_info();
	endif;
endif;


function check_info(){
	// Create connection
	$con=mysqli_connect("localhost","hax12","123","project2db");	//USERNAME AND PASSWORD
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$check=0;
	$id=$_POST['id'];
	$email=$_POST['email'];
	$query="SELECT * FROM admin WHERE id='$id'";
	$result = mysqli_query($con,$query);
	$num_rows=$result->num_rows;
	if ($num_rows>0){	//id in database
		$query="SELECT admin.email FROM admin WHERE id='$id'"; //check email
		$result = mysqli_query($con,$query) or die(mysql_error());
		while($row = mysqli_fetch_array($result)){
			if ($email==$row['email']){
				$check=1;
			}
		}
	}
	mysqli_close($con);
	return $check;
}	
function check_set(){
	return ((isset($_POST['id']) && $_POST['id']=="")
		|| (isset($_POST['email']) && $_POST['email']=="")
		|| !isset($_POST['id']) || !isset($_POST['email']));
}
function get_info() {
	//id, password
	?>
	<form action  = "reset.php" method = "post">
		Admin Id: <input type = "text" name = "id"/><br />
		Email: <input type = "text" name = "email"/><br />
		<input type = "submit"  value = "Send Reset Email" />
	</form>
<?php 
}
?>
</body> 
</html> 
	