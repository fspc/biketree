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
   
global $cfg_membershipID;
   
if(!$sec->isLoggedIn())
{
//        header ("location: ../login.php");
//        exit();
}

?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="../allstyles.css" />
</head>
<body>


<center>
<?php

// sending query
if ($_GET[userID] != ""){
// echo "userID is set";
$result = mysql_query("SELECT * FROM customers WHERE id='$_GET[userID]'");
if (!$result) {
    die("Query to show fields from table failed");
}
$fields_num = mysql_num_fields($result);
$field = mysql_fetch_array($result);

foreach($field as $key=>$value) { $$key = stripslashes($value); }  

}

// Get expirey date
// Construct the bitchin join query
$query22 = "SELECT sales.id, sales_items.sale_id, sales_items.item_id, DATE_ADD(sales.date, INTERVAL 1 YEAR) as expires ".
 "FROM sales, sales_items ".
	"WHERE sales.id = sales_items.sale_id AND sales_items.item_id = '1' AND sales.customer_id = '$_GET[userID]'";
$result22 = mysql_query($query22) or die(mysql_error());

$today = date('Y-m-d');

// Print out the contents of each row into a table 
$row = mysql_fetch_array($result22);
$expires = $row['expires'];
// echo "EXPIRES $expires";
	if ($row['item_id'] == "1" && $expires > $today){
	$expiredate = $expires;
	} else {
	$expiredate = "Membership not paid (or expired $expires)";
	}



//mysql_free_result($result);
?>

<form name=booking enctype="multipart/form-data" method="POST" action="add.php?userID=<? echo $_GET[userID]; ?>">
<table class="text">
<tr>
<td class="40high">
<h2 style="float: left;"> <? echo "$first_name $last_name";  ?></h2><input type="submit" name="edit" value="Edit Member" style="float: right;"><br />

</td>
</tr>
<tr>
<td valign="middle" align="right" width="*" height="*">
<h3>Personal Information</h3>

</td>
</tr>
<tr>
<td>
   <table class="text">
   <tr>
   <td valign="middle" align="right" width="*"><b>Member Number: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<? echo $id; ?>
   </td>
   </tr>
   <tr>
   <td valign="middle" align="right" width="*"><b>E-mail: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<? echo $email; ?>
   </td>
   </tr>

   <tr>
   <td valign="middle" align="right" width="*"><b>Email Lists: </b></td>
   <td valign="middle" align="left" width="*">
<?
if ($maillist1 == "1"){ $maillists = "$cfg_mailmanListName1"; }
if ($maillist2 == "1"){ $maillists .= ", $cfg_mailmanListName2"; }
if ($maillist3 == "1"){ $maillists .= ", $cfg_mailmanListName3"; }
echo "&nbsp;$maillists";

?>




   </td>
   </tr>



   <tr>
   <td valign="middle" align="right" width="*"><b>Phone Number: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<? echo $phone_number;?>

  
		
   </td>
   </tr>

   <tr>
   <td valign="middle" align="right" width="*"><b>Mailing Address: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<? echo $street_address; ?>
   </td>
   </tr>

   </table>
</td>
</tr>
<tr><td align="right"><h3>Membership</h3>
<tr><td align="left">

   <table class="text">
   <tr>

   <td valign="middle" align="right" width="*">
   &nbsp;<b>Member Type:&nbsp;</td><td>&nbsp; </b> <? echo $membertype; ?>
  
		
   </td>
   </tr>

   <tr>
   <td valign="middle" align="right" width="*"><b>Liability Waiver Signed? </b></td>
   <td valign="middle" align="left" width="*">
<?
if ($waiver == "1"){ $waivercheck = "Yes"; } else { $waivercheck = "No";  }
?>
   &nbsp; <? echo $waivercheck; ?>

   </td>
   </tr>
   <tr>
   <td valign="middle" align="right" width="*"><b>Membership Expires: </b></td>
   <td valign="middle" align="left" width="*	">
   &nbsp; <? echo $expiredate; ?>

   </td>
   </tr>

   </table>

</td></tr>
</td></tr>
<tr><td align="right"><h3>Member Standing / Hours</h3>
</td></tr>
<tr><td align="left">




   <table class="text">
   <tr>
   <td valign="middle" align="right" width="*" colspan=2><b>Member Flags: </b></td>
   <td valign="middle" align="left" width="*" colspan=3>
<?
if ($warnedonce == "1"){ $flags = "Warned Once"; }
if ($warnedtwice == "1"){ $flags .= " - Warned Twice"; }
if ($banned == "1"){ $flags .= " - Banned"; }
echo "&nbsp;$flags";
?>




   </td>
   </tr>



   <tr>
   <td valign="middle" align="right" width="*" colspan=2><b>Notes About This Member: </b></td>
   <td valign="middle" align="left" width="*" colspan=3>
   &nbsp;<? echo $comments; ?>
   </td>
   </tr>

   <tr>
   <td valign="middle" align="left" width="*" colspan=5><br /><b>Record of Visits: </b></td></tr>


	<?
	$vquery = "SELECT *, DATE_FORMAT(endout,'%l:%i %p') as humanout, DATE_FORMAT(intime,'%b %e, %Y') as humanindate, DATE_FORMAT(intime,'%l:%i %p') as humanintime, UNIX_TIMESTAMP(intime) as unixin, UNIX_TIMESTAMP(endout) as unixout FROM visits WHERE userID=$id AND endout >= 1";
	$vresult = mysql_query($vquery);
	if (!$result) {
		die("Query to show visits from table failed");
	}
	$totalseconds=0;
	while($row = mysql_fetch_array($vresult)){
	echo "<tr>";
	echo "<td valign=\"middle\" align=\"left\" width=\"*\" style=\"border-right: 1px dotted #000000\">$row[humanindate]</td>
	<td style=\"border-right: 1px dotted #000000\">$row[humanintime] - $row[humanout]</td>
	<td style=\"border-right: 1px dotted #000000\" align=center>$row[activity]</td>
	<td>";

	$timespent = $row[unixout] - $row[unixin];
	echo number_format(round($timespent / 3600*4)/4, 2) . " hrs";
	echo "</td>
	<td style=\"border-left: 1px dotted #000000\" align=center><a href=\"signin.php?visitID=$row[visitID]&userID=$id&activity=$row[activity]\">[Edit This Visit]</a></td>
	</tr>";
	$totalseconds = $totalseconds + $timespent;
	}

	$hours=round($totalseconds/3600);
	echo "<tr><td colspan=1> </td><td colspan=2 align=\"right\"><br />Rounded Total:</td><td colspan=2 align=left><br /><b> $hours hours</b></td></tr>";

	

	?>
  

   </table>

	
</td></tr>



<tr>
<td class="submit">
   <input type="submit" name="edit" value="Edit Member">
   </td>
   </tr>
   <tr>

</td>
</tr>
</table>
<br><br>
</form>	

</center>
</body>
