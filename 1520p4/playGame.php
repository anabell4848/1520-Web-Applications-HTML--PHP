<?php	
/*
Hanyu Xiong
CS 1520 Project 4
Dr. Ramirez
hax12@pitt.edu


*/
if(!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['id'])){
	header('Location:login.php');
	exit();
}
//var_dump($_SESSION);


?>
<!DOCTYPE html>
<html>
<head>
<title>Hangman Game</title>
<link rel = "stylesheet" type = "text/css" href = "wordStyle.css"/>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">

    function restartSure(){
		//alert("HERE"+wordCount+won+lost);
		if (wordCount!=0 && won!=1 && lost!=1){
			if (confirm('Are you sure you want to start a new round?')) {
				requestWord();
			} else {
				// Do nothing!
			}
		}
		else{
			requestWord();
		}
	}
	
    function requestWord() {
		$.post("incrementTot.php", "", function(data){
			roundstot = $(data).find("tot").text();
			roundswon = $(data).find("won").text();
			//alert("HERE"+roundstot+" "+roundswon);
		});
		reset();
		showButtons();
		$.post("getWords.php", "", function(data){
			word = $(data).find("value").text();
			//alert("HERE"+word);
			checkUsedWord(word);
        });
    }
	
	function reset(){
		won=0;
		lose=0;
		lost=0;
		guessCount=0; 
		incorrectCount=0;
		var $spans = $( "span" );
		$spans.eq( 0 ).empty();
		$spans.eq( 1 ).empty();
		$spans.eq( 3 ).empty();
		$spans.eq( 5 ).empty();
		tempWord = [];
		Word=[];
	}
    // Search for the word in the used-word list.  If found, increment its
    // count.  If not found, add it to the list and append a new row to the table.
	function checkUsedWord(word)    {
        var found = 0;
        for (var i = 0; i < wordCount; i++) {
            if (usedWords[i] == word) {
				//alert("used: "+word);
                requestWord();
				found=1;
                break;
            }
        }
        if (found == 0) {
			displayBlanks(word);
			usedWords[i] = word;
			//alert(usedWords[i]+"  "+wordCount);
			wordCount++;
        }
    }
	
	function displayBlanks(word) {
		$.each(word.split(''), function(i, val) {
			tempWord[i]=" _ ";
			Word[i]=val;
			//alert(tempWord[i]+"  "+Word[i]);
			var $spans = $( "span" );
			$spans.eq( 0 ).append(tempWord[i]);
		});
	}
	
	function guess(s){
		//alert(s);
		
		var $spans = $( "span" );
		
		var but = "BUTTON"+s;
		document.getElementById(but).style.visibility = 'hidden';
		addGuess(s);
		
		var correct=0;
		won=1;
		//$("#theWordDisplay").empty();
		$spans.eq( 0 ).empty();
		for (var i=0; i<Word.length; i++){
			if (Word[i]==s){
				tempWord[i]=s;
				correct=1;
				//alert(tempWord+i);
			}
			if (tempWord[i]==' _ '){
				//alert(tempWord[i]);
				won=0;
			}
			$spans.eq( 0 ).append(tempWord[i]);
		}
		if (correct==0){
			incorrectCount++;
			alert("Sorry, "+ s+" was not in the word");
		}
		else {
			alert("Yay! "+s+" was in the word");
		}
		$spans.eq( 3 ).text(incorrectCount);
		
		
		lost=0;
		if (incorrectCount>=7){
			alert("You Lost! The word was "+word);
			lost=1;
			hideButtons();
			//alert("HERE");
			showResults();
		}
		if (won==1){
			alert("You Won!!! ");
			//alert("HERE"+roundsTot+" "+roundsWon+" "+roundsWon/roundsTot);
			hideButtons();
			//document.getElementById("second").style.visibility = 'visible';
			$.post("incrementWon.php", "", function(data){
				roundstot = $(data).find("tot").text();
				roundswon = $(data).find("won").text();
				//alert("HERE"+roundstot+" "+roundswon);
				showResults();
			});
		}
	}

    function addGuess(l) {
		var $spans = $( "span" );
		$spans.eq( 5 ).append(l);
		
		guessCount++;
		$spans.eq( 1 ).text(guessCount);
    }
	function showResults(){
		document.getElementById("result1").style.visibility = 'visible';
		document.getElementById("result2").style.visibility = 'visible';
		document.getElementById("result3").style.visibility = 'visible';
		var $spans = $( "span" );
			//alert(roundstot+" "+roundswon);
		$spans.eq( 2 ).text(roundstot);
		$spans.eq( 4 ).text(roundswon);
		$spans.eq( 6 ).text(roundswon/roundstot*100);
	}
	function hideResults(){
		document.getElementById("result1").style.visibility = 'hidden';
		document.getElementById("result2").style.visibility = 'hidden';
		document.getElementById("result3").style.visibility = 'hidden';
	}
	function hideButtons(){		
		$('INPUT').each(function(){
			var id=this.id;
			//alert("button "+ id);
			document.getElementById(id).style.visibility = 'hidden';
		});
		document.getElementById("start").style.visibility = 'visible';
		hideResults();
		document.getElementById("toGuess").style.visibility = 'hidden';
		document.getElementById("numofGuesses").style.visibility = 'hidden';
		document.getElementById("numofIncorrectGuesses").style.visibility = 'hidden';
		document.getElementById("guessed").style.visibility = 'hidden';
	}
		
	function showButtons(){		
		$( "#welcome1" ).remove();
		$( "#welcome2" ).remove();
		$('INPUT').each(function(){
			var id=this.id;
			//alert("button "+ id);
			document.getElementById(id).style.visibility = 'visible';
		});
		//alert("DONE");
		hideResults();
		document.getElementById("toGuess").style.visibility = 'visible';
		document.getElementById("numofGuesses").style.visibility = 'visible';
		document.getElementById("numofIncorrectGuesses").style.visibility = 'visible';
		document.getElementById("guessed").style.visibility = 'visible';
	}
	

