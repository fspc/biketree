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
$tablename="$cfg_tableprefix".'bikes';
$field_names=null;
$field_data=null;
//$id=-1;



	//checks to see if action is delete and an ID is specified. (only delete uses $_GET.)
	if(isset($_GET['action']) and isset($_GET['id']))
	{
		$action=$_GET['action'];
		$id=$_GET['id'];
		
	}

	//checks to make sure data is comming from form ($action is either dateup or update)
	elseif(isset($_POST['bikebrand']) and isset($_POST['bikemodel']) and isset($_POST['bikecolor']) 
	and isset($_POST['biketype']) and isset($_POST['wheel']) and isset($_POST['frame']) and isset($_POST['bikestatus']) and isset($_POST['id']) and isset($_POST['action']) )
	{
		
		$action=$_POST['action'];
		$id = $_POST['id'];

		//gets variables ALWAYS used for everything
		$bikebrand=$_POST['bikebrand'];
		$bikemodel=$_POST['bikemodel'];
		$bikecolor=$_POST['bikecolor'];
		$biketype=$_POST['biketype'];
		$wheel=$_POST['wheel'];
		$frame=$_POST['frame'];
		$bikestatus=$_POST['bikestatus'];

		//Adding a library bike to be in-service?  Make a date for it... today perhaps?

		if($action == "insert" && $bikestatus == "library"){ $putinservice=date('Y-m-d'); }

		//Making a library bike into an out of service library bike or vice versa?  Make it so in the DB...
		if($_POST[majorupdates] == "makeinrepair"){ $inrepair = date('Y-m-d'); }
		if($_POST[majorupdates] == "makeoutrepair"){ $inrepair = ""; }
		//same for retiring a library bike
		if($_POST[majorupdates] == "makeretire"){ $retired = date('Y-m-d'); }
		//If it's a member repair... same as above
		if($bikestatus == "repair" && $action == "insert"){ $inrepair = date('Y-m-d'); $userID=$_POST['userID']; }
		if($bikestatus == "repair" && $action == "update" && $_POST[repairpickup] == "on"){ $pickedupdate = date('Y-m-d'); }
		$duedate= "$_POST[year]-$_POST[month]-$_POST[day]";

		$notes=$_POST['notes'];
				
							// HERE YOU ARE UP TO
		//ensure all fields are filled in.
		if($bikebrand=='' or $bikemodel=='' or $bikecolor=='' or $frame=='')
		{
			echo "$lang->forgottenFields";
			exit();
		}
		else if($bikestatus == "library" && $action == "insert")
		{
			$field_names=array('bikebrand','bikemodel','bikecolor','biketype','wheel','frame','bikestatus','putinservice','inrepair','	retired','notes');
			$field_data=array("$bikebrand","$bikemodel","$bikecolor","$biketype","$wheel","$frame","$bikestatus","$putinservice","$inrepair","$retired","$notes");	
	
		}
		else if($bikestatus == "library" && $action == "update")
		{
			$field_names=array('bikebrand','bikemodel','bikecolor','biketype','wheel','frame','bikestatus','inrepair','	retired','notes');
			$field_data=array("$bikebrand","$bikemodel","$bikecolor","$biketype","$wheel","$frame","$bikestatus","$inrepair","$retired","$notes");	
	
		}
		else if($bikestatus == "sale")
		{
			$field_names=array('bikebrand','bikemodel','bikecolor','biketype','wheel','frame','bikestatus','notes');
			$field_data=array("$bikebrand","$bikemodel","$bikecolor","$biketype","$wheel","$frame","$bikestatus","$notes");	
	
		}
		else if($bikestatus == "repair" && $action == "update")
		{
			$field_names=array('bikebrand','bikemodel','bikecolor','biketype','wheel','frame','bikestatus','notes','putinservice');
			$field_data=array("$bikebrand","$bikemodel","$bikecolor","$biketype","$wheel","$frame","$bikestatus","$notes","$pickedupdate");	
	
		}
		else if($bikestatus == "repair" && $action == "insert")
		{
			$field_names=array('bikebrand','bikemodel','bikecolor','biketype','wheel','frame','bikestatus','inrepair','userID','duedate','notes');
			$field_data=array("$bikebrand","$bikemodel","$bikecolor","$biketype","$wheel","$frame","$bikestatus","$inrepair","$userID","$duedate","$notes");	
	
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
		$newnumber = mysql_insert_id();
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

if($action == "insert"){ echo "<center><h2>Important!!!</h2><h2>Tag this bike as BIKE NUMBER $newnumber</h2>"; }


?>
<br />

<a href="index.php">Manage Bikes--></a>
<br>
<a href="/home.php">Go Home--></a></center>
</body>
</html>
