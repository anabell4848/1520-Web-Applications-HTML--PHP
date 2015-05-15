<!--
Hanyu Xiong
CS 1520 Project 4
Dr. Ramirez
hax12@pitt.edu


-->

<?php
	$username="hax12";		//ENTER USERNAME AND PASSWORD
	$password="123";

	$con=mysqli_connect("localhost",$username,$password);       //USERNAME AND PASSWORD
		// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		//drop database
	$sql="DROP DATABASE project4db";
	if (mysqli_query($con,$sql)) {
	  echo "Database project4db dropped successfully</br>";
	} else {
	  echo "Error dropping database: " . mysqli_error($con);
	}
		// Create database
	$sql="CREATE DATABASE project4db";
	if (mysqli_query($con,$sql)) {
	  echo "Database project4db created successfully</br>";
	} else {
	  echo "Error creating database: " . mysqli_error($con);
	}
	mysqli_close($con);
	
	
	$db=mysqli_connect("localhost",$username,$password,"project4db");
	if ($db->connect_error): 
       die ("Could not connect to db " . $db->connect_error); 
	endif;
   
	$db->query("drop table Words"); 
	$result = $db->query("create table Words (id int primary key not null auto_increment, word varchar(30) not null)") or die ("Invalid: " . $db->error);

	$fp = fopen("words.txt", "r");
	while ($currword = fgets($fp, 80)):
        $currword = rtrim($currword);
		$currword=strtoupper("$currword");
        $query = "insert into Words values (NULL, '$currword')"; 
        echo "$query<br/>";
		$db->query($query) or die ("Invalid insert " . $db->error); 
	endwhile;
	
	// Create user table
	$sql = "CREATE TABLE user 
	(
	id VARCHAR(222)NOT NULL,
	email VARCHAR(320) NOT NULL,
	password VARCHAR(64) NOT NULL,
	roundstot INT NOT NULL,
	roundswon INT NOT NULL
	)";
		// Execute query
	if (mysqli_query($db,$sql)) {
	  echo "Table user created successfully</br>";
	} else {
	  echo "Error creating table: " . mysqli_error($db);
	}
		//insert users into table
	$users = file("users.php");
	$users = array_slice($users, 1, -1);
	foreach ($users as $user):
		$user = addslashes(rtrim($user));
		$chunks = explode("#", $user);
		//echo "$user</br>";
		//hashing the password for security
		$pass = hash('sha256',$chunks[2]);
		
		$res=mysqli_query($db,"INSERT INTO user VALUES ('$chunks[0]','$chunks[1]','$pass','$chunks[3]','$chunks[4]' )");
		if($res):
			echo "insert successful</br>";
		else:
			echo"NOPE";
		endif;
	endforeach;
	
	
?>
<html>
   <head>
       <title>Generating a Words Table</title>
   </head>
   <body>
<?php
   echo "Words should be set up properly";
?>
   </body>
</html> 