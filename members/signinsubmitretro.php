<html>
<head>
<link rel="stylesheet" type="text/css" href="../allstyles.css" />

</head>
<body>



<?php

include ("../settings.php");
include ("../language/$cfg_language");
include ("../classes/db_functions.php");
include ("../classes/security_functions.php");
include ("../classes/display.php");

$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);
$display=new display($dbf->conn,$cfg_theme,$cfg_currency_symbol,$lang);

global $cfg_membershipID;

if(!$sec->isLoggedIn())
{
        header ("location: ../login.php");
        exit();
}



function getmonth($m=0) {
return (($m==0 ) ? date("F") : date("F", mktime(0,0,0,$m)));
}
$month = getmonth($_POST[month]);

						// STICKUPDATE IT TO THE VISITS DATABASE

$in = mktime($_POST[hour], $_POST[minute], 0, $_POST[month], $_POST[day], $_POST[year]);
$out = mktime($_POST[hourout], $_POST[minuteout], 0, $_POST[monthout], $_POST[dayout], $_POST[yearout]);
$tdin = date('Y-m-d H:i:s', $in);
if($_POST[ignoreout] != "on"){ $tdout = date('Y-m-d H:i:s', $out); $outquery = "endout='$tdout',"; }
$activity = $_POST[activity];

if (isset($_POST[userID])){
$query = "UPDATE visits SET intime='$tdin', $outquery activity='$activity' WHERE visitID=$_POST[visitID]";

						//REPORT BACK TO USER THAT ALL IS OK!
echo "<table class=text><tr><td class=\"high40\"><h2>Sign in/out retroactively</h2></td></tr><tr><td><h3>Success</h3><br /><center>It has been made so. </td></tr><tr><td class=\"submit\"><br /><a href=\"../home.php\">Continue</a><br /></center></td></tr></table>";   
     mysql_query($query);
echo mysql_error();

} 
?>
</body>
