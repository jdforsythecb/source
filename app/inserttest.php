<?php
include('global-logic.php');

// we will need database write access in this view
include('db.php');
$db = new MEODB(DBPrivileges::WRITE);

// create the sql query with ? placeholders
$query = "INSERT INTO `churches` VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
// (cid, name, address1, address2, city, state, zip, country, contactemail,
//  receiptemail. foundingdate, usesenvelopes, achfee, ccfee, ccpct)

// set the parameterized query
$db->setParameterizedQuery($query);

// add the parameter data types and values
$db->addParamValue(DBParamsDataTypes::INTEGER, 2);
$db->addParamValue(DBParamsDataTypes::STRING, "St Pauls Catholic Church");
$db->addParamValue(DBParamsDataTypes::STRING, "123 State Street");
$db->addParamValue(DBParamsDataTypes::STRING, "");
$db->addParamValue(DBParamsDataTypes::STRING, "salem");
$db->addParamValue(DBParamsDataTypes::STRING, "OH");
$db->addParamValue(DBParamsDataTypes::STRING, "44460");
$db->addParamValue(DBParamsDataTypes::STRING, "United States");
$db->addParamValue(DBParamsDataTypes::STRING, "subterfuge27@gmail.com");
$db->addParamValue(DBParamsDataTypes::STRING, "subterfuge28@gmail.com");
$db->addParamValue(DBParamsDataTypes::STRING, "1981-01-31");
$db->addParamValue(DBParamsDataTypes::INTEGER, 1);
$db->addParamValue(DBParamsDataTypes::DOUBLE, 0.30);
$db->addParamValue(DBParamsDataTypes::DOUBLE, 0.30);
$db->addParamValue(DBParamsDataTypes::DOUBLE, 0.030);

// run the insert() query - returns the number of rows affected
$affectedRows = $db->insert();
printf("%d Row(s) inserted.\n", $affectedRows);

// close statement and connection
$db->closeConnection();
















?>