<?php session_start(); ?>

<html>
<head>
</head>

<body>
<?php
include ("../settings.php");
include ("../language/$cfg_language");
include ("../classes/db_functions.php");
include ("../classes/security_functions.php");

//creates 3 objects needed for this script.
$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);

//checks if user is logged in.
if(!$sec->isLoggedIn())
{
	header ("location: ../login.php");
	exit ();
}

//variables needed globably in this file.
$tablename="$cfg_tableprefix".'libraryloans';
$field_names=null;
$field_data=null;
$id=-1;



	//checks to see if action is delete and an ID is specified. (only delete uses $_GET.)
	if(isset($_GET['action']) and isset($_GET['id']))
	{
		$action=$_GET['action'];
		$id=$_GET['id'];

	}
	//checks to make sure data is comming from form ($action is either delete or update)
	elseif(isset($_POST['userID']) and isset($_POST['deposittaken']) and isset($_POST['notes']) )
	{
		
		$action = $_POST['action'];
		$id = $_POST['id'];
		$bikeID = $_POST['bikeID'];
		//gets variables entered by user.
		$userID = $_POST['userID'];
		$deposittaken = $_POST['deposittaken'];
		$duedate = "$_POST[year]-$_POST[month]-$_POST[day]";
		$paid = $_POST['feespaid'];
		$notes = $_POST['notes'];
		$todayowing = $_POST['todayowing'];
		$today = date('Y-m-d');
		//insure all fields are filled in.
		if($userID=='' or $deposittaken=='' or $duedate=='')
		{
			echo "$userID AND $deposittaken AND $duedate $lang->forgottenFields";
			exit();
		}
		else
		{
			if($action == "insert"){
			$field_names=array('userID','bikeID','bikeout','deposittaken','loandate','duedate','notes');
			$field_data=array("$userID","$bikeID","1","$deposittaken","$today","$duedate","$notes");	
			}
			if($action == "update")
			{
			if($paid == "on"){ $feesowing = 0; }
			$field_names=array('bikeout','returndate','notes','latefees');
			$field_data=array("0","$today","$notes","$feesowing");	
			}

		}
		
	}
	else
	{
		//outputs error message because user did not use form to fill out data.
		echo "$lang->mustUseForm";
		exit();
	}
	


switch ($action)
{
	//finds out what action needs to be taken and preforms it by calling methods from dbf class.
	case $action=="insert":
		$dbf->insert($field_names,$field_data,$tablename,true);
	break;
		
	case $action=="update":
		$dbf->update($field_names,$field_data,$tablename,$id,true);
				
	break;
	
	case $action=="delete":
		$dbf->deleteRow($tablename,$id);
	
	break;	
	
	default:
		echo "$lang->noActionSpecified";
	break;
}
$dbf->closeDBlink();

?>
<br>
<a href="index.php">Back to Bike Library--></a>
<br>
<a href="../home.php">Go Home--></a>
</body>
</html>
