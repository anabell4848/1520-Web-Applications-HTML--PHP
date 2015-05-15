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
if($_SESSION['usertype']!="user" && $_SESSION['usertype']!="admin" ){
	die;
}
	
$username="hax12";		//ENTER USERNAME AND PASSWORD
$password="123";

include_once "mailer.php";  
date_default_timezone_set('America/New_York');

$db = new mysqli('localhost', $username, $password, 'project3db');
if ($db->connect_error):
	die ("Could not connect to db " . $db->connect_error);
endif;

	// Set header for xml document and add root tag
	header('Content-type: text/xml');
	echo "<?xml version='1.0' encoding='utf-8'?>";
	echo "<CDResponse>";

	$type = $_POST["type"];
	if ($type == 1):
	elseif ($type == 2):
		$numrows = strip_tags($_POST["rows"]);
		$name =$_SESSION["id"];
		$email =$_SESSION["email"];
		$subject = strip_tags($_POST["subject"]);
		$description = strip_tags($_POST["description"]);
		$newrows = "";
		$query = "lock tables tickets write";
		$result = $db->query($query) || die($db->error);
		$query = "select ticketnum, time, name, email, subject, description, tech, status from tickets";
		$rr = $db->query("select ticketnum, time, name, email, subject, description, tech, status from tickets");
		$resrows = $rr->num_rows;
		$ticketnumctr=0;
		
		// Generate new rows to return using XML tags.  The Type is returned
		// as Update followed by a sequence of CD elements.
		if ($numrows < $resrows):
			$newrows .= "<Type>Update</Type>";
			for ($i = $numrows; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
				$ticketnumctr=$curr["ticketnum"]+1;
			endfor;
		endif;
		// If no rows are added, return Type Ack and Response Ok.
		
		$time = date('Y-m-d H:i:s', time());
		//if ($newrows == ""):
			$ticketnumctr=$resrows+1;
			$newrows .= "<Type>Ack</Type>";
			$newrows .= "<Response>OK</Response>";
			$newrows .= "<Ticketnum>$ticketnumctr</Ticketnum>";
			$newrows .= "<Time>$time</Time>";
			$newrows .= "<Name>$name</Name>";
			$newrows .= "<Email>$email</Email>";;
		//endif;
		$query = "insert into tickets values ('$ticketnumctr','$time','$name','$email','$subject','$description','none','open', NULL)";
		
		$result = $db->query($query) || die($db->error);
		$query = "unlock tables";
		$result = $db->query($query) || die($db->error);
		
		//Send an email confirmation to user
		$body ="Dear ".$name.", your ticket has been confirmed";
		$mailer='Ticket Admin';
		$mailit=new mailer();
		$mailit->sendmail($email, $name, $subject, $body, $mailer);
		
		//Send email to all tech administrators
		$body ='Dear Administrators, New ticket alert';
		$mailer='Ticket Center';
		$query="SELECT * FROM admin";
		$result = mysqli_query($db,$query);
		while($row = mysqli_fetch_array($result)) {//loop through all the administrators
			//echo $row['email'];
			//echo "<br>";
			$adminsemail=$row['email'];
			$mailit->sendmail($adminsemail, $name, $subject, $body, $mailer);
		}
		
		echo "$newrows"; 
	elseif($type == 3):	//see refresh ticket table
		$numrows = strip_tags($_POST["rows"]);
		$newrows="";
		$rr = $db->query("select ticketnum, time, name, email, subject, description, tech, status from tickets");
		$resrows = $rr->num_rows;
		
		//$newrows .= "<Type>".$resrows."</Type>";
		//echo "$newrows";
		// Same idea as above
		if ($numrows < $resrows):
			$newrows .= "<Type>Update</Type>";
			for ($i = $numrows; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
					$newrows .= "<CD>";
				$newrows .= "<ticketnum>" . $curr["ticketnum"] . "</ticketnum>";
				$newrows .= "<time>" . $curr["time"] . "</time>";
				$newrows .= "<name>" . $curr["name"] . "</name>";
				$newrows .= "<email>" . $curr["email"] . "</email>";
				$newrows .= "<subject>" . $curr["subject"] . "</subject>";
				$newrows .= "<description>" . $curr["description"] . "</description>";
				$newrows .= "<tech>" . $curr["tech"] . "</tech>";
				$newrows .= "<status>" . $curr["status"] . "</status>";;
					$newrows .= "</CD>";

					//$newrows = "<Type>". $curr["name"] ."</Type>";
				
			endfor;
		endif;
		if ($newrows == ""):
			$newrows .= "<Type>Ack</Type>";
			$newrows .= "<Response>OK</Response>";;
		endif;
		echo "$newrows";
	elseif( $type == 4):// filter and see only the user's tickets
		$numrows = strip_tags($_POST["rows"]);
		$newrows="";
		$rr = $db->query("select ticketnum, time, name, email, subject, description, tech, status from tickets");
		$resrows = $rr->num_rows;
		
		//$newrows .= "<Type>".$resrows."</Type>";
		//echo "$newrows";
		// Same idea as above
		if ($numrows < $resrows):
			$newrows .= "<Type>Update</Type>";
			for ($i = $numrows; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
				if ($curr["name"]==$_SESSION['id']){
						$newrows .= "<CD>";
					$newrows .= "<ticketnum>" . $curr["ticketnum"] . "</ticketnum>";
					$newrows .= "<time>" . $curr["time"] . "</time>";
					$newrows .= "<name>" . $curr["name"] . "</name>";
					$newrows .= "<email>" . $curr["email"] . "</email>";
					$newrows .= "<subject>" . $curr["subject"] . "</subject>";
					$newrows .= "<description>" . $curr["description"] . "</description>";
					$newrows .= "<tech>" . $curr["tech"] . "</tech>";
					$newrows .= "<status>" . $curr["status"] . "</status>";;
						$newrows .= "</CD>";

					//$newrows = "<Type>". $curr["name"] ."</Type>";
				}
			endfor;
		endif;
		if ($newrows == ""):
			$newrows .= "<Type>Ack</Type>";
			$newrows .= "<Response>OK</Response>";;
		endif;
		echo "$newrows";
	elseif ($type ==5):	//type=5, reset password
		$password = $_POST["password"];
		$pass = hash('sha256',$password);
		$name = $_SESSION["id"];
		$usertype=$_SESSION['usertype'];
		if ($usertype=='user'):
			$passquery="UPDATE user SET password='$pass' WHERE id='$name'";
		else:
			$passquery="UPDATE admin SET password='$pass' WHERE id='$name'";
		endif;
		//mysqli_query($db, $passquery);
		if (mysqli_query($db,$passquery)) {
			$newrows = "<Type>".$password."</Type>";
		} 
		else {
			$newrows = "<Type>".$pass."</Type>";
		}
		echo "$newrows";
	elseif ($type ==6): 		//type = 6 for filtering admin view, and selecting
		$filter = $_POST["filter"];
		$newrows="";
		$rr = $db->query("select ticketnum, time, name, email, subject, description, tech, status from tickets");
		$resrows = $rr->num_rows;
		
		//echo "$newrows";
		if ($filter=="open"):
			for ($i = 0; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
				$newrows .= "<Type>".$filter."</Type>";
				if ($curr["status"]=="open"):
						$newrows .= "<CD>";
					$newrows .= "<ticketnum>" . $curr["ticketnum"] . "</ticketnum>";
					$newrows .= "<time>" . $curr["time"] . "</time>";
					$newrows .= "<name>" . $curr["name"] . "</name>";
					$newrows .= "<email>" . $curr["email"] . "</email>";
					$newrows .= "<subject>" . $curr["subject"] . "</subject>";
					$newrows .= "<description>" . $curr["description"] . "</description>";
					$newrows .= "<tech>" . $curr["tech"] . "</tech>";
					$newrows .= "<status>" . $curr["status"] . "</status>";;
						$newrows .= "</CD>";
					//$newrows = "<Type>". $curr["name"] ."</Type>";
				endif;
			endfor;
		elseif ($filter =="my"):
			for ($i = 0; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
				$newrows .= "<Type>".$filter."</Type>";
				$tech="";
				$tech=$_SESSION['id'];					
				if ($curr["tech"]==$tech):
					$newrows .= "<CD>";
					$newrows .= "<ticketnum>" . $curr["ticketnum"] . "</ticketnum>";
					$newrows .= "<time>" . $curr["time"] . "</time>";
					$newrows .= "<name>" . $curr["name"] . "</name>";
					$newrows .= "<email>" . $curr["email"] . "</email>";
					$newrows .= "<subject>" . $curr["subject"] . "</subject>";
					$newrows .= "<description>" . $curr["description"] . "</description>";
					$newrows .= "<tech>" . $curr["tech"] . "</tech>";
					$newrows .= "<status>" . $curr["status"] . "</status>";;
						$newrows .= "</CD>";
				endif;
			endfor;
		elseif ($filter =="unassigned"):
			for ($i = 0; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
				$newrows .= "<Type>".$filter."</Type>";
				if ($curr["tech"]=="none"):
						$newrows .= "<CD>";
					$newrows .= "<ticketnum>" . $curr["ticketnum"] . "</ticketnum>";
					$newrows .= "<time>" . $curr["time"] . "</time>";
					$newrows .= "<name>" . $curr["name"] . "</name>";
					$newrows .= "<email>" . $curr["email"] . "</email>";
					$newrows .= "<subject>" . $curr["subject"] . "</subject>";
					$newrows .= "<description>" . $curr["description"] . "</description>";
					$newrows .= "<tech>" . $curr["tech"] . "</tech>";
					$newrows .= "<status>" . $curr["status"] . "</status>";;
						$newrows .= "</CD>";
				endif;
			endfor;
		elseif ($filter =="all"):
			for ($i = 0; $i < $resrows; $i++):
				$newrows .= "<Type>".$filter."</Type>";
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
					$newrows .= "<CD>";
				$newrows .= "<ticketnum>" . $curr["ticketnum"] . "</ticketnum>";
				$newrows .= "<time>" . $curr["time"] . "</time>";
				$newrows .= "<name>" . $curr["name"] . "</name>";
				$newrows .= "<email>" . $curr["email"] . "</email>";
				$newrows .= "<subject>" . $curr["subject"] . "</subject>";
				$newrows .= "<description>" . $curr["description"] . "</description>";
				$newrows .= "<tech>" . $curr["tech"] . "</tech>";
				$newrows .= "<status>" . $curr["status"] . "</status>";;
					$newrows .= "</CD>";
			endfor;
		else:
			for ($i = 0; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
				$newrows .= "<Type>".$filter."</Type>";
				if ($curr["ticketnum"]==$filter):
						$newrows .= "<CD>";
					$newrows .= "<ticketnum>" . $curr["ticketnum"] . "</ticketnum>";
					$newrows .= "<time>" . $curr["time"] . "</time>";
					$newrows .= "<name>" . $curr["name"] . "</name>";
					$newrows .= "<email>" . $curr["email"] . "</email>";
					$newrows .= "<subject>" . $curr["subject"] . "</subject>";
					$newrows .= "<description>" . $curr["description"] . "</description>";
					$newrows .= "<tech>" . $curr["tech"] . "</tech>";
					$newrows .= "<status>" . $curr["status"] . "</status>";;
						$newrows .= "</CD>";
				endif;
			endfor;
		endif;
				//$newrows = "<Type>". $curr["name"] ."</Type>";
		echo "$newrows";
	else:	//type = 7
		$status = "";
		$ticketnum = $_POST["ticketnum"];
		$rr = $db->query("select ticketnum, time, name, email, subject, description, tech, status from tickets");
		$resrows = $rr->num_rows;
		for ($i = 0; $i < $resrows; $i++):
				$rr->data_seek($i);
				$curr = $rr->fetch_array();
				if ($curr["ticketnum"]==$ticketnum):
					$status=$curr["status"];
					break;
				endif;
			endfor;
		if ($status=="open"){
			$passquery="UPDATE admin SET status='closed' WHERE ticketnum='$ticketnum'";
		}else{
			$passquery="UPDATE admin SET status='open' WHERE ticketnum='$ticketnum'";
		}
		//mysqli_query($db, $passquery);
		if (mysqli_query($db,$passquery)) {
			$newrows = "<Type>".$status."</Type>";
		} 
		else {
			$newrows = "<Type>".$status."</Type>";
		}
		echo "$newrows";
		
	endif;
	// Close with the correct end tag
	echo "</CDResponse>";
?>
