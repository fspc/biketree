<?php session_start();
include ("../settings.php");
include("../language/$cfg_language");
include ("../classes/db_functions.php");
include ("../classes/security_functions.php");

$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Sales Clerk',$lang);


if(!$sec->isLoggedIn()){
	header("location: ../login.php");
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
    <td><img border=\"0\" src=\"../images/customers.gif\" width=\"41\" height=\"33\" valign='top'><font color='#005B7F' size='4'>&nbsp;<b>Rental Bikes - Sale Bikes - Repair Bikes</b></font><br>
      <br>
      <font face=\"Verdana\" size=\"2\">Welcome to the Bikes panel! Here you can manage <b>any</b> bikes that are in the shop. What would you like to do?	
      <br /><br /><b>Add a bike!</b>
      <ul>
        <li><font face=\"Verdana\" size=\"2\"><a href=\"form_bikes.php?action=insert&mode=repair\">Enter a new member bike in for repair</a></font><br /><br /></li>
        <li><font face=\"Verdana\" size=\"2\"><a href=\"form_bikes.php?action=insert&mode=library\">Add a new bike to the library</a></font><br /><br /></li>
        <li><font face=\"Verdana\" size=\"2\"><a href=\"form_bikes.php?action=insert&mode=sale\">Add a new for-sale completed bike</a></font><br /><br /></li>
      </ul></font>
 
      <font face=\"Verdana\" size=\"2\"><b>Update/modify bike info</b><br /></font>
<form name=bikenumber enctype=\"multipart/form-data\" method=\"POST\" action=\"form_bikes.php?action=update\">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=\"Verdana\" size=\"2\">Bike Number:</font>
	&nbsp;<input type=\"text\" name=\"id\" size=\"10\">
	<input type=\"submit\" name=\"submit\" value=\"Ok Go!\">
</form>


   </td>
  </tr>
</table>
</body>
</html>";

$dbf->closeDBlink();


?>
