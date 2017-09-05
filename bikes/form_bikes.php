<?php session_start(); ?>

<html>
<head>

</head>

<body>
<?php

include ("../settings.php");
include("../language/$cfg_language");
include ("../classes/db_functions.php");
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
$bikebrand_value='';
$bikemodel_value='';
$bikecolor_value='';
$biketype_number_value='';
$wheel_value='';
$frame_value='';
$bikestatus_value="$_GET[mode]";
$putinservice_value='';
$inrepair_value='';
$retired_value='';
$sold_value='';
$notes_value='';
$id=-1;

//decides if the form will be used to update or add a bike.
if(isset($_GET['action']))
{
	$action=$_GET['action'];
}
else
{
	$action="update";
}

//if action is update, sets variables to what the current users data is.
if($action=="update")
{
	if (!$_POST[id] && !$_GET[passbike]){ echo "Oops. Try again. Maybe with a valid bike number this time"; die(); }
	$display->displayTitle("Update a $_POST[mode] Bike");
	
	if(isset($_POST['id']) || isset($_GET['passbike']))
	{
		$id=$_POST['id'];
			if($id == ""){ 
				$id=$_GET[passbike]; 
				
				}
		$tablename = "$cfg_tableprefix".'bikes';

		$queree = "SELECT * FROM $tablename WHERE id=$id";

		$result = mysql_query("$queree",$dbf->conn);

		$row = mysql_fetch_assoc($result);
		$bikebrand_value=$row['bikebrand'];
		$bikemodel_value=$row['bikemodel'];
		$bikecolor_value=$row['bikecolor'];
		$biketype_value=$row['biketype'];


		if ($biketype_value == ""){ echo "Oops, one of the fly rod's has gone out askew on the treddle. Try again. Maybe with a valid bike number this time"; die(); }
		$wheel_value=$row['wheel'];
		$frame_value=$row['frame'];
		$bikestatus_value=$row['bikestatus'];
		$putinservice_value=$row['putinservice'];
		$inrepair_value=$row['inrepair'];

	if($putinservice_value != "0000-00-00" && $bikestatus_value == "repair"){ echo "This fuckin bike was a repair and has already been returned to the owner."; die();}

	if ($inrepair_value != '' && $inrepair_value != '0000-00-00' && $bikestatus_value == "library"){ echo "<center><h4 style=\"background: #000000; color: #FFFFFF; display: inline;\">This library bike is in for repair!</h4><center><br />"; }
		$userID_value=$row['userID'];
		$retired_value=$row['retired'];
	if($retired_value != "0000-00-00" && $retired_value != ""){ die('This bike has been retired and probably stripped down');}
		$sold_value=$row['sold'];
		$notes_value=$row['notes'];

	}

}
else
{
	$display->displayTitle("Add a $bikestatus_value Bike");
}
//creates a form object
$f1=new form('process_form_bikes.php','POST','bikes','450',$cfg_theme,$lang);

//creates form parts.
//Get user List first
	$idarray = array();
	$namearray = array();
	$result = mysql_query("SELECT id,first_name,last_name FROM customers ORDER BY last_name ASC");
	while($field = mysql_fetch_array($result)) { 
		$namearray[] = "$field[last_name], $field[first_name]"; 
		$idarray[] = "$field[id]"; 
	}

if($_POST[id]){ $disable = "DISABLED"; }
if ($_GET[mode] == "repair" || isset($userID_value) && $userID_value != 0){ $f1->createSelectField("<b>Which Member?</b>",'userID',$idarray,$namearray,'150',"$disable","$userID_value"); }
$f1->createInputField("<b>Brand:</b> ",'text','bikebrand',"$bikebrand_value",'24','150');
$f1->createInputField("<b>Model:</b> ",'text','bikemodel',"$bikemodel_value",'24','150');
$f1->createInputField("<b>Color:</b> ",'text','bikecolor',"$bikecolor_value",'24','150');
//make the bike type arrays
$option_values = array('newroad','10spd','8spdinternal','5spd','3spd','singlespeedcoaster','singlespeed','fixedgear','mountain','hybrid','chopper');
$option_titles = array('road bike (12-27speed)','10 speed road bike','8 speed internal hub','5 speed road bike','3 speed internal hub','single speed w/coaster brake','single speed w/brakes','fixed gear','mountain bike','hybrid (road/mountain)','chopper');
$f1->createSelectField("<b>Bike Type</b>",'biketype',$option_values,$option_titles,'150','NULL',"$biketype_value");
//make the wheel size array
$option_values = array('20inch','22inch','24inch','26inch','26fractional','27inch','','','650','700');
$option_titles = array('20 inch','22 inch','24 inch','26 inch','26 by fraction','27 inch','','----Metric Crap----','650','700c');
$f1->createSelectField("<b>Wheel Size</b>",'wheel',$option_values,$option_titles,'150','NULL',"$wheel_value");
$f1->createInputField("<b>Frame Height (inches)</b>: ",'text','frame',"$frame_value",'4','150');
//select bikeStatus here

//make the bike status array and form field
$option_values = array('library','sale','repair');
$option_titles = array('Library bike','For sale bike','Member bike in for repair');
if($action == "insert"){ $statdisable = "DISABLED"; }
$f1->createSelectField("<b>Bike Status</b>",'bikestatus',$option_values,$option_titles,'150',"$statdisable","$bikestatus_value");

if ($_GET[mode] == "repair"){ $f1->createSingleDateSelectField("To be picked up on:"); }

// major changes to library bike
if($inrepair_value != "" && $inrepair_value != "0000-00-00"){ $repairtext = "Mark library bike as fixed"; $repairvalue = "makeoutrepair"; } 
	else { $repairtext = "Mark as broken library bike"; $repairvalue = "makeinrepair";}
$option_values = array("$repairvalue",'makeretire');
$option_titles = array("$repairtext",'Retire this bike from library');
if($bikestatus_value=="library" && $action=="update"){ $f1->createRadioField("Major Updates",'majorupdates',$option_values,$option_titles,'150','',"$bikestatus_value"); }


$f1->createTextareaField("Repair needed:<br />Accepted by:<br />Other notes:",'notes','6','30',"$notes_value",'150');
if($bikestatus_value == "repair"){
	$f1->createCheckboxField("Remember to process payment<br /> in the sales area. ",'repairpickup','150','','','<b>Check if being picked up</b>');
}


//sends 2 hidden varibles needed for process_form_users.php.
echo "		
		<input type='hidden' name='action' value='$action'>
		<input type='hidden' name='id' value='$id'>";
if($action == "insert"){ echo "<input type='hidden' name='bikestatus' value='$_GET[mode]'>"; }
$f1->endForm();
$dbf->closeDBlink();


?>
</body>
</html>




