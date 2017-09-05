<?php session_start();?>
<html>
<head>

</head>

<body>
<?php
include ("../settings.php");
include ("../classes/db_functions.php");
include ("../language/$cfg_language");
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

if($_POST[ID]){
	$userLogin = $_SESSION['session_user_id'];
	mysql_query("UPDATE users SET customerID='$_POST[ID]' WHERE id='$userLogin'");
	echo "<script>document.location.href='../index.php'</script>";
}

$result = mysql_query("SELECT id,first_name,last_name FROM customers ORDER BY last_name ASC");
$body.="
<form name=oopen enctype=\"multipart/form-data\" method=\"POST\" action=\"user_customer_link.php\">
	<h5>It appears as though your bike tree user account has not been linked with a bike root member account.
	Please select your member account.</h5>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=\"Verdana\" size=\"2\">
	&nbsp;&nbsp;&nbsp;&nbsp;Users: 
	<select name=\"ID\">";
		while($field = mysql_fetch_array($result)) { $body.="<option value=\"$field[id]\">$field[last_name], $field[first_name] </option>"; }
    $body.="
    </select>
    <br><br><br>
	</font>
	<input type=\"submit\" name=\"submit\" value=\"Ok!\">
</form>
	";
echo "$body";

/*//check to make sure it's a number
if(!strval(floatval($_POST[openCount])) == strval($_POST[openCount])){
	echo "<script>document.location.href='openshop.php?error=2&count=$_POST[openCount]'</script>";
	exit();
}

//check to make sure it was the administrator who counted
if(!$_POST[counter]){
	echo "<script>document.location.href='openshop.php?error=1'</script>";
	exit();
}

//$tablename = $cfg_tableprefix.'users';
$userLoginName = $dbf->idToField($cfg_tableprefix.'users','username',$_SESSION['session_user_id']);


$tablename="$cfg_tableprefix".'books';
$field_names=null;
$field_data=null;
$today = date('Y-m-d');
$field_names=array('date','event','user','ammount','data');
$field_data=array("$today", "open", "$userLoginName","$_POST[openCount]","$_POST[mechID]");


$dbf->insert($field_names,$field_data,$tablename,"");

$tablename="$cfg_tableprefix".'visits';
$tdin = date('Y-m-d H:i:s');
$field_names=array('userID','intime','activity');
$adminID = $dbf->idToField($cfg_tableprefix.'users','customerID',$_SESSION['session_user_id']);
$field_data=array("$adminID", "$tdin", "Administrator");
$dbf->insert($field_names, $field_data, $tablename, "");

echo "<script>document.location.href='../home.php'</script>";*/

$dbf->closeDBlink();


?>
</body>
</html>




