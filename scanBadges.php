<?php

require_once("config.php"); 

print ' 
<html>
<head>
<title>' . $orgName . ' Booth Badge Scanner</title>

<style type="text/css"> 
div.okmessage
{
color:green;
text-align:center;
font-weight:bold;
}

div.errmsg
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
	' . $orgName . ' Booth Badge Scanner
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
			<input type="text" name="badgeText" id="badgeText" autocomplete="off" size=70 value="">
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

	global $dbHost, $dbUser, $dbPass, $arrBadgeFields, $dbTable; 

	$arrFields = explode("^", $strBadge); 

	$myDB = mysql_connect($dbHost, $dbUser, $dbPass) or 
		die ("Error connecting to Database. Please make sure it's started. Error: " . mysql_error() ); 


	for ( $element = 0; $element < count($arrFields); $element++ ) { 
		if ( $arrFields[$element] == "NULL" ) { 
			// the scanners default to the text NULL for blank fields
			$arrFields[$element] = ""; 
		}
	}

	
	$query = "INSERT INTO " . $dbTable . ".badges (";
	foreach ( $arrBadgeFields as $curField ) { 
		$query .= " $curField,"; 
	} 
	$query = rtrim($query,","); 

	$query .= ") VALUES (";

	// First column we insert is always the entire badge contents, so we can
	// recover data if something goes south
	$query .= "'" . mysql_real_escape_string($strBadge) . "',"; 
	foreach ( $arrFields as $curField ) { 
		$query .= "'" . mysql_real_escape_string($curField) . "',"; 
	}
	$query = rtrim($query,","); 
	$query .= ")"; 


	// If the number of columns we SHOULD have doesn't match the 
	// number of columns we DO have, then we will just store 
	// the raw contents of the badge and print an error message
	if ( count($arrFields) != count($arrBadgeFields) ) { 
		print "<div class='errmsg'>Error: Number of scanned fields does not match database.<br>Inserting only the raw scanned code</div>"; 
		$query = "INSERT INTO " . $dbTable . ".badges (" . $arrBadgeFields[0] . ") VALUES ('" . mysql_real_escape_string($strBadge) . "')"; 
	}
	mysql_query($query, $myDB) or
		die("Error: Unable to submit data: " . mysql_error() ); 

	if ( mysql_insert_id($myDB) ) { 
		print "<div class='okmessage'>OK</div>"; 
	} else { 
		print "<div class='errmsg'>DB Error: Failed somehow at insterting data, but didn't received a MySQL error.<br>Please check logs</div>"; 
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
