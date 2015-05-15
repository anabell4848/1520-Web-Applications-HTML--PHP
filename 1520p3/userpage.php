<!--
Hanyu Xiong
CS 1520 Project 3
Dr. Ramirez
hax12@pitt.edu


-->
<?php 

if(!isset($_SESSION)){
    session_start();
}
//var_dump($_SESSION);
$userid=$_SESSION['id'];
if ($_SESSION['usertype']!="user")
	die;

?>
<!DOCTYPE html>
<html>
<head>
<title>User Options</title>
<link rel = "stylesheet" type = "text/css" href = "CDstyle.css"/>
<script type="text/javascript">
    function processData() {
        var httpRequest;
 
        var type = arguments[0];  // get type of call

        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                }
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {}
            }
        }
        if (!httpRequest) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
		var data;
		if (type == 2){	//show user's tickets
			var rows = arguments[1];
			var subject = arguments[2];
			var description = arguments[3];
			data = 'type=' + type + '&rows=' + rows + '&subject=' + subject+ '&description=' + description; 
		}
		else {	//type =5 reset password
			var password = arguments[1];
			data = 'type=' + type + '&password=' +password; 
		}
        httpRequest.open('POST', 'tabulate.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		if (type == 2){	//show user's tickets
			httpRequest.onreadystatechange = function() { addNewRows(httpRequest, subject, description); } ;
        }
		else{
			//no functions
			httpRequest.onreadystatechange = function() { temp(httpRequest, password); } ;
		}
		httpRequest.send(data);
		//alert(data+" ALERT");
    }
	function temp(httpRequest, password){
		//function addNewRows(httpRequest, password){
		if (httpRequest.readyState == 4)  {
			if (httpRequest.status == 200){
				var root = httpRequest.responseXML.documentElement;
				//alert(root + "root alert");
				var rettype = root.getElementsByTagName('Type')[0].childNodes[0].nodeValue;
				//alert(rettype + "type alert");
			}
		}
	}

    function processWritein() {
		var numrows = document.getElementById("theTable").rows.length-1;
        var subject = document.pollForm.subject.value;
        var description = document.pollForm.description.value;
        var ok = true;
        if (subject == "" || description == ""){
            alert("Please fill out all the fields");
            ok = false;
        }
        if (ok){
            document.pollForm.subject.value = "";
            document.pollForm.description.value = "";
			hidesubmit();
			//alert(ok + subject+ description+"ALERT2");
            processData(2, numrows, subject, description);
        }
    }

    function showResults(httpRequest, choice){
        if (httpRequest.readyState == 4) {
			if (httpRequest.status == 200){
               // Get the root of the XML document
               var root = httpRequest.responseXML.documentElement;
               // Get the new count using the appropriate tag
               var newCount = root.getElementsByTagName('Count')[0].childNodes[0].nodeValue;
               //alert(newCount); 

               var T = document.getElementById("theTable");
               var R = T.rows[choice];
               var C = R.cells;
               var oldChild = C[2].childNodes[0];
               var txt = document.createTextNode(newCount);
               C[2].replaceChild(txt, oldChild);
               //C[2].innerHTML = newCount;
			}
			else
			{   alert('Problem with request'); }
		}
    }

    // Now instead of adding new rows to the table, they are added
    // to an array of CD objects.  This array is then sorted and
    // a new HTML table is created with its contents.  See more
    // comments in other functions.
    function addNewRows(httpRequest, subject, description){
		//function addNewRows(httpRequest, password){
		if (httpRequest.readyState == 4)  {
			if (httpRequest.status == 200){
				// Get the root of the XML document;
				
				var root = httpRequest.responseXML.documentElement;
				//alert(root + "root alert");
				
				var rettype = root.getElementsByTagName('Type')[0].childNodes[0].nodeValue;
				//alert(rettype + "type alert");
				
				var ticketnumber = root.getElementsByTagName('Ticketnum')[0].childNodes[0].nodeValue;
				var thistime = root.getElementsByTagName('Time')[0].childNodes[0].nodeValue;
				var name = root.getElementsByTagName('Name')[0].childNodes[0].nodeValue;
				var email = root.getElementsByTagName('Email')[0].childNodes[0].nodeValue;
				
				//if (rettype == "Ack"){
					//alert("ACK");
		   		addRowToList(ticketnumber, thistime, name, email, subject, description, "none", "open");
				/*}
				else{
					var ticketnumcount = 0;
           			var newRows = root.getElementsByTagName('CD');
		   			for (var i = 0; i < newRows.length; i++)
	       			{	
						var theRow = newRows[i];
                        var theticketnum = theRow.getElementsByTagName('ticketnum')[0].childNodes[0].nodeValue;
                        var thetime = theRow.getElementsByTagName('time')[0].childNodes[0].nodeValue;
                        var thename = theRow.getElementsByTagName('name')[0].childNodes[0].nodeValue;
                        var theemail = theRow.getElementsByTagName('email')[0].childNodes[0].nodeValue;
                        var thesubject = theRow.getElementsByTagName('subject')[0].childNodes[0].nodeValue;
                        var thedescription = theRow.getElementsByTagName('description')[0].childNodes[0].nodeValue;
                        var thetech = theRow.getElementsByTagName('tech')[0].childNodes[0].nodeValue;
						var thestatus = theRow.getElementsByTagName('status')[0].childNodes[0].nodeValue;
                        addRowToList(ticketnum, time, name, email, subject, description, tech, status);
						ticketnumcount=theticketnum+1;
					}
		   			addRowToList(ticketnumcount, thistime, name, email, subject, description, "none", "open");
				}*/
				// Once all CDs have been added, sort the list and regenerate
				// the table.
				showCDTable(); 
			}
			else{   alert('Problem with request'); }
		}
    }

    // This is receiving new rows via the auto-update functionality.
    // The logic is the same as the addNewRows except that we do not have
    // the additional write-in value to append at the end.
    function updateRows(httpRequest){
        if (httpRequest.readyState == 4){
			if (httpRequest.status == 200){
				var root = httpRequest.responseXML.documentElement;
				var rettype = root.getElementsByTagName('Type')[0].childNodes[0].nodeValue;
				if (rettype == "Update"){
					var newRows = root.getElementsByTagName('CD');
					for (var i = 0; i < newRows.length; i++){
						//alert("ALERT1");
                        var theRow = newRows[i];
                        var theticketnum = theRow.getElementsByTagName('ticketnum')[0].childNodes[0].nodeValue;
                        var thetime = theRow.getElementsByTagName('time')[0].childNodes[0].nodeValue;
                        var thename = theRow.getElementsByTagName('name')[0].childNodes[0].nodeValue;
                        var theemail = theRow.getElementsByTagName('email')[0].childNodes[0].nodeValue;
                        var thesubject = theRow.getElementsByTagName('subject')[0].childNodes[0].nodeValue;
                        var thedescription = theRow.getElementsByTagName('description')[0].childNodes[0].nodeValue;
                        var thetech = theRow.getElementsByTagName('tech')[0].childNodes[0].nodeValue;
						var thestatus = theRow.getElementsByTagName('status')[0].childNodes[0].nodeValue;
                        //alert("ALERT2");
						addRowToList(theticketnum, thetime, thename, theemail, thesubject, thedescription, thetech, thestatus);
						//alert("ALERT3");
					}
					//alert("ALERT3 !!");
					showCDTable(by_ticketnum);
					window.status="Table updated at " + (new Date()).toString();
				}
				else{
					window.status="";
				}
			}
			else{   alert('Problem with request'); }
		}
    }

    // Create a CD
    function CD(ticketnum, time, name, email, subject, description, tech, status){
		this.ticketnum = ticketnum;
		this.time = time;
		this.name = name;
		this.email = email;
		this.subject = subject;
        this.description = description;
        this.tech = tech;
        this.status = status;
    }

    // Compare CDs by name
    function by_ticketnum(a, b){
		if (a.tickernum < b.ticketnum) return 1;
			else if (a.ticketnum == b.ticketnum) return 0;
			else return -1;
	}

    // Add a new row to the CD array.  If the id is null it means that
    // the CD was a write-in (not sent back from the server).  We then give
    // it the appropriate id (assuming the DB uses auto-increment).  As
    // stated above, it would be better if we received this new CD from the
    // server just so we would make sure to get the correct id.
    function addRowToList(ticketnum, time, name, email, subject, description, tech, status) {
		var currCD;
		if (ticketnum == null)
			currCD = new CD(CDcount+1, time, name, email, subject, description, tech, status);
		else
			currCD = new CD(ticketnum, time, name, email, subject, description, tech, status);
		theCDs[CDcount] = currCD;
		CDcount++;
		//alert("ALERT2 ! " +CDcount);
    }

    // Sort the CDs then generate a new table, replacing the old one with
    // the new.  Note how the various attributes are generated using DOM.
    function showCDTable(sortby){
		theCDs.sort(sortby);
		var T = document.getElementById("theTable");
		var tParent = T.parentNode;

		var newT = document.createElement('table');
		newT.setAttribute('id', 'theTable');
		newT.border = 1;
		newT.className = 'thetable';
		var cap = newT.createCaption();
		var contents = document.createTextNode('Tickets Table');
		cap.appendChild(contents);
		cap.className = "title";
		var hrow = newT.insertRow(0);
		hrow.align = 'center';

		var currCell = hrow.insertCell(0);
		contents = document.createTextNode('Ticketnum');
		currCell.appendChild(contents);

		var currCell = hrow.insertCell(1);
		contents = document.createTextNode('Received');
		currCell.appendChild(contents);

		var currCell = hrow.insertCell(2);
		contents = document.createTextNode('Name');
		currCell.appendChild(contents);

		var currCell = hrow.insertCell(3);
		contents = document.createTextNode('Email');
		currCell.appendChild(contents);

		var currCell = hrow.insertCell(4);
		contents = document.createTextNode('Subject');
		currCell.appendChild(contents);

		var currCell = hrow.insertCell(5);
		contents = document.createTextNode('Description');
		currCell.appendChild(contents);

		var currCell = hrow.insertCell(6);
		contents = document.createTextNode('Tech');
		currCell.appendChild(contents);

		var currCell = hrow.insertCell(7);
		contents = document.createTextNode('Status');
		currCell.appendChild(contents);

		// New table is set up, but without CD rows.  Replace old with new
		// then add the CDs
		tParent.replaceChild(newT, T);

		for (var i = 0; i < CDcount; i++){
				addRow(theCDs[i].ticketnum, theCDs[i].time, theCDs[i].name, theCDs[i].email,
				theCDs[i].subject, theCDs[i].description, theCDs[i].tech, theCDs[i].status);
		}
		addSort();
    }
 
    // When adding a row we now have to distinguish between the row index
    // and the CD id.  The index is used for selection and for highlighting,
    // while the id is used to update the count via AJAX.
    function addRow(ticketnum, time, name, email, subject, description, tech, status){
		var T = document.getElementById("theTable");
		var len = T.rows.length;
		//alert("adding " + ticketnum + " " + time + " " + name + " " + email + " at index " + len);
		var R = T.insertRow(len); 
		R.align = 'center';       
		R.className = 'regular';

		var C = R.insertCell(0);  
		var txt = document.createTextNode(ticketnum);
		C.appendChild(txt);
		C = R.insertCell(1);
		txt = document.createTextNode(time);
		C.appendChild(txt);
		C = R.insertCell(2);
		txt = document.createTextNode(name);
		C.appendChild(txt);
		C = R.insertCell(3);
		txt = document.createTextNode(email);
		C.appendChild(txt);
		C = R.insertCell(4);
		txt = document.createTextNode(subject);
		C.appendChild(txt);
		C = R.insertCell(5);
		txt = document.createTextNode(description);
		C.appendChild(txt);
		C = R.insertCell(6);
		txt = document.createTextNode(tech);
		C.appendChild(txt);
		C = R.insertCell(7);
		txt = document.createTextNode(status);
		C.appendChild(txt);
    }
    function showSelected(){
		var rowind = arguments[0];
		var theTable = document.getElementById("theTable");
		var theRow = theTable.rows[rowind];
		theRow.className = "highlight";
    }

    function makeRegular() {
          var rowind = arguments[0];
          var theTable = document.getElementById("theTable");
	  var theRow = theTable.rows[rowind];
          theRow.className = "regular";
    }

    // We now refresh the page immediately to load the initial CDs
    function refreshPage(){
        var httpRequest;
 
        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                }
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {}
            }
        }
        if (!httpRequest) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
 
        var type = 4; 
        var rows = document.getElementById("theTable").rows.length-1;;
        // Special case:  Before initial table is displayed ther are
        // no rows.  We don't want the submitted variable to be negative,
        // however.
        if (rows == -1)
	    rows = 0;
        var data = 'type=' + type + '&rows=' + rows;

        httpRequest.open('POST', 'tabulate.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        httpRequest.onreadystatechange = function() { updateRows(httpRequest); } ;
        httpRequest.send(data);
        //t = setTimeout("refreshPage()", 60000);
		document.getElementById("button3").style.visibility = 'hidden';
    }
	
	function hidesubmit(){
		document.getElementById("submit").style.visibility = 'hidden';
	}
	function showsubmit(){
		if (document.getElementById("submit").style.visibility == 'visible'){
			hidesubmit();
		}
		else{
			document.getElementById("submit").style.visibility = 'visible';
			if(document.getElementById("password").style.visibility != 'visible')
				document.getElementById("password").style.visibility = 'hidden';
		}
	}
	function hidepassword(){
		document.getElementById("password").style.visibility = 'hidden';
	}
	function showpassword(){
		if (document.getElementById("password").style.visibility == 'visible'){
			hidepassword();
		}
		else{
			document.getElementById("password").style.visibility = 'visible';
		}
	}
	function processPassword(){
        var password = document.pollForm.pass.value;
        var ok = true;
        if (password == "" ){
            alert("Please fill out the password field");
            ok = false;
        }
        if (ok){
            document.pollForm.pass.value = "";
			hidepassword();
            processData(5, password);
			alert("You have successfully changed your password");
        }
	}

