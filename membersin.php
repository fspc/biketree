<h3>&nbsp; Who's In The Shop? </h3>
<SCRIPT LANGUAGE="Javascript">
  <!---
      function decision(message, url)
        {
	        if(confirm(message)){
            parent.location.href = url;
          }
        }
        // --->
</SCRIPT>

<?php


// sending query
$userresult = mysql_query("SELECT * FROM visits WHERE endout IS NULL ORDER BY activity ASC");
if (!$userresult) {
    die("Query to show fieldfhdkjshs from table failed");
}

$fields_num = mysql_num_fields($userresult);


echo "<table border=0 cellpadding=3 cellspacing=0 width=\"100%\" style=\"font-family: verdana; font-size: 12px;
\"><tr>";
echo "</tr>\n";

$colorbit = 1;
while($row = mysql_fetch_array($userresult)){
  $userID = "$row[userID]";
	$inforesult = mysql_query("SELECT first_name,last_name FROM customers WHERE id=$userID ORDER BY last_name ASC");
	$visitID = $row[visitID];

	while($info = mysql_fetch_array($inforesult)){
    $trcolour = "";//#DDDDDD";
    if($colorbit == 1){
      $trcolour = "#BBBBBB";
	    $idhide = "#CCCCCC";
  	  $colorbit = 2;
	  }else{ 
      $trcolour = "#DDDDDD";
	    $idhide = "#999999";
	    $colorbit = 1;
 	  }
    $isAdmin = false;
    $isMech = false;
    $exstyle = "";
    if($row[activity] == "Mechanic"){
      //$trcolour = "#99FF99";
      $isMech = true;
      $exstyle = "font-weight: bold;font-size: 10px;";
    }else if($row[activity] == "Administrator"){
      //$trcolour = "#66BBBB";
      $isAdmin = true;
      $exstyle = "font-weight: bold;font-size: 10px;";
    }
    //if($colorbit == 2){//trcolour != ""){
      //echo "";
    //}
  
    $result = mysql_query("SELECT id,first_name,last_name FROM customers ORDER BY last_name ASC");
  	echo "<tr style=\"background: $trcolour;\">
        <td><a href=\"members/getinfo.php?userID=$row[userID]\">$info[last_name], $info[first_name]</a></td>";
    if($isAdmin){
      echo "<td style=\"border-left: 1px solid #000000;$exstyle\">&nbsp;<em>$cfg_administratorTitle</em></td>";
      //echo "<td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\"><a href=\"books/depositPayout.php\">deposit/payout</a></td>";
      //echo "<td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\"><input type=\"submit\" value=\"new sale\"></td>";
  	  echo "<td colspan=2 align=\"center\" style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\">
        <a href=\"javascript:decision('$lang->logoutConfirm', 'logout.php')\">Log off</a></td>";
    }else if($isMech){
      echo "<td style=\"border-left: 1px solid #000000;$exstyle\">&nbsp;<em>$cfg_mechanicTitle</em></td>";
      //echo "<td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\"><a href=\"\">internal sale</a></td>";
      if($cfg_mechAutoSignin == "yes"){
  	    echo "<td colspan=2 style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\">
 	        <form name=mechswitch enctype=\"multipart/form-data\" method=\"GET\" action=\"members/signoutsubmit.php\" style=\"text-align: center; margin-bottom: 0px; padding-bottom: 0px\">
            <select name=\"switchID\">";
		    while($field = mysql_fetch_array($result)){
          echo "<option value=\"$field[id]\">$field[last_name], $field[first_name] </option> ";
        }
          echo "<input type=\"hidden\" name=\"visitID\" value=\"$visitID\"></select><br>
            <a href=\"javascript:document.mechswitch.submit();\">Switch It Up</a></form></td>";
       }else{
    	  echo "<td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\">
        <a href=\"members/signoutsubmit.php?visitID=$visitID\">Sign Out</a></td>
        <td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\">
        <a href=\"members/signin.php?visitID=$visitID&userID=$row[userID]&activity=$row[activity]&refer=membersin\">
          In/Out Retroactively</a>
        </td>"; 
       }
    }else{
      echo "<td style=\"border-left: 1px solid #000000;$exstyle\">&nbsp;<em>$row[activity]</em></td>";
  	  //echo "<td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\"><input type=\"submit\" value=\"new sale\"></td>
        echo "<td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\">
        <a href=\"members/signoutsubmit.php?visitID=$visitID\">Sign Out</a></td>
        <td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\">
        <a href=\"members/signin.php?visitID=$visitID&userID=$row[userID]&activity=$row[activity]&refer=membersin\">
            In/Out Retroactively</a>
        </td>"; 
    }
    echo "</tr>\n";
  }
}

echo "</table>";
?>


