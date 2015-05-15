<?php	
/*
Hanyu Xiong
CS 1520 Project 2
Dr. Ramirez
hax12@pitt.edu

- form for sending a email out to a ticket submitter

*/
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send a Mail Message</title>
</head>
<body>
    <form action = "adminmailer.php"
          method = "POST">

    Subject
    <input type = "text" name = "subject" size = "60" maxlength = "60">
    <br /><br />
    Your message below:
    <br />
    <textarea name="msg" rows="5" cols="60"></textarea>
    <br /><br />
    <input type = "submit" value = "Submit">
    </form>
</body>
</html>
