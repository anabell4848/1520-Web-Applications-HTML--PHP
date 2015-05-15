<?php
session_start();
	//set last attempted date to today

//date_default_timezone_set('America/New_York');	
	$username = $_SESSION['username'];
	setcookie("$username", date('m/d/Y', time()));
	
//set session variables
if (isset($_SESSION['quesctr'])):
	$quesctr = $_SESSION['quesctr'];
else:
	$quesctr=0;
endif;
if (isset($_SESSION['answer']))
	$matchanswer = $_SESSION['answer'];
if (isset($_SESSION['coranswer']))
	$matchcor=$_SESSION['coranswer'];
if (isset($_SESSION['correct']))
    $correct = $_SESSION['correct'];
else
    $correct = 0;


//$dw = date( "w", time());
//srand($dw );
//echo "$randval<br />";


	
	
	//echo "$quizfile";
	//echo "question counter = $quesctr";
	?>
		<form action="quizprocess.php" 
			  method="POST">
	<?php  
	//check if there is an answer and if it's correct
	if (isset($_POST["choice"])){
		$choice = $_POST["choice"];
		//echo "CHOICE = $choice, correct answer = $matchanswer <br />";
		if ((int)$choice ==(int)$matchanswer){
			$correct++;
			$_SESSION['correct']=$correct;
			echo "Correct answer! It was $matchcor<br />";
		}
		else{
			echo "Wrong answer! The correct answer is $matchcor<br />";
		}
	}

	$lines=file("quizzes.txt");
	
if (count($lines)>0):
	if ((int)$quesctr ==0):
		$randval = rand(0,count($lines)-1);
		$_SESSION['random']=$randval;
	endif;
	if ((int)$quesctr <5):
		$randval=$_SESSION['random'];
		$chunks= explode("#",$lines[$randval]);
		$quizfile = $chunks[0];
		$quizlines=file("$quizfile");
		$quizchunks= explode("#",$quizlines[$quesctr]);
		$quesctr++;
		echo "$quesctr.";
		$_SESSION['quesctr'] = $quesctr;
		$question = $quizchunks[0];
		echo "$question<br />";
		$choices = explode(":",$quizchunks[1]);
		//print_r($choices);
		$ctr=0;
		$answer = $quizchunks[2];
		$_SESSION['answer'] = $answer;	
		//echo "$answer";
		$coranswer = $choices[(int)$answer];
		$_SESSION['coranswer'] = $coranswer;
		  
		//display question
		foreach ($choices as $key=>$value):
			?>
			<input type="radio" name="choice" value="<?php echo $ctr ?>">
			<?php echo "$value<br />"; ?> 
		<?php
			$ctr++;
		endforeach;
		?>
		<input type = "submit" value = "Process">
		
		</form>
		<?php
	else:
		$randval=$_SESSION['random'];
		unset($lines[$randval]);
		$newline= implode("",$lines);
		$fileptr = fopen("quizzes.txt", "w");
		fwrite($fileptr, "$newline");
		fclose($fileptr);
		
		$percentcor= $correct/5*100;
		echo "How many correct: $correct<br />";
		echo "Percent correct: $percentcor%<br />";
		
		unset($_SESSION["answer"]);
		unset($_SESSION["quesctr"]);
		unset($_SESSION["coranswer"]);
		unset($_SESSION["correct"]);
	endif;
else:
	echo "You've finished all the quizzes, you're such a smarta$$!<br/>";
endif;
?>