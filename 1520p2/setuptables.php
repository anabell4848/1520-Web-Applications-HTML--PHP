<?php
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- deletes the project2db database and makes a new database
- creates 3 tables, admin for administrators and their info, tickets to store 
  info of all tickets, and simialr to compare all the tickets to see how many 
  words they have in common in their description
- add administrators.php and closedtickets.php information to the tables, and add to 
  similar table too
  
  
- TA needs to change username and password for the database, and need to go in 
  administrators.php to change the emails of the administrators to their own.
- next go to submit.php to add more tickets

*/


	?>
<!DOCTYPE html>
<html>
 <head>
  <title>Script to Initialize Quotes Database</title>
 </head>
 <body>
 <?php
		// Create connection
	$con=mysqli_connect("localhost","hax12","123");       //USERNAME AND PASSWORD
		// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		//drop database
	$sql="DROP DATABASE project2db";
	if (mysqli_query($con,$sql)) {
	  echo "Database project2db dropped successfully</br>";
	} else {
	  echo "Error dropping database: " . mysqli_error($con);
	}
		// Create database
	
	$sql="CREATE DATABASE project2db";
	if (mysqli_query($con,$sql)) {
	  echo "Database project2db created successfully</br>";
	} else {
	  echo "Error creating database: " . mysqli_error($con);
	}
	mysqli_close($con);
	
		// Create connection  
	$con=mysqli_connect("localhost","hax12","123","project2db");	//USERNAME AND PASSWORD for Salim
		// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		// Create ADMIN table
	$sql = "CREATE TABLE admin 
	(
	id VARCHAR(222)NOT NULL,
	email VARCHAR(320) NOT NULL,
	password VARCHAR(64) NOT NULL,
	token VARCHAR(222)
	)";
		// Execute query
	if (mysqli_query($con,$sql)) {
	  echo "Table admin created successfully</br>";
	} else {
	  echo "Error creating table: " . mysqli_error($con);
	}
		//insert admins into table
	$administrators = file("administrators.php");
	$administrators = array_slice($administrators, 1, -1);
	foreach ($administrators as $administrator):
		$administrator = rtrim($administrator);
		$administrator = preg_replace('/#/','',$administrator);
		//echo "$administrator</br>";
		$chunks = explode(" ", $administrator);
		//hashing the password for security
		$pass = hash('sha256',$chunks[2]);
		
		$res=mysqli_query($con,"INSERT INTO admin VALUES ('$chunks[0]','$chunks[1]','$pass',NULL)");
		if($res):
			echo "insert successful</br>";
		else:
			echo"NOPE";
		endif;
	endforeach;
	
		// Create TICKETS table
	$sql = "CREATE TABLE tickets 
	(
	ticketnum INT NOT NULL,
	time DATETIME NOT NULL,
	name VARCHAR(222) NOT NULL,
	email VARCHAR(320) NOT NULL,
	subject TINYTEXT NOT NULL,
	description TEXT NOT NULL,
	tech VARCHAR(320),
	status VARCHAR(222) NOT NULL,
	ID INT AUTO_INCREMENT,
	PRIMARY KEY (`ID`)
	)";
		// Execute query
	if (mysqli_query($con,$sql)) {
	  echo "Table tickets created successfully</br>";
	} else {
	  echo "Error creating table: " . mysqli_error($con);
	}

	// Create SIMILAR table
	$sql = "CREATE TABLE similar 
	(
	ticketid1 INT NOT NULL,
	ticketid2 INT NOT NULL,
	nummatches INT NOT NULL
	)";
		// Execute query
	if (mysqli_query($con,$sql)) {
	  echo "Table similar created successfully</br>";
	} else {
	  echo "Error creating table: " . mysqli_error($con);
	}
	
	//add closed tickets to tickets table
	$closedtickets = file("closedtickets.php");
	$closedtickets = array_slice($closedtickets, 1, -1);
	foreach ($closedtickets as $closedticket):
		$closedticket = addslashes(rtrim($closedticket));
		//echo "$closedticket</br>";
		$chunks = explode("#", $closedticket);
		//print_r($chunks);
		$res=mysqli_query($con,"INSERT INTO tickets 
		VALUES ('$chunks[0]','$chunks[1]','$chunks[2]','$chunks[3]','$chunks[4]','$chunks[5]','$chunks[6]','$chunks[7]', NULL)");
		if($res):
			echo "insert successful tickets</br>";
		else:
			echo"NOPE</br>";
		endif;
	endforeach; 
	
		//insert into similar table
	$sql="TRUNCATE TABLE similar";	//first empty the table
	if (mysqli_query($con,$sql)) {
	  echo "table similar dropped successfully</br>";
	} else {
	  echo "Error dropping database: " . mysqli_error($con);
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
				echo "insert successful ticket</br>";
			else:
				echo"NOPE</br>";
			endif;
		}
	}
	
	
function matches($description1, $description2){
	$arr1 = explode(" ",$description1 );
	$arr2 = explode(" ",$description2 );
	$result = array_intersect($arr1 , $arr2 ); //matched elements
	$num = count($result); //number of matches
	return $num;
}

?>
 </body>
</html>
