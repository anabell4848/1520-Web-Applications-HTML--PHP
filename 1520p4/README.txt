Hanyu Xiong
CS 1520 Project 4
Dr. Ramirez
hax12@pitt.edu


- TA needs to enter his username and password in: init.php, incrementWon.php, incrementTot.php, and getWords.php.
- users.php contain the username and passwords to login to play.


Instructions:

- run init.php to initialize the database, may have to run twice if it doesn't drop the DB the first time
- run login.php to login as a user from users.php (ie. username=user1, password=a1)
	- unfilled fields cause an alert
	- wrong login redirects back to login page sayinng "wrong info"
	- correct login redirects to playGame page
	- if the user is already logged in it will redirect to playGame page
	- user must log out to go back to the login page, game remembers user login 
- play hangman in playGame.php
	- starts with a welcome page, click "start new round button to play", logout link underneath button   
	- once you start a game it shows
		- blanks for the word (filled with each correct guess)
		- num of guesses (incremented with each guess)
		- num of incorrect guesses (incremented with each incorrect guess, max at 7, where it restarts game)
		- guessed letters (shown with each letter guessed)
		- 26 buttons to click to guess that letter, once clicked the button dissapears so you can't use it again
		- can "start a new round" at any time, but if done during the game a confirmation alert will pop up
		- note: if you start a new round during a game your number of rounds played will be incremented, but you will not see your stats until you win or lose
	- with each guess an alert tells if it was inthe words or not
	- after 7 incorrect guesses game automatically ends
	- after you guessed the word game ends
	- after a win or loss the game tells you your stats (rounds played, rounds won, percent won), and give option to play again
