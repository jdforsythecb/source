<!-- always start the content pages by loading any page-specific stylesheets -->
<link rel="stylesheet" href="/views/settings.css">

<?php

	// debug
	$churchID = 1;

	// we will need database read access in this view
	include('/app/db.php');
	//$db = new MEODB();

	// read admin settings from database
	//$db->select("* FROM " . DBTables::Churches . " WHERE cid='$churchID' LIMIT 1");

?>

<?php 
/*
<!-- then comes the actual content html -->
<h1>Welcome to the settings page</h1>

<!-- admin settings -->
Church information:

Church Name

Pastor Name

Church Address

Church Phone

Church Fax

Church E-mail Address (for receipts):

Church Contact E-mail Address:

Church Founding Date:

Church Uses Offering Envelopes:



Setup Church Funds



<!-- user settings -->

Show the following on my dashboard

Send me e-mail receipts

Days before transaction to send me an email notice

 */
 
// select query


$db = new MEODB(DBPrivileges::READ);

$db->safeSelect("* FROM `churches`");

if($db->result->num_rows > 0) {
	
	echo "<table>";
	echo "<tr><td>cid</td><td>Name</td><td>Address1</td><td>Address2</td><td>City</td><td>State</td><td>Zip</td><td>Country</td><td>contact email</td><td>receipt email</td><td>founding date</td><td>uses envelopes?</td><td>ach fee</td><td>cc fee</td><td>cc pct</td></tr>";
		
	while($row = $db->result->fetch_assoc()) {		
		echo "<tr>";
		echo "<td>" . $row['cid'] . "</td>";
		echo "<td>" . $row['name'] . "</td>";
		echo "<td>" . $row['address1'] . "</td>";
		echo "<td>" . $row['address2'] . "</td>";
		echo "<td>" . $row['city'] . "</td>";
		echo "<td>" . $row['state'] . "</td>";
		echo "<td>" . $row['zip'] . "</td>";
		echo "<td>" . $row['country'] . "</td>";
		echo "<td>" . $row['contactemail'] . "</td>";
		echo "<td>" . $row['receiptemail'] . "</td>";
		echo "<td>" . $row['foundingdate'] . "</td>";
		echo "<td>" . $row['usesenvelopes'] . "</td>";
		echo "<td>" . $row['achfee'] . "</td>";
		echo "<td>" . $row['ccfee'] . "</td>";
		echo "<td>" . $row['ccpct'] . "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
	
}
$db->closeConnection();

?>
