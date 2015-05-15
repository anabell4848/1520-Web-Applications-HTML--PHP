<?php	
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- gets ticket information from a submission form, shows the form again
  if any fields are left blank
- inserts ticket info into database table and recalculate how many similar 
  words are in each of the ticket's descriptions, add to similar table
- send an email comfirmation to submitter and to all the administrators
  using mailer class


- TA needs to change username and password for the database, and need 
  to go into mailer.php to change usermail, basemail, and password for email
- next go to admin.php to log in as admin and see the tickets

*/
include_once "mailer.php";  

date_default_timezone_set('America/New_York');
?>

<!DOCTYPE html> 
<html> 
<head> 
<script src="http://code.jquery.com/jquery-latest.js"></script>
<title>Submit your ticket</title> 
</head> 
<body> 
<?php 
	// Create connection
	$con=mysqli_connect("localhost","hax12","123","project2db");		//TA CHANGE USERNAME AND PASSWORD
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

if(check_set()):  		  //user submits an invalid request 
	get_info();					
else:
	show_confirmation();
	
	//Add ticket to database
	$fname=$_POST['fname'];
	$lname=$_POST['lname'];
	$name=$fname." ".$lname;
	$email=$_POST['email'];
	$subject=$_POST['subject'];
	$description=$_POST['description'];
	$time = date('Y-m-d H:i:s', time());
	
	//get the ticket number from number of entries in table
	$result=mysqli_query($con,"SELECT * FROM tickets");
	$ticketnum=0;
	while($row = mysqli_fetch_array($result)){
		$temp=$row['ticketnum'];
		if ($temp>=$ticketnum) $ticketnum=$temp+1;
		//echo "Ticket number=$$ticketnum";
	}
	
	//insert into ticket table
	$res=mysqli_query($con,"INSERT INTO tickets 
	VALUES ('$ticketnum','$time','$name','$email','$subject','$description','','open', NULL)");
	if($res):
		//echo "insert successful ticket</br>";
	else:
		//echo"NOPE</br>";
	endif;
	
	//insert into similar table
	$sql="TRUNCATE TABLE similar";	//first empty the table
	if (mysqli_query($con,$sql)) {
	  //echo "table similar dropped successfully</br>";
	} else {
	  //echo "Error dropping database: " . mysqli_error($con);
	}
	
	$result=mysqli_query($con,"SELECT * FROM tickets");
	while($row = mysqli_fetch_array($result)){
		$ticketid1=$row['ID'];
		$description1=$row['description'];
		
		$result2=mysqli_query($con,"SELECT * FROM tickets");
		while($row = mysqli_fetch_array($result2)){//go thru all the tickets in the table compare with new entry
			$ticketid2=$row['ID'];
			$description2=$row['description'];
			$matches=matches($description1, $description2);
			//echo "NUM OF MATCHES for $ticketid1 and $ticketid2 IS $matches";
			$res=mysqli_query($con,"INSERT INTO similar VALUES ('$ticketid1','$ticketid2','$matches')");
			if($res):
				//echo "insert successful ticket</br>";
			else:
				//echo"NOPE</br>";
			endif;
		}
	}
	
	//Send an email confirmation to user
	$body ="Dear ".$name.", your ticket has been confirmed";
	$mailer='Ticket Admin';
	$mailit=new mailer();
	$mailit->sendmail($email, $name, $subject, $body, $mailer);
	
	//Send email to all tech administrators
	$body ='Dear Administrators, New ticket alert';
	$mailer='Ticket Center';
	$query="SELECT * FROM admin";
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)) {//loop through all the administrators
		//echo $row['email'];
		//echo "<br>";
		$email=$row['email'];
		$mailit->sendmail($email, $name, $subject, $body, $mailer);
	}
	
endif;

//FUNCTIONS

function matches($description1, $description2){
	$arr1 = explode(" ",$description1 );
	$arr2 = explode(" ",$description2 );
	$result = array_intersect($arr1 , $arr2 ); //matched elements
	$num = count($result); //number of matches
	//echo "NUM OF MATCHES IS $num";
	return $num;
}

function check_set(){
	return ((isset($_POST['fname']) && $_POST['fname']=="")
		|| (isset($_POST['lname']) && $_POST['lname']=="")
		|| (isset($_POST['email']) && $_POST['email']=="")
		|| (isset($_POST['subject']) && $_POST['subject']=="")
		|| (isset($_POST['description']) && $_POST['description']=="")
		|| !isset($_POST['fname']) || !isset($_POST['lname']) 
		|| !isset($_POST['email']) || !isset($_POST['subject'])
		|| !isset($_POST['description']));
	/*?><script>
	function validateForm() {
		var fname = document.forms["submitform"]["fname"].value;
		var lname = document.forms["submitform"]["lname"].value;
		var email = document.forms["submitform"]["email"].value;
		var subject = document.forms["submitform"]["subject"].value;
		var description = document.forms["submitform"]["description"].value;
		if (fname==null || fname=="" || lname==null || lname==""
			|| email==null || email=="" || subject==null || subject=="" || description==null || description=="") {
			alert("Form not filled out");
			return false;
		}
	}
	</script><?php */
}	

function get_info() {
	//first name, last name, email address, subject of the problem, 1 line
	//brief description of the problems, multiple lines
	?>
	<p>Enter Your Ticket Information</p> 
	<form name="submitform" action="submit.php" method="POST">	
	<form action = "submit.php" method = "POST"/> 
	First name: <input type = "text" name = "fname" size = "30"/> <br />
	Last name: <input type = "text" name = "lname" size = "30"/> <br />
	Email: <input type = "text" name = "email" size = "30"/> <br />
	Subject of Problem: <input type = "text" name = "subject" size = "60"/> <br />
	Description of Problem: <br />
    <textarea name="description" rows="5" cols="60"></textarea>
    <br /><br />
    <input type = "submit" value = "Submit"> 
	</form>
<?php 
}
function show_confirmation(){
	echo "Your problem has been reported, we will try to solve the problem as soon possible.<br />"; 
}
?>
</body> 
</html> 

















