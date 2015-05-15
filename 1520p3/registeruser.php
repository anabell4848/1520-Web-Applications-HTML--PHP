<?php	
/*
Hanyu Xiong
CS 1520 Project 3
Dr. Ramirez
hax12@pitt.edu


- redirected from admin.php to allow admin to reset password
- asks admin to enter username and email, if they match from the database 
  then sends the user an email using mailer class with a randomly generated 
  token that's also stored in the admin database


*/
include_once "mailer.php"; 

$username="hax12";		//ENTER USERNAME AND PASSWORD
$password="123";

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
		// Create connection
	$con=mysqli_connect("localhost",$username, $password,"project3db");	
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	//get user input id and email 
	$name=$_POST['id'];
	$email=$_POST['email'];
	$password=$_POST['password'];
	$pass = hash('sha256',$password);
	$type="user";
	//insert token into admin table 
	$res=mysqli_query($con,"INSERT INTO user VALUES ('$name','$email','$pass', '$type')");
	if($res):
		echo "You are now registered as a user</br>";
	else:
		echo"NOPE";
	endif;
	?><form action  = "clear.php" method = "post">
		<a href="http://localhost:8080/1520p3/clear.php">Back to login</a> <br />
	</form><?php
endif;

function check_set(){
	return ((isset($_POST['id']) && $_POST['id']=="")
		|| (isset($_POST['email']) && $_POST['email']=="") 
		|| (isset($_POST['password']) && $_POST['password']=="")
		|| !isset($_POST['id']) || !isset($_POST['email']) || !isset($_POST['password']));
}
function get_info() {
	//id, password
	?>
	<form action  = "registeruser.php" method = "post">
		Username: <input type = "text" name = "id"/><br />
		Email: <input type = "text" name = "email"/><br />
		Password: <input type = "password" name = "password"/><br />
		<input type = "submit"  value = "Register as new user" />
	</form>
<?php 
}
?>
</body> 
</html> 
	