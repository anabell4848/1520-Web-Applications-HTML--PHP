<?php	
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- class for displaying all the tickets in a table

-TA change username and password

*/
if(!isset($_SESSION)){
    session_start();
}

class ticket{

	public function __construct()
	{ }
	public function seetickets($passquery, $b1, $b4, $b6){
		$con=mysqli_connect("localhost","hax12","123","project2db");		//USERNAME AND PASSWORD
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$result = mysqli_query($con, $passquery);
		//echo "passquery= $passquery";
		
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
		<th>Select</th>
		</tr>";
		?><form action="admin.php" method="POST"> <?php
		while($row = mysqli_fetch_array($result)) {
			$quer = substr($passquery, 14, 7);
			//echo "QUER=$quer";
			if ($quer=="similar"){
				$theid=$row['ticketid2'];
				$passquer="SELECT * FROM tickets WHERE ID='$theid'";
				$result2 = mysqli_query($con, $passquer);
				while($row = mysqli_fetch_array($result2)) {
				echo "<tr>";
				echo "<td>" . $row['ticketnum'] . "</td>";
				echo "<td>" . $row['time'] . "</td>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>" . $row['email'] . "</td>";
				echo "<td>" . $row['subject'] . "</td>";
				echo "<td>" . $row['description'] . "</td>";
				echo "<td>" . $row['tech'] . "</td>";
				echo "<td>" . $row['status'] . "</td>";
				echo "<td>" 
				//radio buttom for each ticket
				?> <input type = "radio" name = "choice" value= "<?php echo $row['ID'] ?>"></input><?php
				echo "</tr>";	
				}				
			}
			else{
				echo "<tr>";
				echo "<td>" . $row['ticketnum'] . "</td>";
				echo "<td>" . $row['time'] . "</td>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>" . $row['email'] . "</td>";
				echo "<td>" . $row['subject'] . "</td>";
				echo "<td>" . $row['description'] . "</td>";
				echo "<td>" . $row['tech'] . "</td>";
				echo "<td>" . $row['status'] . "</td>";
				echo "<td>" 
				//radio buttom for each ticket
				?> <input type = "radio" name = "choice" value= "<?php echo $row['ID'] ?>"></input><?php
				echo "</tr>";
			}
		}
		mysqli_close($con);
		echo "<tr>
		<th>Sort By "?> <input type = "radio" name = "choice" value= "ticketnum"></input><?php
		echo "</th>
		<th>Sort By "?> <input type = "radio" name = "choice" value= "received"></input><?php
		echo "</th>
		<th>Sort By "?> <input type = "radio" name = "choice" value= "sendername"></input><?php
		echo "</th>
		<th>Sort By "?> <input type = "radio" name = "choice" value= "senderemail"></input><?php
		echo "</th>
		<th>Sort By "?> <input type = "radio" name = "choice" value= "subject"></input><?php
		echo "</th>
		<th>Sort By "?> <input type = "radio" name = "choice" value= "description"></input><?php
		echo "</th>
		<th> </th>
		<th> </th>
		<th> </th>
		</tr>";
		
		?>
		<table border="0" id="table">
		<tr>
		<td><input type=submit id="button1" name="choice1" value="<?php echo "$b1"; ?>"></input>
		<td><input type=submit id="button2" name="choice2" value="Sort"></input>
		<td><input type=submit id="button3" name="choice3" value="View Selected Ticket"></input>
		</tr><tr>
		<td><input type=submit id="button4" name="choice4" value="<?php echo "$b4"; ?>"></input>
		<td></form><form action="http://localhost:8080/1520p2/clear.php">
			<input type=submit id="button5" name="choice5" value="Logout"></input>
			</form>
		<form action="admin.php" method="POST">
		<td><input type=submit id="button6" name="choice6" value="<?php echo "$b6"; ?>"></input>
		</tr>
		</form>
	<?php
	}
}