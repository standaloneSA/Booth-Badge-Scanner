<?php

$dbUser = "username"; 
$dbPass = "password"; 
$dbHost = "localhost"; 

print ' 
<html>
<head>
<title>Booth Badge Scanner</title>

<style type="text/css"> 
div.okmessage
{
color:green;
text-align:center;
font-weight:bold;
}

div.errmessage
{
color:red;
text-align:center; 
font-weight:bold;
}

div.banner
{
font-size:20px;
font-weight:bold;
text-align:center;
}
</style>

<script src="shortcut.js"></script>


</head>
<body>
<div class="banner">
	Booth Badge Scanner
</div>


<div id="body">
	'; 

if ( $_POST ) { 
	storePost($_POST['badgeText']); 
}

printForm(); 

//debug(); 

function printForm() { 
// quick and dirty text field that allows us to scan badges
	print '
		<center>
		<form name="badgeInfo" id="badgeInfo" method="POST" action=""> 
			<input type="text" name="badgeText" id="badgeText" size=70 value="">
			<br>
			<input type="submit">
		</form>
	'; 
} // end printForm() 

function storePost($strBadge) { 

	if ( ! $strBadge ) { 
		print "<div class='errmsg'>Error: blank</div>"; 
		return; 
	}

	global $dbHost, $dbUser, $dbPass; 

	$arrFields = explode("^", $strBadge); 

	for ( $element = 0; $element < count($arrFields); $element++ ) { 
		if ( $arrFields[$element] == "NULL" ) { 
			// the scanners default to the text NULL for blank fields
			$arrFields[$element] = ""; 
		}
	}

	$myDB = mysql_connect($dbHost, $dbUser, $dbPass) or 
		die ("Error connecting to Database. Please make sure it's started. Error: " . mysql_error() ); 
	
	$query = "INSERT INTO lopsaBadges.badges ( 
				rawText, badgeID, fName, 
				lName, email, company, 
				address1, address2, address3, 
				city, state, zipcode, 
				country, phone
				) VALUES ( 
				'" . mysql_real_escape_string($strBadge) . "', 
				'" . mysql_real_escape_string($arrFields[0]) . "',
				'" . mysql_real_escape_string($arrFields[1]) . "',
				'" . mysql_real_escape_string($arrFields[2]) . "',
				'" . mysql_real_escape_string($arrFields[3]) . "',
				'" . mysql_real_escape_string($arrFields[4]) . "',
				'" . mysql_real_escape_string($arrFields[5]) . "',
				'" . mysql_real_escape_string($arrFields[6]) . "',
				'" . mysql_real_escape_string($arrFields[7]) . "',
				'" . mysql_real_escape_string($arrFields[8]) . "',
				'" . mysql_real_escape_string($arrFields[9]) . "',
				'" . mysql_real_escape_string($arrFields[10]) . "',
				'" . mysql_real_escape_string($arrFields[11]) . "',
				'" . mysql_real_escape_string($arrFields[12]) . "'
				)"; 

		mysql_query($query, $myDB) or
			die("Error: Unable to submit data: " . mysql_error() ); 

	if ( mysql_insert_id($myDB) ) { 
		print "<div class='okmessage'>OK</div>"; 
	}

	mysql_close($myDB); 


} // end storePost()

function debug() { 
	print "<br><pre>"; 
	print_r($_POST); 
	print "</pre><br>";
} // end debug()

?>
</div>
<script>

function init() { 
shortcut.add("Ctrl+Shift+j",function() {
	var myVar; 
});
document.getElementById("badgeText").focus(); 
}

if (document.addEventListener)
  document.addEventListener("DOMContentLoaded", init, false)
</script>

</body>
</html>
