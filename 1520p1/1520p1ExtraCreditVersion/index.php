<?php	
/*
Hanyu Xiong
CS 1520 Project 1
Dr. Ramirez
hax12@pitt.edu

Quiz Of The Day
extra credit version
	*Make it so that a quiz will not be repeated once it has been given, even on a different day.  
	The idea here is rather than picking a completely "random" quiz to give, you are now using 
	"random" selection with elimination.  Clearly, with this technique, at some point you will run 
	out of quizzes.
	*Allow a "New User" to register on the site to take the quizzes.  This will require a check to 
	make sure the new user does not choose the same ID as a user already registered
*/

session_start();

date_default_timezone_set('America/New_York');
$userpass=0;

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
		if ($passw==$password):
			$userpass=1;
		endif;
	endforeach;
else:  						//no username blank filled, go to login page.  
	//echo "error<br />";		//But if last user stayed logged in then use their info
	if (isset($_COOKIE['lastuser'])): 
		$lastuser=$_COOKIE["lastuser"];
		$_SESSION['username']=$lastuser;
		$username=$lastuser;
		$userpass=1;
	endif;
	
endif;

if ($userpass):			//verified username password
	if (isset($_COOKIE[$username])):   //user already logged in before
		$_SESSION['username']=$username ;
		$currcookie = $_COOKIE[$username];
		$temptime=strtotime($currcookie);
		$diff=time()-$temptime;
		if ($diff<(3600*24)):
			//already taken quiz message
			setcookie("lastuser", "", time()-3600 ); //delete lastuser cookie
			show_header();
			took_quiz();
			show_end();
		else:
			//user logged in but didn't take the quiz
			if (isset($_POST["keeploggedin"])):   //check if keeploggedin was checked
				//echo "stayed logged in <br />";  
				setcookie("lastuser", "$username");
			else:
				setcookie("lastuser", "", time()-3600 ); //delete lastuser cookie
			endif; 
			//load main page: 
			show_header();
			show_home();
			show_end();
		endif;
	else:
		if (isset($_POST["keeploggedin"])):   //check if keeploggedin was checked
			//echo "stayed logged in <br />";  
			setcookie("lastuser", "$username");
		else:
			setcookie("lastuser", "", time()-3600 ); //delete lastuser cookie
		endif; 
		
		$_SESSION['username']=$username ;
		setcookie("$username", date('m/d/Y', time()-3600*24)); 
		//new user, take the quiz
		show_header();
		show_home();
		show_end();
	endif;
else:				//unverified, back to login page
	show_header();
	get_loginfo();
	show_end();
endif;	
	
	
//FUNCTIONS
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
function show_home()
{
   $username = $_SESSION['username'];
?>
   <form name = "homeform"
         action = "index.php"
         method = "POST">
   Quiz Home Page <br />
 <?php 
	echo  "Welcome $username <br />";
?>
	<a href="http://localhost:8080/1520p1/quizprocess.php">Start the Quiz</a> <br />
	<a href="http://localhost:8080/1520p1/clear.php">Change Users</a> <br />
   </form>
<?php
}
function get_loginfo()
{
    echo "Quiz of the Day <br />";
    echo "Login <br />";
?>
    <form name = "loginfoform"
         action = "index.php"
         method = "POST">
    Username: <input type = "text" name = "username"><br />
    Password: <input type = "text" name = "password"><br />
	<input type = "checkbox" name = "keeploggedin" value = "1" > Keep Me Logged In <br />
    <input type = "submit" value = "Login"> <br />
	<a href="http://localhost:8080/1520p1/newuser.php">Register as new user</a> <br />
    </form>
<?php
}
function took_quiz()
{
	$username = $_SESSION['username'];
	echo " $username, you have taken today's quiz already, try again tomorrow <br />";
	?>
	<a href="http://localhost:8080/1520p1/clear.php">Not <?php echo "$username"?>? Log out</a> <br />
	<?php

}
?>
	
	
	
	
	
	