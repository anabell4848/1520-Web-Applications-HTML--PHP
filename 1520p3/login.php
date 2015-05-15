<?php	
/*
Hanyu Xiong
CS 1520 Project 3
Dr. Ramirez
hax12@pitt.edu


*/
if(!isset($_SESSION)){
    session_start();
}
$username="hax12";		//ENTER USERNAME AND PASSWORD
$password="123";
?>
<!DOCTYPE html> 
<html> 
<head> 
<title>Login</title> 
<script type = "text/javascript">	
	function checkfields(formnum)
	{
	    theform = document.forms[formnum];
        var id = theform.id.value;
        var password = theform.password.value;
	    if (id == "" || password == "")
	    {
			alert(theform.name + " is not completely filled out");
			return false;
	    }
	    else {
			return true;
	    }
	}
	</script>
</head> 
<body> 
<?php 

//var_dump($_SESSION);

if (isset($_SESSION['usertype'])):
	if ($_SESSION['usertype']=="admin"): 	//admin login
		header('Location: admin.php');
		exit();
	else:		//regular user login
		header('Location: userpage.php');
		exit();
	endif;
elseif (!not_set()):			//fields filled out
	if (check_info($username,$password)):	//login correct
		if ($_SESSION['usertype']=="admin"): 	//admin login
			header('Location: admin.php');
			exit();
		else:		//regular user login
			header('Location: userpage.php');
			exit();
		endif;
	else:			//admin login wrong
		echo "Wrong login info";
		get_info();
	endif;
else:
	get_info();
endif;

function check_info($username,$password){
	
	// Create connection
	$con=mysqli_connect("localhost",$username, $password,"project3db");		//USERNAME AND PASSWORD
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$check=0;
	$id=$_POST['id'];
	$password = hash('sha256',$_POST['password']);
	//check if user is an admin
	$query="SELECT * FROM admin WHERE id='$id'";
	$result = mysqli_query($con,$query);
	$num_rows=$result->num_rows;
	
	if ($num_rows>0){	//id in database
		$query="SELECT admin.password FROM admin WHERE id='$id'"; //check password
		$result = mysqli_query($con,$query) or die(mysql_error());
		while($row = mysqli_fetch_array($result)){
			if ($password==$row['password']){
				$check=1;
				$usertype = "admin";
				$_SESSION['usertype']=$usertype;
				$_SESSION['id']=$id;
				break;
			}
		}
	}
	
	//check if user is a regular user
	$query="SELECT * FROM user WHERE id='$id'";
	$result = mysqli_query($con,$query);
	$num_rows=$result->num_rows;
	if ($num_rows>0){	//id in database
		$query="SELECT user.password FROM user WHERE id='$id'"; //check password
		$result = mysqli_query($con,$query) or die(mysql_error());
		while($row = mysqli_fetch_array($result)){
			if ($password==$row['password']){
				$check=1;
				$usertype = "user";
				$_SESSION['usertype']=$usertype;
				$_SESSION['id']=$id;
				$query2="SELECT user.email FROM user"; //get ID number
				$result2 = mysqli_query($con,$query2) or die(mysql_error());
				while($row2 = mysqli_fetch_array($result2)){
					$_SESSION['email']=$row2['email'];
				}
				break;
			}
		}
		
	}
	mysqli_close($con);
	return $check;
}	

function not_set(){
	return (!isset($_POST['id']) || !isset($_POST['password']));
}
function get_info() {
	//id, password
	?>
	<form name = "Admin login" 
		  action="login.php" 
		  method="POST"
		  onSubmit = "return checkfields(0)">
	<h3>Enter your admin login info below:</h3>
	<b>Admin ID: <input type = "text" name = "id" size = "30"></b>
	<br />
	<b>Password: <input type = "password" name = "password" size = "30"></b>
	<br />
	<input type = "submit" name = "submit" value = "Login">
	</form>
	<br />
	<form name = "User login" 
		  action="login.php" 
		  method="POST"
		  onSubmit = "return checkfields(1)">
	<h3>Enter your user login info below:</h3>
	<b>Username: <input type = "text" name = "id" size = "30"></b>
	<br />
	<b>Password: <input type = "password" name = "password" size = "30"></b>
	<br />
	<input type = "submit" name = "submit" value = "Login">
	<a href="http://localhost:8080/1520p3/registeruser.php">Register as new user</a> <br />
	</form>
	<br />
<?php 
}
?>
</body> 
</html> 
	