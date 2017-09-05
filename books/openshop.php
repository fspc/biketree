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
$result = mysql_query("SELECT * FROM books");
if(!mysql_num_rows(mysql_query("SELECT * FROM books WHERE date='$today' AND event='close'")) && mysql_num_rows(mysql_query("SELECT * FROM books WHERE date='$today' AND event='open'"))){
	header("location: ../home.php");
	exit();
}

$body.="</select>";

$tablename = $cfg_tableprefix.'users';
$userLoginName = $dbf->idToField($tablename,'username',$_SESSION['session_user_id']);

$result = mysql_query("SELECT id,first_name,last_name FROM customers ORDER BY last_name ASC");

$error = (int)$_GET[error];
$errorMsg = "";
switch($error){
 case 1:
	$errorMsg="ERROR: if you are not $userLoginName please switch to your own administrator account";
	break;
 case 2:
	$errorMsg="ERROR: Not a valid ammount: [$_GET[count]]";
	break;
}

if($errorMsg != ""){
	$body.="<br><font color=\"red\">".$errorMsg."</font><br>";
}

$body.="
<h2>Open The Shop...</h2>
<form class=\"form\" name=oopen enctype=\"multipart/form-data\" method=\"POST\" action=\"verifyaction.php\">
	<h5>Before any members are singed in or any transactions are processed please count all
	cash, cheques, and coupons in the coin box</h5>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=\"Verdana\" size=\"2\">Opening Count: $
	<input type=\"text\" name=\"ammount\" size=\"10\" value=\"$_GET[count]\">
	<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Counted by <b>$userLoginName</b>&nbsp;<input type=\"checkbox\" name=\"counter\">
	<br><br>";
	if($cfg_mechAutoSignin != "no"){
		if($cfg_mechAutoSignin == "option"){
			$body .= "<input type=\"checkbox\" CHECKED name=\"m\">Sign in Mechanic: <br>";
		}else{
			$body .= "<input type=\"hidden\" value=\"on\" name=\"m\">";
		}
		$body .= "&nbsp;&nbsp;&nbsp;&nbsp;Mechanic on duty
		<select name=\"data\">";
			while($field = mysql_fetch_array($result)) { $body.="<option value=\"$field[id]\">$field[last_name], $field[first_name] </option>"; }
		$body.="
	    </select>";
	}
    $body .= "<input type=\"hidden\" name=\"action\" value=\"1\">
    <br><br><br>
	</font>
	<input type=\"submit\" name=\"submit\" value=\"Process!\">
</form>
	";
echo "$body";

$dbf->closeDBlink();

?>

</body>
</html>
