<?php
	if (isset($_POST['username'])): 	//if username blank is filled out
		$username = $_POST['username'];	
		$password = $_POST['password'];
		
		$userpass=0;
		
		$userlines=file("users.txt");	//verify username&password
		foreach ($userlines as $currdata):
			$userchunks= explode("#",$currdata);
			$usern = $userchunks[0];
			$passw = $userchunks[1];
			if ($usern==$username):
				$userpass=1;
			endif;
		endforeach;
	endif;
if (isset($userpass) && $userpass==1):			//username already being used
	show_header();
	get_loginfo();
	bad_loginfo();
	show_end();
elseif (isset($userpass) && $userpass==0):
	$newline = "\n$username#$password";
	$fileptr = fopen("users.txt", "a");
	fwrite($fileptr, "$newline");
	fclose($fileptr);
	show_header();
	got_loginfo();
	show_end();	
else:
	show_header();
	get_loginfo();
	show_end();
endif;
	
function show_header()
{
?>
<!DOCTYPE html>

<html>
<head>
<title>Quiz Of The Day</title>
</head>
<?php
}
function show_end()
{
    echo "</html>";
}
function get_loginfo()
{
    echo "Welcome to Quiz Of The Day, please enter a username and password <br />"; 
?>
	<form name = "loginfoform"
         action = "newuser.php"
         method = "POST">
	
    Username: <input type = "text" name = "username"><br />
    Password: <input type = "text" name = "password"><br />
    <input type = "submit" value = "Submit">
	</form>

<?php
}
function bad_loginfo()
{
    echo "This username has already been taken, please choose another <br />"; 
}	

function got_loginfo()
{
    echo "Your username and password have been set, you may login now <br />"; 
?>
	<a href="http://localhost:8080/1520p1/index.php">Take today's quiz</a> <br />
<?php
}	
?>

