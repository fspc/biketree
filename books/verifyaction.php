<?php session_start();
//<?php
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

//echo "Processing...";

$action = $_POST[action];
//check to make sure it's a number
$actionPage = "";
$user = null;
switch($action){
 case 1:
	$actionPage = "openshop";
	$user = $_SESSION['session_user_id'];
	break;
 case 2:
	$actionPage = "closeshop";
	$user = $_POST[username];
	break;
 case 3:
	$actionPage = "openshop";
	$user = $_POST[username];
	break;
 case 4:
 case 5:
	$actionPage = "depositPayout";
	$user = $_POST[username];
	break;
}

//make sure the ammount looks right
if(!isset($_POST[ammount]) || !strval(floatval($_POST[ammount])) == strval($_POST[ammount])){
	header("location: $actionPage.php?error=2&count=$_POST[ammount]");
	exit();
}

//check to make sure it was the administrator who counted
// Or that a valid username and password was entered
if($action == 1 && !$_POST[counter]){
	header("location: $actionPage.php?error=1&count=$_POST[ammount]");
	exit();
}else if($action != 1 && !$sec->checkLogin($_POST[username], md5($_POST[password]))){
	header("location: $actionPage.php?error=1&count=$_POST[ammount]");
	exit();
}
if((($action == 4 || $action == 5) && (!isset($_POST[data]) || !strlen($_POST[data]))) || ($action == 5 && (!isset($_POST[data2]) || !strlen($_POST[data]) || strrchr($_POST[data], "^")))){
	header("location: $actionPage.php?error=3&count=$_POST[ammount]&data=$_POST[data]&data2=$_POST[data2]");
	exit();
}

//echo "stuff";
if(($action == 1 || $action == 2) && !$_POST[nocompare]){
	$lastCountFound = false;
	$compareAmmount = 0.0;
	if($action == 2){
		$cashresult = mysql_query("SELECT sale_total_cost FROM sales WHERE date='$today'");
		while ($casharray = mysql_fetch_array($cashresult)){
			$compareAmmount += $casharray[sale_total_cost];
		}
	}

	$le = mysql_query("SELECT * FROM books ORDER BY listID DESC");//, $dfb->conn);
	while(($item = mysql_fetch_assoc($le)) && !$lastCountFound){
		switch($item[event]){
		 case 1:
		 case 2://close
			$lastCountFound = true;
			$compareAmmount += ($item[ammount] / 100.0);
			break;
		 case 4://deposit
			$compareAmmount -= ($item[ammount] / 100.0);
			break;
		 case 5://payout
			$compareAmmount -= ($item[ammount] / 100.0);
			break;
		}
	}
	if($_POST[ammount] != $compareAmmount){
		$difference = round(abs($_POST[ammount] - $compareAmmount), 2);//, PHP_ROUND_HALF_UP);
		echo "<html><head><link rel=\"stylesheet\" href=\"form.css\" type=\"text/css\"></head>";
		echo "<body><h2>Count was ";
		if($_POST[ammount] < $compareAmmount){
			echo "Short";
		}else{
			echo "Over";
		}
		echo "</h2><form class=\"form\" name=\"continue\" enctype=\"multipart/form-data\" method=\"POST\" action=\"verifyaction.php\">
			<h3>There was a difference of $"."$difference<br><br>
			<input type=\"hidden\" name=\"ammount\" value=\"$_POST[ammount]\">
			<input type=\"hidden\" name=\"action\" value=\"1\">
			<input type=\"hidden\" name=\"counter\" value=\"1\">
			<input type=\"hidden\" name=\"data\" value=\"$_POST[data]\">
			<input type=\"hidden\" name=\"nocompare\" value=\"$action\">
			<input type=\"submit\" name=\"submit\" value=\"Count is correct, proceede...\">
		</form><br><br>
		<form name=\"redo\" class=\"subform\" enctype=\"multipart/form-data\" method=\"GET\" action=\"$actionPage.php\">
			<input type=\"hidden\" name=\"count\" value=\"$_POST[ammount]\">
			<input type=\"submit\" name=\"submit\" value=\"Recount...\">
		</form>
		</body>
		</html>";
		exit();
	}
	//		<input type=\"text\" name=\"username\" size=\"15\" value=\"$_POST[username]\">
	//	    <input type=\"hidden\" name=\"password\" size=\"15\ value=\"$_POST[password]\">
}

//$tablename = $cfg_tableprefix.'users';
$userLoginName = $dbf->idToField($cfg_tableprefix.'users','username',$_SESSION['session_user_id']);
$tablename="$cfg_tableprefix".'books';
$field_names=null;
$field_data=null;
$today = date('Y-m-d');
$ammount = $_POST[ammount]*100.0;
$field_names=array('date','event','user','ammount','data');
$data = $_POST[data];
if($action == 5){
	$data .= "^".$_POST[data2];
}
$field_data=array("$today", "$action", "$user","$ammount","$data");

$dbf->insert($field_names,$field_data,$tablename,"");

if($action == 1){//"open"){
	//no one should be logged in but in case they are, log them out.
	$now = date('Y-m-d H:i:s');
	$userresult = mysql_query("SELECT * FROM visits WHERE endout IS NULL ORDER BY activity ASC");
	while($row = mysql_fetch_array($userresult)){
		$visitID = $row[visitID];
		//
		$query="UPDATE visits SET endout='$now' WHERE visitID='$visitID' LIMIT 1";
		mysql_query($query) or die('Error, user not done . Consult Mark, he probably fucked up. OH shits');
	}
	$tablename="$cfg_tableprefix".'visits';
	//$tdin = date('Y-m-d H:i:s');
	if($cfg_mechAutoSignin != "no" && $_POST["m"]){
		$sec->signinMember($_POST[data], $now, "Mechanic");
	}
	if($cfg_adminAutoSignin){
		$adminID = $dbf->idToField($cfg_tableprefix.'users','customerID',$_SESSION['session_user_id']);
		$sec->signinMember($adminID, $now, "Administrator");
	}
	header("location: ../home.php");
}else if($action == 2){//"close"){
	//log everyone out
	$userresult = mysql_query("SELECT * FROM visits WHERE endout IS NULL ORDER BY activity ASC");
	while($row = mysql_fetch_array($userresult)){
		$visitID = $row[visitID];
		$now = date('Y-m-d H:i:s');
		$query="UPDATE visits SET endout='$now' WHERE visitID='$visitID' LIMIT 1";
		mysql_query($query) or die('Error, user not done . Consult Mark, he probably fucked up. OH shits');
	}
	session_destroy();
	//header("location: ../shopclosed.php");
	echo "<script>parent.document.location.href='../shopclosed.php'</script>";
}else{
	//header("location: ../index.php");
	echo "<script>document.location.href='../home.php'</script>";
}

$dbf->closeDBlink();


?>
<html>
<head>
<link rel="stylesheet" href="form.css" type="text/css">
</head>

<body>
</body>
</html>




