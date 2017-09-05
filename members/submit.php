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
</head>
<body>

<h3> Progress </h3>
<b>...If membership needs to be sold, click <a href="../sales/sale_ui.php">HERE</a></b><br /><br />
<?php



/* 
if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");
 
if (!mysql_select_db($database))
    die("Can't select database");
*/

/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}


// sending query

//echo "userID is not set";


$fname = $_POST[fname];
$lname = $_POST[lname];

$maillist = $_POST[maillist];


//if (!preg_match('/^(\(?[2-9]{1}[0-9]{2}\)?|[0-9]{3,3}[-. ]?)[ ][0-9]{3,3}[-. ]?[0-9]{4,4}$/', $_POST[phone1])) {
//die('Phone number invalid. Click back and try again.');
//}

if ($_POST[waiver] == "on"){ $waiver = "1"; } else { $waiver = "0";}
if ($_POST[maillist1] == "on"){ $maillist1 = "1"; } else { $maillist1 = "0";}
if ($_POST[maillist2] == "on"){ $maillist2 = "1"; } else { $maillist2 = "0";}
if ($_POST[maillist3] == "on"){ $maillist3 = "1"; } else { $maillist3 = "0";}
if ($_POST[warnedonce] == "on"){ $warnedonce = "1"; } else { $warnedonce = "0";}
if ($_POST[warnedtwice] == "on"){ $warnedtwice = "1"; } else { $warnedtwice = "0";}
if ($_POST[banned] == "on"){ $banned = "1"; } else { $banned = "0";}

$phone1 = $_POST[phone1];

$email = $_POST[email];
//  $pass = validEmail($email);
//  if ($pass)  { $email = $email; } else { die('E-mail Address is not valid. Click back and try again.'); }
$email = $email;
$address = $_POST[address];
$membertype = $_POST[membertype];
$notes = $_POST[notes];


if (!isset($_GET[userID]) || $_GET[userID] == ""){
	$query = "INSERT INTO customers (first_name, last_name, phone_number, email, maillist1, maillist2, maillist3, street_address, membertype, studentID, drivers, cashdeposit, waiver, warnedonce, warnedtwice, banned, comments) VALUES ('$fname', '$lname', '$phone1', '$email', '$maillist1', '$maillist2', '$maillist3', '$address', '$membertype', '$_POST[studentID]', '$_POST[drivers]','$_POST[cashdeposit]', '$waiver', '$warnedonce', '$warnedtwice', '$banned', '$notes')";

// echo "QUEERY:$query";

	mysql_query($query) or die('Error, user not added. Consult Mark...');

	echo "<b>...User has been added</b><br /><br />";

	//$query = "FLUSH PRIVILEGES";
	//mysql_query($query) or die('Error, flush insert query failed');

//	echo "<b>Here is some technical jargon if you want to check what info was added</b>... <br />$query"; 
/*
	//EMAIL WELCOME TO MEMBER!

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: reciepts@goodlifebikes.ca' . "\r\n";
	$headers .= 'Reply-to: info@goodlifebikes.ca' . "\r\n";
	
	 $message = "<html><body><br /><br /><b>Please retain or print this receipt for your records</b><br /> $body </body></html>";
	
	// In case any of our lines are larger than 70 characters, we should use wordwrap()
	$message = wordwrap($message, 70);
	
	// Send
	mail($customer_email, "Your E-receipt from $cfg_company", $message, $headers);
	
	echo "<h3>E-Reciept has been sent to &lt;$customer_email&gt;</h3>";

*/


} else {
  $query="UPDATE customers SET first_name='$fname', last_name='$lname', phone_number='$phone1', email='$email', maillist1='$maillist1', maillist2='$maillist2', maillist3='$maillist3', street_address='$address', membertype='$membertype', studentID='$_POST[studentID]', drivers='$_POST[drivers]', cashdeposit='$_POST[cashdeposit]', waiver='$waiver', comments='$notes', warnedonce='$warnedonce', warnedtwice='$warnedtwice', banned='$banned' WHERE id=$_GET[userID] ";

// echo "QUERY : $query";

	mysql_query($query) or die('Error, user not added. Consult Mark...');

	echo "<b>...User $fname $lname has been updated</b>(if no errors appear above)<br /><br />";



	//$query = "FLUSH PRIVILEGES";
	//mysql_query($query) or die('Error, flush insert query failed');

//	echo "<b>Here is some technical jargon if you want to check what info was added</b>... <br />$query"; 


}

if (!isset($_GET[userID]) || $_GET[userID] == ""){

	echo "<b>...register mailing list subscriptions</b>";
	if($maillist1 == 1){$subscribeURL = "http://$cfg_mailmanLocation/mailman/admin/$cfg_mailmanListName1" . "_" . "$cfg_mailmanLocation/members/add?subscribees=$email&adminpw=$cfg_mailmanPass&send_welcome_msg_to_this_batch=0&send_notifications_to_list_owner=0";
	echo "<iframe scrolling=no src=\"$subscribeURL\" style=\"display: block;\" width=\"500\" height=\"80\"><p>Your browser does not support iframes.</p></iframe>";
	}
if($maillist2 == 1){$subscribeURL = "http://$cfg_mailmanLocation/mailman/admin/$cfg_mailmanListName2" . "_" . "$cfg_mailmanLocation/members/add?subscribees=$email&adminpw=$cfg_mailmanPass&send_welcome_msg_to_this_batch=0&send_notifications_to_list_owner=0";
        echo "<iframe scrolling=no src=\"$subscribeURL\" style=\"display: block;\" width=\"500\" height=\"80\"><p>Your browser does not support iframes.</p></iframe>";
        }
if($maillist3 == 1){$subscribeURL = "http://$cfg_mailmanLocation/mailman/admin/$cfg_mailmanListName3" . "_" . "$cfg_mailmanLocation/members/add?subscribees=$email&adminpw=$cfg_mailmanPass&send_welcome_msg_to_this_batch=0&send_notifications_to_list_owner=0";
        echo "<iframe scrolling=no src=\"$subscribeURL\" style=\"display: block;\" width=\"500\" height=\"80\"><p>Your browser does not support iframes.</p></iframe>";
        }

}
echo "<br />...NOTE: If you are not connected to the internet, the user has NOT been added to the mailing lists.";
echo "<br /><br />Finished<br /><br />"
?>
	<a href="../customers/manage_customers.php">Back to Member List...</a>
<body>
