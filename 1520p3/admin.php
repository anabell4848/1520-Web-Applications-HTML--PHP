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
var_dump($_SESSION);

if ($_SESSION['usertype']!="admin")
	die;

?>
<!DOCTYPE html>
<html>
<head>
<title>Tickets table</title>
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
        if (type == 1){
            var choice = arguments[1];
            var id = arguments[2];
            data = 'type=' + type + '&' + 'select=' + id;  
            //alert(data);
        }
        else if(type==2)  {// type == 2
       
            var rows = arguments[1];
            var title = arguments[2];
            var artist = arguments[3];
            var rows = arguments[1];
            var title = arguments[2];
            var artist = arguments[3];
            data = 'type=' + type + '&rows=' + rows + '&title=' + title + '&artist=' + artist; 
            //alert(data);
        }
		else if (type==5){	//type=5, password change
			var password = arguments[1];
			data = 'type=' + type + '&password=' +password; 
		}
		else if (type==6){	//type=6, filter
			var filter = arguments[1];
			data = 'type=' + type + '&filter=' +filter; 
			//alert(data);
		}
		else{
			var ticketnum = arguments[1];
			data = 'type=' + type + '&ticketnum=' +ticketnum; 
		}
		
        httpRequest.open('POST', 'tabulate.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        if (type == 1)
            httpRequest.onreadystatechange = function() { showResults(httpRequest, choice); };
        else if (type==2)
            httpRequest.onreadystatechange = function() { addNewRows(httpRequest, title, artist); } ;
        else if (type==5)
			httpRequest.onreadystatechange = function() { temp(httpRequest, password); } ;
		else if (type==6)
			httpRequest.onreadystatechange = function() { addNewRows(httpRequest, filter); } ;
		else
			httpRequest.onreadystatechange = function() { temp(httpRequest, ticketnum); } ;
		httpRequest.send(data);
    }
	function temp(httpRequest, password){
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
        var title = document.pollForm.title.value;
        var artist = document.pollForm.artist.value;
        var ok = true;
        if (title == ""){
            alert("Please enter a title for your write-in vote");
            document.pollForm.title.focus();
            ok = false;
        }
        if (artist == "") {
            alert("Please enter an artist for your write-in vote");
            document.pollForm.artist.focus();
            ok = false;
        }
        if (ok){
            document.pollForm.artist.value = "";
            document.pollForm.title.value = "";
            processData(2, numrows, title, artist);
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

    function addNewRows(httpRequest, filter){
        if (httpRequest.readyState == 4)  {
			if (httpRequest.status == 200){
				// Get the root of the XML document
				var root = httpRequest.responseXML.documentElement;
				//alert(root + "root alert");
				var rettype = root.getElementsByTagName('Type')[0].childNodes[0].nodeValue;
				//alert(rettype + " type alert");
				
				theCDs = new Array();
				theCDs.length = 0;
				CDcount=0;
				var newRows = root.getElementsByTagName('CD');
				for (var i = 0; i < newRows.length; i++){	
					var theRow = newRows[i];
					var theticketnum = theRow.getElementsByTagName('ticketnum')[0].childNodes[0].nodeValue;
					var thetime = theRow.getElementsByTagName('time')[0].childNodes[0].nodeValue;
					var thename = theRow.getElementsByTagName('name')[0].childNodes[0].nodeValue;
					var theemail = theRow.getElementsByTagName('email')[0].childNodes[0].nodeValue;
					var thesubject = theRow.getElementsByTagName('subject')[0].childNodes[0].nodeValue;
					var thedescription = theRow.getElementsByTagName('description')[0].childNodes[0].nodeValue;
					var thetech = theRow.getElementsByTagName('tech')[0].childNodes[0].nodeValue;
					var thestatus = theRow.getElementsByTagName('status')[0].childNodes[0].nodeValue;
					//alert(theCDs.length+" ADD TO LIST NOW");
					addRowToList(theticketnum, thetime, thename, theemail, thesubject, thedescription, thetech, thestatus);
					//alert("SHOW TICKET1 "+ CDcount);
				}
				//alert("SHOW TICKET2 "+ CDcount );
				// Once all CDs have been added, sort the list and regenerate
				// the table.
				showCDTable(by_ticketnum);
			}
			else{   alert('Problem with request'); }
		}
    }


    function updateRows(httpRequest){
        if (httpRequest.readyState == 4){
			if (httpRequest.status == 200){
				var root = httpRequest.responseXML.documentElement;
				//alert(root+"ALERT1");
				var rettype = root.getElementsByTagName('Type')[0].childNodes[0].nodeValue;
				//alert(rettype+"ALERT2");
				if (rettype == "Update"){
					var newRows = root.getElementsByTagName('CD');
					//alert(newRows.length+"ALERT2");
					for (var i = 0; i < newRows.length; i++){
						//alert(i+" "+newRows.length+"ALERT3.1");
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
						//alert(i+" "+newRows.length+"ALERT3.2");
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

    // Compare CDs by ticketnum
    function by_ticketnum(a, b){
		if (a.ticketnum < b.ticketnum) return -1;
			else if (a.ticketnum == b.ticketnum) return 0;
			else return 1;
    }
	// Compare CDs by time
    function by_time(a, b){
		if (a.time < b.time) return -1;
			else if (a.time == b.time) return 0;
			else return 1;
    }
	// Compare CDs by name
    function by_name(a, b){
		if (a.name < b.name) return -1;
			else if (a.name == b.name) return 0;
			else return 1;
    }
	// Compare CDs by email
    function by_email(a, b){
		if (a.email < b.email) return -1;
			else if (a.email == b.email) return 0;
			else return 1;
    }
	// Compare CDs by subject
    function by_subject(a, b){
		if (a.subject < b.subject) return -1;
			else if (a.subject == b.subject) return 0;
			else return 1;
    }
	// Compare CDs by description
    function by_description(a, b){
		if (a.description < b.description) return -1;
			else if (a.description == b.description) return 0;
			else return 1;
    }


    function addRowToList(ticketnum, time, name, email, subject, description, tech, status) {
		var currCD;
		currCD = new CD(ticketnum, time, name, email, subject, description, tech, status);
		
		//alert(currCD + CDcount+" ABOUT TO ADD TO ARRAY");
		theCDs[CDcount] = currCD;
		//alert(currCD + CDcount+" AT ADD ");
		CDcount++;
		//alert(CDcount +" AFTER THE ADD");
    }

    // Sort, then generate a new table
    function showCDTable(sortby){
		//alert("SHOW TICKETTTTTTTTTT "+ CDcount );
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
			//alert("SHOW TICKET "+ CDcount + "  "+i);
			addRow(theCDs[i].ticketnum, theCDs[i].time, theCDs[i].name, theCDs[i].email,
			theCDs[i].subject, theCDs[i].description, theCDs[i].tech, theCDs[i].status);
		}
		addSort();
    }
 
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

		C = R.insertCell(8);     
		var rb = document.createElement('input');
		rb.setAttribute('type', 'radio');
		rb.setAttribute('name', 'option');
		
		rb.setAttribute('value', ticketnum);
		rb.onclick = function() { getselect(ticketnum); };
		rb.onmouseover = function() { showSelected(len); };
		rb.onmouseout = function() { makeRegular(len); };
		C.appendChild(rb);
    }
	function addSort(){
		var T = document.getElementById("theTable");
		var len = T.rows.length;
		var R = T.insertRow(len); 
		R.align = 'center';       
		R.className = 'regular';

		var C = R.insertCell(0);  
		var rb = document.createElement('input');
		rb.setAttribute('type', 'radio');
		rb.setAttribute('name', 'options');
		rb.setAttribute('id', 'ticketnum');
		C.appendChild(rb);
		C = R.insertCell(1);
		rb = document.createElement('input');
		rb.setAttribute('type', 'radio');
		rb.setAttribute('name', 'options');
		rb.setAttribute('id', 'received');
		C.appendChild(rb);
		C = R.insertCell(2);
		rb = document.createElement('input');
		rb.setAttribute('type', 'radio');
		rb.setAttribute('name', 'options');
		rb.setAttribute('id', 'sendername');
		C.appendChild(rb);
		C = R.insertCell(3);
		rb = document.createElement('input');
		rb.setAttribute('type', 'radio');
		rb.setAttribute('name', 'options');
		rb.setAttribute('id', 'senderemail');
		C.appendChild(rb);
		C = R.insertCell(4);
		rb = document.createElement('input');
		rb.setAttribute('type', 'radio');
		rb.setAttribute('name', 'options');
		rb.setAttribute('id', 'subject');
		C.appendChild(rb);
		C = R.insertCell(5);
		rb = document.createElement('input');
		rb.setAttribute('type', 'radio');
		rb.setAttribute('name', 'options');
		rb.setAttribute('id', 'description');
		C.appendChild(rb);
		C = R.insertCell(6);
		txt = document.createTextNode("");
		C.appendChild(txt);
		C = R.insertCell(7);
		txt = document.createTextNode("");
		C.appendChild(txt);
		C = R.insertCell(8);
		txt = document.createTextNode("");
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
 
        var type = 3; 
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
		showButtons();
        //t = setTimeout("refreshPage()", 60000);
    }
	
	function hideButtons(){		//when show selected tickets has been clicked
		//alert(theselection);
		processData(6,theselection);
		document.getElementById("first").style.visibility = 'hidden';
		document.getElementById("second").style.visibility = 'visible';
	}
	function getselect(ticketnum){
		theselection=ticketnum;
		//alert(theselection);
	}
	function showButtons(){		//when show selected tickets has been clicked
		var filter = "all";
		processData(6, filter);
		document.getElementById("first").style.visibility = 'visible';
		document.getElementById("second").style.visibility = 'hidden';
		//see original view
	}
	function toggle1(){
		var elem = document.getElementById("button1");
		if (elem.value=="View Open Tickets"){ 
			elem.value = "View All Tickets";
			var filter="open";
		}
		else {
			elem.value = "View Open Tickets";
			var filter="all";
		}
		processData(6, filter);
	}
	function toggle4(){
		var elem = document.getElementById("button4");
		if (elem.value=="View My Tickets") {
			elem.value = "View Open Tickets";
			var filter="my";
		}
		else {
			elem.value = "View My Tickets";
			var filter="open";
		}
		processData(6, filter);
	}
	function toggle7(){
		var elem = document.getElementById("button7");
		if (elem.value=="View Unassigned Tickets") {
			elem.value = "View Open Tickets";
			var filter="unassigned";
		}
		else {
			elem.value = "View Unassigned Tickets";
			var filter="open";
		}
		processData(6, filter);
	}
	function sortData(){
		if (document.getElementById("ticketnum").checked == true) {
			//alert("You have selected Option 1");
			showCDTable(by_ticketnum);
		}
		else if (document.getElementById("received").checked == true) {
			//alert("You have selected Option 2");
			showCDTable(by_time);
		}
		else if (document.getElementById("sendername").checked == true) {
			//alert("You have selected Option 3");
			showCDTable(by_name);
		}
		else if (document.getElementById("senderemail").checked == true) {
			//alert("You have selected Option 4");
			showCDTable(by_email);
		}
		else if (document.getElementById("subject").checked == true) {
			//alert("You have selected Option 5");
			showCDTable(by_subject);
		}
		else if (document.getElementById("description").checked == true) {
			//alert("You have selected Option 6");
			showCDTable(by_description);
		}
		else {
			alert("Please select a sort method");
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
            processData(5, password);
			alert("You have successfully changed your password");
        }
	}
	function openclose(){
		processData(7, theselection);
	}
</script>
</head>
<body onload = "refreshPage()" >
<center>
<form name = "pollForm">
<table id = "theTable" border = "1" class="thetable">
</table>
<br /><center>
<h2>
	<table id = "first" border="0" id="table">
	<tr>
	<td><input type="button" id="button1" name="choice1" value="View Open Tickets" onclick = 'toggle1()'></input>
	<td><input type="button" id="button2" name="choice2" value="Sort" onclick = 'sortData()'></input>
	<td><input type="button" id="button3" name="choice3" value="View Selected Ticket" onclick = 'hideButtons()'></input>
	<td><input type="button" id="button7" name="choice7" value="View Unassigned Tickets" onclick = 'toggle7()'></input>
	</tr>
	<tr>
	<td><input type="button" id="button4" name="choice4" value="View My Tickets" onclick = 'toggle4()'></input>
	<td><input type = "button" value = "Change Password" onclick = 'processPassword()'> </input>
	<td>New Password: <br/><input type = "password" name = "pass" size = "30"/></input> 

	<td><a href="http://localhost:8080/1520p3/clear.php">Log Out</a> <br /></input>
	</tr>	
	<table id = "second" border="0" id="table" >
	<tr>
	<td><input type="button" id="button8" name="closeopen" value="Close/reopen the ticket" onclick = 'openclose()'></input>
	<td><input type="button" id="button9" name="assignself" value="Assign self to ticket" onclick = 'processWritein()'></input>
	<td><input type="button" id="button10" name="removeself" value="Remove self from ticket" onclick = 'processWritein()'></input>
	<td><input type="button" id="button11" name="emailsubmitter" value="Email the submitter" onclick = 'processWritein()'></input>
	</tr><tr>
	<td><input type="button" id="button12" name="deleteticket" value="Delete the ticket from DB" onclick = 'processWritein()'></input>
	<td><input type="button" id="button13" name="findall" value="Find all tickets from submitter" onclick = 'processWritein()'></input>
	<td><input type="button" id="button14" name="adminpage" value="Back to main administrator page" onclick = 'showButtons()'></input>
	<td><input type="button" id="button15" name="findsimilar" value="Find all similar tickets" onclick = 'processWritein()'></input>
	<td><input type="text" id="button16" name="numofmatches" size = "3" maxlength = "3">How many words must match?</input>
	</tr>
	</form>
</h2>
</center>
<script type="text/javascript">
    var theCDs = new Array(), CDcount = 0, theselection;
</script>
</body>
</html>
