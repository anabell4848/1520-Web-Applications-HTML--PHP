<?php	
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- TA change database connection username and password

*/
include_once "mailer.php"; 
if(!isset($_SESSION)){
    session_start();
}

?>
<!DOCTYPE html> 
<html> 
<head> 
<title>Administrator Reset </title> 
</head> 
<body> 
<?php 

//var_dump($_SESSION);

//get the token
if (isset($_GET["token"])):
	$token = $_GET["token"];
	$_SESSION["token"]= $token;
	if (isset($_SESSION["token"])):
		$token = $_SESSION["token"];
	endif;
	if (check_set()):			//fields not filled out
		get_info();
	else:		//fields filled out
		if (check_match()):		//both passwords are the same
			// Create connection
			$con=mysqli_connect("localhost","hax12","123","project2db");	//USERNAME, PASSWORD
			if (mysqli_connect_errno()) {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			//get user input password
			$password=$_POST['password1'];
			//update this admin's password
			$pass = hash('sha256',$password);
			$passquery="SELECT * FROM admin WHERE token='$token'";
			$result = mysqli_query($con, $passquery);
			$matchadmin=0;
			while($row = mysqli_fetch_array($result)) {
				if ($row['token']=$token)
					$matchadmin++;
			}
			//echo "MATCHES=$matchadmin";
			if ($matchadmin!=0):
				$passquery="UPDATE admin SET password='$pass' WHERE token='$token'";
				//echo "PASS=$pass, TOKEN=$token";
				$result = mysqli_query($con, $passquery);
				//make token NULL in admin table, so this link can't be used again 
				$passquery="UPDATE admin SET token=NULL WHERE password='$pass'";
				$result = mysqli_query($con, $passquery);
				echo "Congratulations, you have changed your password";
				//link to go to login page
				?><form action  = "clear.php" method = "post">
					<a href="http://localhost:8080/1520p2/clear.php">Done, back to login</a> <br />
				</form><?php
			else:
				echo "Your password reset link has expired, cannot reset password";
			endif;
		else:
			echo "Your passwords did not match, please enter them again";
			get_info();
		endif;
	endif;
else:	
	echo "Access Denied";
endif;

function check_match(){
	return (($_POST['password1']==$_POST['password2']));
}	
function check_set(){
	return ((isset($_POST['password1']) && $_POST['password1']=="")
		|| (isset($_POST['password2']) && $_POST['password2']=="")
		|| !isset($_POST['password1']) || !isset($_POST['password2']));
}
function get_info() {
	//id, password
	$token = $_GET["token"];
	?>
	<form action  = "http://localhost:8080/1520p2/resetpassword.php?token=<?php echo "$token"; ?>" method = "post">
		New Password: <input type = "text" name = "password1"/><br />
		Re-enter New Password: <input type = "text" name = "password2"/><br />
		<input type = "submit"  value = "Submit" />
	</form>
<?php 
}
?>
</body> 
</html> 
	