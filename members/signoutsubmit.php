<?php

include ("../settings.php");
include ("../language/$cfg_language");
include ("../classes/db_functions.php");
include ("../classes/security_functions.php");

function getmonth($m=0) {
return (($m==0 ) ? date("F") : date("F", mktime(0,0,0,$m)));
}


						// MAKE IT SO IN THE USER DATABASE
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);

if(isset($_GET[visitID])){
  $visitID = $_GET[visitID];
  $isAdmin = false;
  //echo "javascript:alert(\"$visitID\")";
  if($visitID < 1){
    $result = mysql_fetch_array(mysql_query("SELECT visitID FROM visits WHERE endout IS NULL AND activity = \"Administrator\""));
    $visitID = $result[visitID];
    //echo "javascript:alert(\"$visitID\");";
    $isAdmin = true;
  }

//MAKE THE TIME!
$now = date('Y-m-d H:i:s');
$query="UPDATE visits SET endout='$now' WHERE visitID='$visitID' LIMIT 1";

mysql_query($query) or die('Error, user not done . Consult Mark, he probably fucked up. OH shits');
}

if(isset($_GET[switchID])){
  $tablename="$cfg_tableprefix".'visits';
  $tdin = date('Y-m-d H:i:s');
  $field_names=array('userID','intime','activity');
  //$adminID = $dbf->idToField($cfg_tableprefix.'users','customerID',$_SESSION['session_user_id']);
  $field_data=array("$_GET[switchID]", "$tdin", "Mechanic");
  $dbf->insert($field_names, $field_data, $tablename, "");
}		

if($isAdmin){
  header('Location: /pos/login.php');
}else{
  header('Location: /pos/home.php');
}
?>


