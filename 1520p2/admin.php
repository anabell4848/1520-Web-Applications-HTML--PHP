<?php	
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- asks for admin to log in, checks with database info to see if it's correct
- after admin logs in, shows a table with all the open tickets, user can view all
  tickets, view my tickets, view unassigned tickets, view selected ticket, sort, 
  logout
- button and radio button selected decided what query is set as and direct to 
  ticket or showticket class to display the table; uses a ticket class to show 
  the tickets on this main page; uses a showticket class to show a specific 
  selected ticket
- allows admin to reset password


- TA needs to change username and passsword for database connection in this code 
  in check_info() function below
  


*/
include_once "ticket.php"; 
include_once "showticket.php"; 
if(!isset($_SESSION)){
    session_start();
}

?>
<!DOCTYPE html> 
<html> 
<head> 
<script src="http://code.jquery.com/jquery-latest.js"></script>
<title>Administrator</title> 
</head> 
<body> 
<?php 

//var_dump($_SESSION);

if (isset($_SESSION['id'])):
	$returnval=check_click();
	if ($returnval[4]!="CHOICE3"):
		$ticket=new ticket();
		$ticket->seetickets($returnval[0], $returnval[1], $returnval[2], $returnval[3]);
	else:
		$ticket=new showticket();
		$ticket->seeticket($returnval[0],$returnval[1]);
	endif;
elseif (!not_set()):			//fields filled out
	if (check_info()):	//admin login right
		$passquery="SELECT * FROM tickets WHERE status='open'";
		$b1="View All Tickets";
		$_SESSION['b1']=$b1;
		$b4="View My Tickets";
		$_SESSION['b4']=$b4;
		$b6="View Unassigned Tickets";
		$_SESSION['b6']=$b6;
		$ticket=new ticket();
		$ticket->seetickets($passquery, $b1, $b4,$b6);
	else:			//admin login wrong
		get_info();
	endif;
else:		//fields not filled out
	get_info();
endif;



