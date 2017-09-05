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
        //header ("location: ../login.php");
        //exit();
}

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="../allstyles.css" />
</head>
<body>
<center>
<?php

include('odb.php');

 
if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");
 
if (!mysql_select_db($database))
    die("Can't select database");



// sending query
if (isset($_GET[userID])){
//echo "userID is $_GET[userID]";
$result = mysql_query("SELECT * FROM customers WHERE id='$_GET[userID]'");
if (!$result) {
    die(mysql_error());
}
$fields_num = mysql_num_fields($result);
$field = mysql_fetch_array($result);

foreach($field as $key=>$value) { $$key = stripslashes($value); }  

}


// Get expirey date
// Construct a join query which already ADDS ONE YEAR to the date of purchase to result in expirey date
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
	$expiredate = "Not Paid (or expired $expires) <br /> (USE SALES to sell a membership)";
	}


//mysql_free_result($result);
?>

<form name=booking enctype="multipart/form-data" method="POST" action="submit.php?userID=<? echo $id; ?>">
<table class="text" width="*" border="0" bordercolor="#000000" cellpadding="4" cellspacing="0">
<tr>
<td valign="middle" align="left" width="*" height="25">
<h2>Add/Edit A Member</h2>

</td>
</tr>
<tr>
<td valign="middle" align="right" width="*" height="*">
<h3>Personal Information</h3>
</td>
</tr>
<tr>
<td>
   <table class="text" width="*" border="0" bordercolor="#000000" cellpadding="0" cellspacing="0">
   <tr>
   <td valign="middle" align="right" width="*"><b>First Name: </b>&nbsp;<input type="text" value="<? echo $first_name;?>" name="fname" size="10"><font color="red">*</font></td>
   <td valign="middle" align="left" width="*">
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Last Name: </b>&nbsp;<input type="text" name="lname" size="10" value="<? echo $last_name;?>"><font color="red">*</font>
   </td>

   </tr>
   <tr>
   <td valign="middle" align="right" width="*"><b>E-mail: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<input type="text" name="email" size="30" value="<? echo $email;?>"><font color="red">*</font>
   </td>
   </tr>

   <tr>
   <td valign="middle" align="right" width="*"><b>Mailing Lists: </b></td>
   <td valign="middle" align="left" width="*">
<?
if ($maillist1 == "1"){ $mail1check = "CHECKED"; }
if ($maillist2 == "1"){ $mail2check = "CHECKED"; }
if ($maillist3 == "1"){ $mail3check = "CHECKED"; }
?> 
   &nbsp;<input type="checkbox" name="maillist1" <? echo "CHECKED> $cfg_mailmanListName1"; ?>&nbsp;&nbsp;
   &nbsp;<input type="checkbox" name="maillist2" <? echo "$mail2check> $cfg_mailmanListName2"; ?>&nbsp;&nbsp;
   &nbsp;<input type="checkbox" name="maillist3" <? echo "$mail3check> $cfg_mailmanListName3"; ?>

   </td>
   </tr>


<?
if ($phone_nuber == ""){ $phone1 = "403-"; }
?>

   <tr>

   <td valign="middle" align="right" width="*"><b>Phone Number: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<input type="text" name="phone1" size="12" maxlength="13" value="<? echo $phone_number;?>">
   <font color="red">*</font>
		
   </td>
   </tr>
   

   

   <tr>
   <td valign="middle" align="right" width="*"><b>Mailing Address: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<textarea name="address" rows="4" cols="30"><? echo $street_address; ?></textarea> <br />
   <font color="red">*</font>
   </td>
   </tr>

   </table>
