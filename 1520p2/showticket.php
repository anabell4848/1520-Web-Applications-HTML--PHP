

<script src="http://code.jquery.com/jquery-latest.js"></script>
<?php	
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- class for displaying the selected ticket

-TA change username and password

*/
if(!isset($_SESSION)){
    session_start();
}
class showticket{

	public function __construct()
	{ }
	public function seeticket($passquery, $ticketchoice){
		$con=mysqli_connect("localhost","hax12","123","project2db");		//USERNAME AND PASSWORD
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$result = mysqli_query($con, $passquery);
		//echo "pass query= $passquery";
		
		//display the default table
		echo "<table border='1'>
		<tr>
		<th>Ticket#</th>
		<th>Received</th>
		<th>Sender Name</th>
		<th>Sender Email</th>
		<th>Subject</th>
		<th>Description</th>
		<th>Tech</th>
		<th>Status</th>
		</tr>";
		$quer = substr($passquery, 0, 6);
		if ($quer=="UPDATE" || $quer=="DELETE"){
			$ticketid=$_SESSION['ticketid'];
			$passquery="SELECT * FROM tickets WHERE ID='$ticketid'";
			$result = mysqli_query($con, $passquery);
		}
		
		while($row = mysqli_fetch_array($result)) {
			echo "<tr>";
			echo "<td>" . $row['ticketnum'] . "</td>";
			echo "<td>" . $row['time'] . "</td>";
			echo "<td>" . $row['name'] . "</td>";
			echo "<td>" . $row['email'] . "</td>";
			echo "<td>" . $row['subject'] . "</td>";
			echo "<td>" . $row['description'] . "</td>";
			echo "<td>" . $row['tech'] . "</td>";
			echo "<td>" . $row['status'] . "</td>";
			echo "</tr>";
			if ($ticketchoice==$row['ID']){
				$_SESSION['name']=$row['name']; //set session variable to remember ticket submitter
				$_SESSION['ticketid']=$row['ID'];	//set session variable ticketid
				$_SESSION['openclosed']=$row['status'];
				$_SESSION['email']=$row['email'];
			}
		}			
		mysqli_close($con);
		
		?><form action="admin.php" method="POST">
		<table border="0" id="table">
		<tr>
		<td><input type=submit id="button1" name="closeopen" value="Close/reopen the ticket"></input>
		<td><input type=submit id="button2" name="assignself" value="Assign self to ticket"></input>
		<td><input type=submit id="button3" name="removeself" value="Remove self from ticket"></input>
		<td></form><form action="http://localhost:8080/1520p2/writemail.php">
			<input type=submit id="button4" name="emailsubmitter" value="Email the submitter"></input>
			</form>
		</tr><tr><form action="admin.php" method="POST">
		<td><input type=submit id="button5" name="deleteticket" value="Delete the ticket from DB"></input>
		<td><input type=submit id="button6" name="findall" value="Find all tickets from submitter"></input>
		<td><input type=submit id="button8" name="adminpage" value="Back to main administrator page"></input>
		<td><input type=submit id="button7" name="findsimilar" value="Find all similar tickets"></input>
		<td><input type=text id="button9" name="numofmatches" size = "3" maxlength = "3">How many words must match?</input>
		</tr>
		</form>
	<?php
	}
}