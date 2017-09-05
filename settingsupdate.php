<?php session_start();
//<?php
include ("settings.php");
include ("classes/db_functions.php");
include ("language/$cfg_language");
include ("classes/security_functions.php");
include ("classes/form.php");
include ("classes/display.php");
$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);
$display=new display($dbf->conn,$cfg_theme,$cfg_currency_symbol,$lang);
if(!$sec->isLoggedIn())
{
	header ("location: login.php");
	exit();
}

if(isset($_GET[mask])){
	$userLogin = $_SESSION['session_user_id'];
	$data = $dbf->idToField($cfg_tableprefix.'users', 'settings', $userLogin);
	switch($_GET[op]){
	 case 1:
		$data &= ~($_GET[mask]);
		break;
	 default:
		$data |= $_GET[mask];
		break;
	}
	mysql_query("UPDATE users SET settings='$data' WHERE id='$userLogin'");
}

$dbf->closeDBlink();
header("location: home.php");

?>
<html>
<head>
</head>

<body>
</body>
</html>