</td>
</tr>
<tr><td align="right"><h3>Membership</h3>
<tr><td align="left">
<table class="text" width="*" border="0" bordercolor="#000000" cellpadding="0" cellspacing="0">
   <tr>
   <td valign="middle" align="right" width="*"><b>Student ID: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<input type="text" name="studentID" size="12" maxlength="13" value="<? echo $studentID;?>">
   

   </td>
   </tr>


   <tr>

   <td valign="middle" align="right" width="*"><b>OR: Drivers License #: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<input type="text" name="drivers" size="18" maxlength="13" value="<? echo $drivers;?>">(non-student)
   

   </td>
   </tr>


   <tr>

   <td valign="middle" align="right" width="*"><b>OR: Cash Deposit: </b><br /><br /></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<input type="text" name="cashdeposit" size="12" maxlength="13" value="<? echo $cashdeposit;?>">(no drivers license)<br /><br />
   

   </td>
   </tr>



 <tr>

   <td valign="middle" align="right" width="*">
<?
$$membertype = "SELECTED";
?>
   &nbsp;<b>Member Type:&nbsp;</td><td>&nbsp; </b>
	<select name="membertype">
		<option value="0">  </option>
		<option value="0">---Good Life Types---</option>
		<option value="member" <? echo $member; ?>>Member</option>
		<option value="paidmechanic" <? echo $paidmechanic; ?>>Paid Mechanic</option>
		<option value="paidgreaser" <? echo $paidgreaser; ?>>Paid Greaser</option>
		<option value="volunteer" <? echo $volunteer; ?>>Volunteer</option>
		<option value="0">  </option>
		<option value="0">---Bike Root (U of C) Types---</option>
		<option value="uofcstudent" <? echo $uofcstudent; ?>>U of C Student</option>
		<option value="uofcstaff" <? echo $uofcstaff; ?>>U of C Staff</option>
		<option value="uofccommunity" <? echo $uofccommunity; ?>>Community Member</option>
		<option value="uofcvolunteer" <? echo $uofcvolunteer; ?>>Volunteer</option>
		<option value="uofcorganizer" <? echo $uofcorganizer; ?>>Organizer/Key Member</option>

	</select>

<font color="red">*</font>
  
		
   </td>
   </tr>

   <tr>
   <td valign="middle" align="right" width="*"><b>Liability Waiver Signed </b></td>
   <td valign="middle" align="left" width="*">
<?
if ($waiver == "1"){ $waivercheck = "CHECKED"; }
?>
   &nbsp;<input type="checkbox" name="waiver" <? echo $waivercheck; ?>>

   </td>
   </tr>
   <tr>
   <td valign="middle" align="right" width="*"><b><? if(isset($_GET[userID])){ echo "Membership Expires:";} ?> </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp; <? if(isset($_GET[userID])){ echo $expiredate; } ?>
   </td>
   </tr>

   <tr>
   <td valign="middle" align="right" width="*"><b>Member Flags: </b></td>
   <td valign="middle" align="left" width="*">
<?
if ($warnedonce == "1"){ $warnedonce = "CHECKED"; }
if ($warnedtwice == "1"){ $warnedtwice = "CHECKED"; }
if ($banned == "1"){ $banned = "CHECKED"; }
?> 
   &nbsp;<input type="checkbox" name="warnedonce" <? echo "$warnedonce> First Warning"; ?><br />
   &nbsp;<input type="checkbox" name="warnedtwice" <? echo "$warnedtwice> Second Warning"; ?><br />
   &nbsp;<input type="checkbox" name="banned" <? echo "$banned> Banned from the shop"; ?>

   </td>
   </tr>




   <tr>
   <td valign="middle" align="right" width="*"><b>Notes About This Member: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;<textarea name="notes" rows="4" cols="30"><? echo $comments; ?></textarea>
   <font color="red">*</font>
   </td>
   </tr>


   </table>

	
</td></tr>



<tr>
<td valign="middle" align="center" width="*" height="*" style="border-bottom: 4px solid #333333; border-top: 1px dotted #333333">
   <input type="submit" name="submit" value="Submit">
   </td>
   </tr>
   
</table>
<font size="2"><i>
All information is held in confidence by TheBikeTree.<br>

Information will not be shared with any other organizations without your permission.</i></font><br><br>
</form>	

</center>
</body>
