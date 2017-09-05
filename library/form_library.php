<?php session_start(); ?>

<html>
<head>

</head>

<body>
<?php

include ("../settings.php");
include ("../classes/db_functions.php");
include("../language/$cfg_language");
include ("../classes/security_functions.php");
include ("../classes/form.php");
include ("../classes/display.php");


$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);
$display=new display($dbf->conn,$cfg_theme,$cfg_currency_symbol,$lang);

if(!$sec->isLoggedIn())
{
		header ("location: ../login.php");
		exit();
}
//set default values, these will change if $action==update.
$userID='';
$loanID='';
$deposittaken='';
$loandate='';
$returndate='';
$notes='';
$latefeespaid='';
$paid='';
$id=-1;


//echo "post is $_POST[bikeID]and id is $id";
//Destroy the world if they didn't put a valid bike number in. Then apologize.
$bikecheck = mysql_query("SELECT * FROM bikes WHERE id='$_POST[bikeID]' LIMIT 1",$dbf->conn);
echo mysql_error();
$bikeexists = mysql_fetch_array($bikecheck);
$back = "<br /><br /><a href=\"index.php\">[Go Baaaaaack]</a>";
if($bikeexists['id'] == ""){ echo "<br />Bike Doesn't exist. Divide by zero. Did you put a bike number in the box?  If you <b>did</b> put a number in, go back and try typing it again.$back"; die(); }
if($bikeexists['bikestatus'] == "repair"){ echo "<br />This is <b>a personal bike in for repair!</b> Take it from them and make a note! $back"; die(); }
if($bikeexists['bikestatus'] != "library"){ echo "<br />This is not a library bike. It is marked as <b>$bikeexists[bikestatus]</b>. Take it from them and tell the IT working group $back"; die(); }
if($bikeexists['putinservice'] == "" || $bikeexists['putinservice'] == "0000-00-00"){ echo "<br />This bike has not yet been put in service! DO NOT LOAN. Merci! $back"; die(); }
if($bikeexists['inrepair'] != "" && $bikeexists['inrepair'] != "0000-00-00"){ echo "<br />This bike is in repair. DO NOT LOAN. Merci! $back"; die(); }
if($bikeexists['retired'] != "" && $bikeexists['retired'] != "0000-00-00"){ echo "<br />This bike has been retired from the library. Do not loan. $back"; die(); }


//Check if bike is in or out
$inoutquery = mysql_query("SELECT * FROM libraryloans WHERE bikeID='$_POST[bikeID]' AND bikeout=1",$dbf->conn);
$loanarray = mysql_fetch_array($inoutquery);

//decides if the form will be used to sign in or add a loan.
if($loanarray['id'] != "")
{
	$action="update";
//	print_r($loanarray);
}
else
{
	$action="insert";
}

//if action is update, sets variables to what the current loan data is.
if($action=="update")
{
	$display->displayTitle("Bike is OUT. Sign it in");

	if(isset($_POST['bikeID']))
	{
// echo "Now it's all: $_POST[bikeID]";
		$bikeID=$_POST['bikeID'];
		$tablename = "$cfg_tableprefix".'libraryloans';
		$result = mysql_query("SELECT *, UNIX_TIMESTAMP(duedate)as latedate FROM $tablename WHERE bikeID=\"$bikeID\" AND bikeout=1",$dbf->conn);
		
		$row = mysql_fetch_assoc($result);
		$userID=$row['userID'];
		$loanID=$row['id'];
		$deposittaken=$row['deposittaken'];
		$loandate=$row['loandate'];
		$duedate=$row['duedate'];
		$returndate=$row['returndate'];
		$notes=$row['notes'];
		$latefees=$row['latefees'];
		$latedate=$row['latedate'];

$today = date('U');
if($today > $latedate){
	$todayowing = round((($today-$latedate)/60/60/24)-1, 0) * $cfg_dailyLateFee; 
	echo "<center>There is <b>\$$todayowing.00</b> owing in late fees.</center><br />";
}

	}

}
else
{
	$display->displayTitle("Bike #$_POST[bikeID] is available for loan. Use form below.");
}
//creates a form object
$f1=new form('process_form_library.php','POST','library','450',$cfg_theme,$lang);

// Get User ID's and names for the select creation
	//sidenote: if user has bike, grab user number and add SELECTED to their entry in the select (last 3 lines)
$fnamearray = array();
$lnamearray = array();
$userIDarray = array();
$usrquery = mysql_query("SELECT first_name, last_name, id FROM customers ORDER BY last_name ASC");
while ($row = mysql_fetch_assoc($usrquery))
{
$namearray[] = $row['last_name'] .',' . $row['first_name'];
$idstring = $row['id'];
if($userID == $row['id']){ 
$idstring .= "SELECTED"; }
$userIDarray[] = $idstring;
} 

if($action == "update"){ $disabled="disabled"; }



//creates form parts.
$f1->createSelectField("<b>Member: </b>",'userID',$userIDarray,$namearray,'170',"$disabled");
$f1->createInputField("<b>Deposit Taken:</b> $",'text','deposittaken',"$deposittaken",'24','170',"$disabled");
if ($action == "update"){ $f1->createInputField("Due Date (YYYY-MM-DD): ",'text','<b>duedate</b>',"$duedate",'24','170',"$disabled"); }
if ($action == "insert"){ $f1->createSingleDateSelectField("<b>Due Date</b>"); }
$f1->createCheckboxField("<b>Paying fees now?</b>","feespaid",'170');
//$f1->createInputField("<i>Late Fees Paid: $</i> ",'text','amtpaid',"",'24','170');
$f1->createTextareaField("Notes about this loan:",'notes','5','24',"$notes",'170');

//sends many hidden varibles needed for process_form_library.php.
echo "		
		<input type='hidden' name='action' value='$action'>
		<input type='hidden' name='action' value='$action'>
		<input type='hidden' name='id' value='$loanID'>
		<input type='hidden' name='bikeID' value='$_POST[bikeID]'>";
if($action == "update"){
		echo "<input type='hidden' name='userID' value='$userID'>";
		echo "<input type='hidden' name='duedate' value='$duedate'>";
		echo "<input type='hidden' name='deposittaken' value='$deposittaken'>";
		echo "<input type='hidden' name='todayowing' value='$todayowing'>";

		
}

$f1->endLibraryForm();
$dbf->closeDBlink();


?>
</body>
</html>