function check_click(){
	//set passing to ticket.php variables using last stored session variables
	$b1=$_SESSION['b1'];
	$b4=$_SESSION['b4'];
	$b6=$_SESSION['b6'];
	$choice3="";
	if (isset($_POST["choice1"])): //view all tickets/view open tickets
		//toggle ticket and table output
		if ($b1=="View Open Tickets"):
			$passquery="SELECT * FROM tickets WHERE status='open'";
			$_SESSION['query']=$passquery;
			$b1="View All Tickets";
		else:
			$passquery="SELECT * FROM tickets";
			$_SESSION['query']=$passquery;
			$b1="View Open Tickets";
		endif;
		
		//set session variables 
		$_SESSION['b1']=$b1;
		
	elseif (isset($_POST["choice2"])): //sort
		//figure out query based on what sort
		if (isset($_POST['choice'])):  //something was selected
			$sessionquery=$_SESSION['query'];
			if($_POST['choice']=="ticketnum"):
				$passquery="$sessionquery"." ORDER BY ticketnum";
			elseif($_POST['choice']=="received"):
				$passquery="$sessionquery"." ORDER BY time";
			elseif($_POST['choice']=="sendername"):
				$passquery="$sessionquery"." ORDER BY name";
			elseif($_POST['choice']=="senderemail"):
				$passquery="$sessionquery"." ORDER BY email";
			elseif($_POST['choice']=="subject"):
				$passquery="$sessionquery"." ORDER BY subject";
			elseif($_POST['choice']=="description"):
				$passquery="$sessionquery"." ORDER BY description";
			else:
				$passquery="$sessionquery";
			endif;
		else:		//nothing selected don't sort
			$passquery="SELECT * FROM tickets WHERE status='open'";
		endif;
		
	elseif (isset($_POST["choice3"])):  //view selected ticket
		//display ticket
		if (isset($_POST['choice'])):  //something was selected
			$choice3="CHOICE3";		//use showticket
			$ticketchosen=$_POST['choice'];
			$passquery="SELECT * FROM tickets WHERE ID=$ticketchosen";
			$b1=$_POST['choice'];	//$b1=ticket ID
		else:		//nothing selected don't do anything
			$passquery="SELECT * FROM tickets WHERE status='open'";
		endif;
		
		
	elseif (isset($_POST["choice4"])):  //view my tickets/view open tickets
		//toggle ticket and table output
		if ($b4=="View Open Tickets"):
			$passquery="SELECT * FROM tickets WHERE status='open'";
			$_SESSION['query']=$passquery;
			$b4="View My Tickets";
		else:
			$tech=$_SESSION['id'];
			$passquery="SELECT * FROM tickets WHERE tech='$tech'";
			$_SESSION['query']=$passquery;
			$b4="View Open Tickets";
		endif;
		
		//set session variables 
		$_SESSION['b4']=$b4;
		
	elseif (isset($_POST["choice5"])):  //logout
		//toggle ticket and table output
		if ($b4=="View Open Tickets"):
			$passquery="SELECT * FROM tickets WHERE status='open'";
			$_SESSION['query']=$passquery;
			$b4="View My Tickets";
		else:
			$tech=$_SESSION['id'];
			$passquery="SELECT * FROM tickets WHERE tech='$tech'";
			$_SESSION['query']=$passquery;
			$b4="View Open Tickets";
		endif;
		
		//set session variables 
		$_SESSION['b4']=$b4;
		
	elseif (isset($_POST["choice6"])):  //view unassigned tickets/view open tickets
		//toggle ticket and table output
		if ($b6=="View Open Tickets"):
			$passquery="SELECT * FROM tickets WHERE status='open'";
			$_SESSION['query']=$passquery;
			$b6="View Unassigned Tickets";
		else:
			$passquery="SELECT * FROM tickets WHERE tech=''";
			$_SESSION['query']=$passquery;
			$b6="View Open Tickets";
		endif;
		
		//set session variables 
		$_SESSION['b6']=$b6;
		
	else:   //just logged in, or left showticket page 
		//echo "DIDN'T SELECT ANYTHING";
		if (isset($_POST["closeopen"])):
			$choice3="CHOICE3";		//use showticket
			$ticketid=$_SESSION['ticketid'];
			$openclosed=$_SESSION['openclosed'];
			if ($openclosed=="closed"):
				$passquery="UPDATE tickets SET status='open' WHERE ID='$ticketid'";
				$open="open";
				$_SESSION['openclosed']=$open;
			else:
				$passquery="UPDATE tickets SET status='closed' WHERE ID='$ticketid'";
				$closed="closed";
				$_SESSION['openclosed']=$closed;
			endif;
		elseif(isset($_POST["assignself"])):
			$choice3="CHOICE3";		//use showticket
			$ticketid=$_SESSION['ticketid'];
			$tech=$_SESSION['id'];
			$passquery="UPDATE tickets SET tech='$tech' WHERE ID='$ticketid'";
		elseif(isset($_POST["removeself"])):
			$choice3="CHOICE3";		//use showticket
			$ticketid=$_SESSION['ticketid'];
			$passquery="UPDATE tickets SET tech='' WHERE ID='$ticketid'";
		elseif(isset($_POST["deleteticket"])):
			$choice3="CHOICE3";		//use showticket
			$ticketid=$_SESSION['ticketid'];
			$passquery="DELETE FROM tickets WHERE ID='$ticketid'";
		elseif(isset($_POST["findall"])):	//use table not showtable
			$name=$_SESSION['name'];
			$passquery="SELECT * FROM tickets WHERE name='$name'";
		elseif(isset($_POST["findsimilar"])): //use table not showtable
			if(isset($_POST["numofmatches"])):
				$numofmatches=$_POST["numofmatches"];
				$ticketid=$_SESSION['ticketid'];
				$passquery="SELECT * FROM similar WHERE similar.ticketid1='$ticketid' and similar.nummatches='$numofmatches' ";
				
			else:
				$passquery="SELECT * FROM tickets WHERE status='open'";
			endif;
		elseif(isset($_POST["adminpage"])):
			$passquery="SELECT * FROM tickets WHERE status='open'";
		else:		
			$passquery="SELECT * FROM tickets WHERE status='open'";
			//echo "NOT SURE WHY HERE";
		endif;
	endif;
	
	$returnval=array($passquery, $b1, $b4, $b6, $choice3);
	return $returnval;
}

function check_info(){
	// Create connection
	$con=mysqli_connect("localhost","hax12","123","project2db");		//USERNAME AND PASSWORD
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$check=0;
	$id=$_POST['id'];
	$password = hash('sha256',$_POST['password']);
	$query="SELECT * FROM admin WHERE id='$id'";
	$result = mysqli_query($con,$query);
	$num_rows=$result->num_rows;
	if ($num_rows>0){	//id in database
		$query="SELECT admin.password FROM admin WHERE id='$id'"; //check password
		$result = mysqli_query($con,$query) or die(mysql_error());
		while($row = mysqli_fetch_array($result)){
			if ($password==$row['password']){
				$check=1;
				$_SESSION['id']=$id;
				$query="SELECT admin.email FROM admin WHERE id='$id'"; //get ID number
				$result = mysqli_query($con,$query) or die(mysql_error());
				while($row = mysqli_fetch_array($result)){
					$_SESSION['adminemail']=$row['email'];
					break;
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
	<form action  = "admin.php" method = "post">
		Admin Id: <input type = "text" name = "id"/><br />
		Password: <input type = "password" name = "password"/><br />
		<input type = "submit"  value = "Login" />
		<a href="http://localhost:8080/1520p2/reset.php">Reset password</a> <br />
	</form>
<?php 
}
?>
</body> 
</html> 
	