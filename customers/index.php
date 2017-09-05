<?php session_start();
include ("../settings.php");
include("../language/$cfg_language");
include ("../classes/db_functions.php");
include ("../classes/security_functions.php");

$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);


if(!$sec->isLoggedIn())
{
	header ("location: ../login.php");
	exit();
}
$tablename = $cfg_tableprefix.'users';
$auth = $dbf->idToField($tablename,'type',$_SESSION['session_user_id']);
$first_name = $dbf->idToField($tablename,'first_name',$_SESSION['session_user_id']);
$last_name= $dbf->idToField($tablename,'last_name',$_SESSION['session_user_id']);
$today = date("Y-m-d");
if($auth=="Sales Clerk"){
	if(!$sec->isOpen()){
		header("location: ../books/openshop.php");
		exit();
	}
}
echo "
<html>
<body>
<head>

</head>

<table border=\"0\" width=\"500\">
  <tr>
    <td><img border=\"0\" src=\"../images/customers.gif\" width=\"41\" height=\"33\" valign='top'><font color='#005B7F' size='4'>&nbsp;<b>Members</b></font><br>
      <br>
      <font face=\"Verdana\" size=\"2\">Welcome to the Members panel! Here you can manage our members database. What would you like to do?</font>
      <ul>
        <li><font face=\"Verdana\" size=\"2\"><a href=\"../members/add.php?action=insert\">New Member</a></font></li>
        <li><font face=\"Verdana\" size=\"2\"><a href=\"manage_customers.php\">Edit or remove Members</a></font></li>


	<br /><br />
          <li><font face=\"Verdana\" size=\"2\"><a href=\"customers_barcode.php\">Member Barcode Sheet (don't even bother...)</a></font></li>
      </ul>
    </td>
  </tr>
</table>
</body>
</html>";

$dbf->closeDBlink();


?>
