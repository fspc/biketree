<?php session_start();

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


//include('odb.php');


function getmonth($m=0) {
  return (($m==0 ) ? date("F") : date("F", mktime(0,0,0,$m)));
}

$month = getmonth($_POST[month]);



						// MAKE SURE THEY'RE NOT ALREADY HERE!


//$in = mktime($_POST[hour], $_POST[minute], 0, $_POST[month], $_POST[day], $_POST[year]);
//$tdin = date('Y-m-d H:i:s');
//$activity = $_POST[activity];
$sec->signinMember($_POST[userID], mktime($_POST[hour], $_POST[minute], 0, $_POST[month], $_POST[day], $_POST[year]), $_POST[activity]);
/*$isinresult = mysql_query("SELECT userID FROM visits WHERE endout IS NULL");

if (!$isinresult) { die("Query to show fields from table failed"); }
	
	while ($isinrow = mysql_fetch_array($isinresult)) {
	if ($_POST[userID] == "$isinrow[userID]"){ die("<b>Bike Error!! User is already signed in...</b>"); }
	}




						// MAKE SURE THEY'VE PAID THEIR MEMBERSHIP (IF REQUIRED BY CONFIG FILE) 
if(!$sec->checkMembership($_POST[userID]) && $cfg_reqmembership == 1){ echo "Membership not paid (or expired $expires)!<br /><a href=\"../home.php\">Go Home --&gt;</a>"; die(''); } 

						// Have you been a naughty schoolchild and not signed your waiver?  PUNISH!
if(!$sec->checkWaiver($_POST[userID])){ echo "Waiver not signed. Sign waiver, or no shop access you naughty boy!<br /><a href=\"../home.php\">Go Home --&gt;</a>"; die(''); } 



						// ADD IT TO THE VISITS DATABASE

$in = mktime($_POST[hour], $_POST[minute], 0, $_POST[month], $_POST[day], $_POST[year]);
$tdin = date('Y-m-d H:i:s');
$activity = $_POST[activity];

if (isset($_POST[userID])){
$query = "INSERT INTO `visits` (`userID` ,`intime` ,`activity`) VALUES ('$_POST[userID]', '$tdin', '$activity')";
// echo "IT FJDSFDSA $query";   
     mysql_query($query);


}*/ 




// sending query
if ($_POST[userID] != ""){
// echo "userID is set: $_POST[userID]";
$result = mysql_query("SELECT * FROM customers WHERE id='$_POST[userID]'");
if (!$result) {
    die("Query to show fields from table failed");
}
$fields_num = mysql_num_fields($result);
$field = mysql_fetch_array($result);

foreach($field as $key=>$value) { $$key = stripslashes($value); }

}

header( 'Location: /home.php' ) ;

?>

