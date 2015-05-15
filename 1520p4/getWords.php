<?php
	$username="hax12";		//ENTER USERNAME AND PASSWORD
	$password="123";
	
   $db=mysqli_connect("localhost",$username, $password,"project4db");	
   if ($db->connect_error):
      die ("Could not connect to db " . $db->connect_error);
   endif;

   $query = "select word from Words order by rand() limit 1";
   $result = $db->query($query);
   $rows = $result->num_rows;
   if ($rows >= 1):
      header('Content-type: text/xml');
      echo "<?xml version='1.0' encoding='utf-8'?>";
      echo "<Word>";
      $row = $result->fetch_array();
      $ans = $row["word"];
      echo "<value>$ans</value>";
      echo "</Word>";
   else:
      die ("DB Error");
   endif; 
?>