</script>
</head>
<body onload = "hideButtons()" > 

<P id="toGuess"> Guess the Word: <br/><br/><span></span></P><br/>
<P id="numofGuesses"> Number of guesses: <span></span></P>
<P id="result1"> Total rounds played: <span></span></P>
<h2 id="welcome1">WELCOME TO HANGMAN, SHALL WE PLAY?</h2>
<P id="numofIncorrectGuesses"> Number of incorrect guesses: <span></span></P>
<h3 id="welcome2">Click the botton below to start</h3>
<P id="result2"> Total rounds won: <span></span></P>
<P id="guessed"> Guessed letters: <span></span></P>
<P id="result3"> Winning Percentage: <span></span>%</P>
<P> </P>
<table id = "first" border="0" >
	<tr> <td>
	<INPUT TYPE="BUTTON" VALUE=" A " id = "BUTTONA" ONCLICK="guess('A')">
	<INPUT TYPE="BUTTON" VALUE=" B " id = "BUTTONB" ONCLICK="guess('B')">
	<INPUT TYPE="BUTTON" VALUE=" C " id = "BUTTONC" ONCLICK="guess('C')">
	<INPUT TYPE="BUTTON" VALUE=" D " id = "BUTTOND" ONCLICK="guess('D')">
	<INPUT TYPE="BUTTON" VALUE=" E " id = "BUTTONE" ONCLICK="guess('E')">
	<INPUT TYPE="BUTTON" VALUE=" F " id = "BUTTONF" ONCLICK="guess('F')">
	<INPUT TYPE="BUTTON" VALUE=" G " id = "BUTTONG" ONCLICK="guess('G')">
	<INPUT TYPE="BUTTON" VALUE=" H " id = "BUTTONH" ONCLICK="guess('H')">
	<INPUT TYPE="BUTTON" VALUE=" I " id = "BUTTONI" ONCLICK="guess('I')">
	<INPUT TYPE="BUTTON" VALUE=" J " id = "BUTTONJ" ONCLICK="guess('J')">
	<INPUT TYPE="BUTTON" VALUE=" K " id = "BUTTONK" ONCLICK="guess('K')">
	<INPUT TYPE="BUTTON" VALUE=" L " id = "BUTTONL" ONCLICK="guess('L')">
	<INPUT TYPE="BUTTON" VALUE=" M " id = "BUTTONM" ONCLICK="guess('M')">
	</tr>
	<tr><td>
	<INPUT TYPE="BUTTON" VALUE=" N " id = "BUTTONN" ONCLICK="guess('N')">
	<INPUT TYPE="BUTTON" VALUE=" O " id = "BUTTONO" ONCLICK="guess('O')">
	<INPUT TYPE="BUTTON" VALUE=" P " id = "BUTTONP" ONCLICK="guess('P')">
	<INPUT TYPE="BUTTON" VALUE=" Q " id = "BUTTONQ" ONCLICK="guess('Q')">
	<INPUT TYPE="BUTTON" VALUE=" R " id = "BUTTONR" ONCLICK="guess('R')">
	<INPUT TYPE="BUTTON" VALUE=" S " id = "BUTTONS" ONCLICK="guess('S')">
	<INPUT TYPE="BUTTON" VALUE=" T " id = "BUTTONT" ONCLICK="guess('T')">
	<INPUT TYPE="BUTTON" VALUE=" U " id = "BUTTONU" ONCLICK="guess('U')">
	<INPUT TYPE="BUTTON" VALUE=" V " id = "BUTTONV" ONCLICK="guess('V')">
	<INPUT TYPE="BUTTON" VALUE=" W " id = "BUTTONW" ONCLICK="guess('W')">
	<INPUT TYPE="BUTTON" VALUE=" X " id = "BUTTONX" ONCLICK="guess('X')">
	<INPUT TYPE="BUTTON" VALUE=" Y " id = "BUTTONY" ONCLICK="guess('Y')">
	<INPUT TYPE="BUTTON" VALUE=" Z " id = "BUTTONZ" ONCLICK="guess('Z')">
</tr>
<tr><td><INPUT TYPE="BUTTON" NAME="restart" id="start" VALUE="Start a New Round" ONCLICK="restartSure()">
</tr><tr><td><a href="http://localhost:8080/1520p4/clear.php">Log Out</a>
</tr>
	
<script type="text/javascript">
    var tempWord = new Array(), Word = new Array(), usedWords = new Array(), word, won=1, lost=0, roundswon, roundstot, wordCount = 0, guessCount=0, incorrectCount=0;
</script>
</body>
</html>
