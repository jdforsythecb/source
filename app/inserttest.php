<?php
include('global-logic.php');

// we will need database write access in this view
include('db.php');
$db = new MEODB(DBPrivileges::WRITE);

$query = "INSERT INTO `churches` VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
// (cid, name, address1, address2, city, state, zip, country, contactemail, receiptemail. foundingdate, usesenvelopes, achfee, ccfee, ccpct)

$db->setParameterizedQuery($query);

$db->setParameterValues(DBParamsDataTypes::INTEGER,	1,
						DBParamsDataTypes::STRING, "St. Paul's Catholic Church",
						DBParamsDataTypes::STRING, "123 State Street",
						DBParamsDataTypes::STRING, "",
						DBParamsDataTypes::STRING, "Salem",
						DBParamsDataTypes::STRING, "OH",
						DBParamsDataTypes::STRING, "44460",
						DBParamsDataTypes::STRING, "United States",
						DBParamsDataTypes::STRING, "subterfuge27@gmail.com",
						DBParamsDataTypes::STRING, "subterfuge28@gmail.com",
						DBParamsDataTypes::STRING, "1981-01-31",
						DBParamsDataTypes::INTEGER, 1,
						DBParamsDataTypes::STRING, "0.30",
						DBParamsDataTypes::STRING, "0.30",
						DBParamsDataTypes::STRING, "0.030");
						
/* execute prepared statement */
$db->statement->execute();

$affectedRows = $db->insert();

printf("%d Row inserted.\n", $affectedRows);

// close statement and connection
$db->closeConnection();
















?>