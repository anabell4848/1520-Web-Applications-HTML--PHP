<?php	
/*
Hanyu Xiong
CS 1520 Project 4
Dr. Ramirez
hax12@pitt.edu


*/
if(!isset($_SESSION)){
    session_start();
}

//var_dump($_SESSION);

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

if (isset($_SESSION['id'])):
		header('Location: playGame.php');
		exit();
elseif (!not_set()):			//fields filled out
	if (check_info($username,$password)):	//login correct
		header('Location:playGame.php');
		exit();
	else:			//user login wrong
		echo "Wrong login info";
		get_info();
	endif;
else:
	get_info();
endif;

function check_info($username,$password){
	
	// Create connection
	$con=mysqli_connect("localhost",$username, $password,"project4db");		//USERNAME AND PASSWORD
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$check=0;
	$id=$_POST['id'];
	$password = hash('sha256',$_POST['password']);
	//check if user is an user
	$query="SELECT * FROM user WHERE id='$id'";
	$result = mysqli_query($con,$query);
	$num_rows=$result->num_rows;
	
	if ($num_rows>0){	//id in database
		$query="SELECT * FROM user WHERE id='$id'"; //check password
		$result = mysqli_query($con,$query) or die(mysql_error());
		while($row = mysqli_fetch_array($result)){
			if ($password==$row['password']){
				$check=1;
				$_SESSION['id']=$id;
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
	<form name = "Login" 
		  action="login.php" 
		  method="POST"
		  onSubmit = "return checkfields(0)">
	<h3>Enter your user login info below:</h3>
	<b>Admin ID: <input type = "text" name = "id" size = "30"></b>
	<br />
	<b>Password: <input type = "password" name = "password" size = "30"></b>
	<br />
	<input type = "submit" name = "submit" value = "Login">
	</form>
	<br />
<?php 
}
?>
</body> 
</html> 
	