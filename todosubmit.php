<?php session_start(); ?>

<html>
<head>
</head>

<body>
<?php
include ("settings.php");
include ("language/$cfg_language");
include ("classes/db_functions.php");
include ("classes/security_functions.php");

//creates 3 objects needed for this script.
$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);

//checks if user is logged in.
if(!$sec->isLoggedIn())
{
	header ("location: login.php");
	exit ();
}

//variables needed globably in this file.
$tablename="$cfg_tableprefix".'todolist';
$field_names=null;
$field_data=null;
$id=-1;
	

	if(isset($_POST['content']) && $_GET['action'] == "update")
	{
		$action="update";
		$id = $_GET['id'];
		
		//gets variables entered by user.
		$content = $_POST['content'];
		
		//ensure all fields are filled in.
		if($content=='')
		{
			echo "$lang->forgottenFields";
			exit();
		}
		else
		{
			$field_names=array('content');
			$field_data=array("$content");	
	
		}
		
	}
	elseif($_GET['completed'] == "yes"){
		$action="update";
		$id = $_GET['id'];
		$completed = "1";
		$field_names=array('completed');
		$field_data=array("$completed");	
	
	}
	elseif($_GET['action'] == "insert"){
		$action="insert";
		$name="$_POST[name]";
		$content="$_POST[content]";
		$field_names=array('name','content');
		$field_data=array("$name","$content");	

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
;
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

<br /><center>
<a href="home.php">Continue--></a></center>
</body>
</html>

