<?php
if(!isset($_SESSION)){
    session_start();
}

	$username="hax12";		//ENTER USERNAME AND PASSWORD
	$password="123";
	
   $db=mysqli_connect("localhost",$username, $password,"project4db");	
   if ($db->connect_error):
      die ("Could not connect to db " . $db->connect_error);
   endif;
	
	$id=$_SESSION['id'];
	mysqli_query($db,"UPDATE user SET roundswon=roundswon+1 WHERE id='$id'");
	  
	$query = "select * from user WHERE id='$id'";
   $result = $db->query($query);
   $rows = $result->num_rows;
   if ($rows >= 1):
      header('Content-type: text/xml');
      echo "<?xml version='1.0' encoding='utf-8'?>";
      echo "<Word>";
      $row = $result->fetch_array();
      $ans = $row["roundstot"];
      echo "<tot>$ans</tot>";
      $ans = $row["roundswon"];
      echo "<won>$ans</won>";
      echo "</Word>";
   else:
      die ("DB Error");
   endif; 
?>