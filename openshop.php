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
include ("classes/display.php");

$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);
$display=new display($dbf->conn,$cfg_theme,$cfg_currency_symbol,$lang);

if(!$sec->isLoggedIn())
{
	header ("location: ../login.php");
	exit();
}

$body.="ok, so here we're going to have a forced opening page. To disable this for now,
	comment out the if statement directly after 'if(!sec->isLoggedIn())...' in home .php";
echo "$body";

?>

</body>
</html>
