<?php session_start(); ?>

<html>
<head>
<link rel="stylesheet" href="form.css" type="text/css">
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

if(!$sec->isLoggedIn())
{
	header ("location: ../login.php");
	exit();
}

$today = date("Y-m-d");
//$result = mysql_query("SELECT * FROM books");

$body.="</select>";

$tablename = $cfg_tableprefix.'users';
$userLoginName = $dbf->idToField($tablename,'username',$_SESSION['session_user_id']);

if(isset($_GET[error])){
	$error = (int)$_GET[error];
	$errorMsg = "";
	switch($error){
	 case 1:
		$errorMsg="ERROR: invalid username or password";
		break;
	 case 2:
		$errorMsg="ERROR: Not a valid ammount: [$_GET[count]]";
		break;
	}
}

if($errorMsg != ""){
	$body.="<br><font color=\"red\">".$errorMsg."</font><br>";
}

$body.="
<h2>Close The Shop...</h2>
<form class=\"form\" name=closeform enctype=\"multipart/form-data\" method=\"POST\" action=\"verifyaction.php\">
	<h5>Please count all cash, cheques, and coupons in the coin box</h5>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=\"Verdana\" size=\"2\">Closing Count: $
	<input type=\"text\" name=\"ammount\" size=\"10\" value=\"$_GET[count]\">
	<br>
	Counted by:
    <blockquote>Username: <input type=\"text\" name=\"username\" size=\"15\" value=\"$userLoginName\"><br>
    Password: <input type=\"password\" name=\"password\" size=\"15\"></blockquote>
    <br><br><br>
    <input type=\"hidden\" name=\"action\" value=\"2\">
    <input type=\"hidden\" name=\"data\" value=\"0\">
    </font>
	<input type=\"submit\" name=\"submit\" value=\"Process!\">
</form>
	";
echo "$body";
//	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Counted by <b>$userLoginName</b>&nbsp;<input type=\"checkbox\" name=\"counter\">
$dbf->closeDBlink();

?>

</body>
</html>
