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
if(!$sec->isOpen()){
	header("location: ../books/openshop.php");
	exit();
}

echo "
<html>
<body>
<head>

</head>

<table border=\"0\" width=\"500\">
  <tr>
    <td><img border=\"0\" src=\"../images/customers.gif\" width=\"41\" height=\"33\" valign='top'><font color='#005B7F' size='4'>&nbsp;<b>Bike Library</b></font><br>
      <br>
<form name=booking enctype=\"multipart/form-data\" method=\"POST\" action=\"form_library.php\">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=\"Verdana\" size=\"2\">Sign in/out - Bike Number:</font>
	&nbsp;<input type=\"text\" name=\"bikeID\" size=\"10\" value=\"$_GET[passbike]\">
	<input type=\"submit\" name=\"submit\" value=\"Ok Go!\">
</form>
To modify a library bike, please use the <a href=\"../bikes/index.php\"> <b>bikes</b></a> panel.
    </td>
  </tr>
</table>
</body>
</html>";

$dbf->closeDBlink();


?>