</script>
</head>
<body onload = "hidesubmit()">
<center>
<form name = "pollForm">
<table id = "theTable" border = "1" class="thetable">
</table>
<br /><center>
<h2>
	<table id="theform" border="0">
	<tr>
	<td><input type="button" id="button1" name="choice1" value="Submit New Ticket" onclick = 'showsubmit()'></input>
	<td><input type="button" id="button3" name="choice3" value="View My Tickets" onclick = 'refreshPage()'></input>
	<td><input type="button" id="button6" name="choice6" value="Change Password" onclick = 'showpassword()'></input>
	<td><a href="http://localhost:8080/1520p3/clear.php">Log Out</a> <br /></input>
		
	</tr> 
	<table id="submit" border="0">
	<tr> <td>Subject of Problem: <br/><input type = "text" name = "subject" size = "50"/> </td>
		<td><p id="password"> New Password: <br/><input type = "password" name = "pass" size = "30"/>
				<input type = "button" value = "Submit" onclick = 'processPassword()'> </p></tr>
		<td>Description of Problem:	<br/><textarea name="description" rows="5" cols="40"></textarea></tr>
		<td><input type = "button" value = "Submit" onclick = 'processWritein()'> </tr>
</h2>
</form>
</center>
<script type="text/javascript">
    var theCDs = new Array(), CDcount = 0, t;
</script>
</body>
</html>
