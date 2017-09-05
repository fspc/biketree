<html>
<head>
<link rel="stylesheet" type="text/css" href="../allstyles.css" />

</head>
<body>

<?php

include('odb.php');
 
if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");
 
if (!mysql_select_db($database))
    die("Can't select database");



function getmonth($m=0) {
return (($m==0 ) ? date("F") : date("F", mktime(0,0,0,$m)));
}

//get prior signin time/date
$intimeresult = mysql_query("SELECT DATE_FORMAT(intime, '%m') AS premonth, DATE_FORMAT(intime, '%d') AS preday, DATE_FORMAT(intime, '%Y') AS preyear, DATE_FORMAT(intime, '%H') AS prehour, DATE_FORMAT(intime, '%i') AS preminute FROM visits WHERE visitID='$_GET[visitID]'");

while($field = mysql_fetch_array($intimeresult)) { 
	$premonth = $field[premonth]; $preday = $field[preday]; $preyear = $field[preyear]; 
	$prehour = $field[prehour]; $preminute = $field[preminute]; 
}
//get prior signOUT time/date
$outtimeresult = mysql_query("SELECT activity, DATE_FORMAT(endout, '%m') AS premonth, DATE_FORMAT(endout, '%d') AS preday, DATE_FORMAT(endout, '%Y') AS preyear, DATE_FORMAT(endout, '%H') AS prehour, DATE_FORMAT(endout, '%i') AS preminute FROM visits WHERE visitID='$_GET[visitID]'");

while($field = mysql_fetch_array($outtimeresult)) { 
	$premonthout = $field[premonth]; $predayout = $field[preday]; $preyearout = $field[preyear]; 
	$prehourout = $field[prehour]; $preminuteout = $field[preminute]; 
	$activity = $field[activity];
}

// sending query
$result = mysql_query("SELECT id,first_name,last_name FROM customers WHERE id='$_GET[userID]'");
if (!$result) {
    die("Query to show fields from table failed");
}
$fields_num = mysql_num_fields($result);

while($field = mysql_fetch_array($result)) { $userID = $field[id]; $last_name = $field[last_name]; $first_name = $field[first_name]; }

?>

<form name=booking enctype="multipart/form-data" method="POST" action="signinsubmitretro.php">
<table class="text">
<tr>
<td valign="middle" class=high40>
<h2>Sign in/out retroactively</h2>

</td>
</tr>
<tr>
<td valign="middle" align="right" width="*" height="*">
<h3>Time/Date IN</h3>

</td>
</tr>
<tr>
<td>
   <table class="text" width="*" border="0" bordercolor="#000000" cellpadding="0" cellspacing="0">
     <tr>
   <td valign="middle" align="right" width="*"><b>Name: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;
        <? echo "<em><b>$first_name $last_name</b></em>";?>

   </td>
   </tr>  
   <tr>
   <td valign="middle" align="right" width="*"><b>Date: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;
         <select name="month">
		<?
		$month = 1;
		while( $month < 13 ) { 
			$monthname = getmonth($month);
			if ($month == $premonth){ $thismonth = "SELECTED"; } else { $thismonth = "";}
			echo "<option value=\"$month\" $thismonth>$monthname</option> "; 
			$month ++;
		}
		?>
        </select>

       <select name="day">
		<?
		$day = 1;
		while( $day < 32 ) {
			if ($day == $preday){ $thisday = "SELECTED"; } else { $thisday = "";} 
			echo "<option value=\"$day\" $thisday>$day</option> "; 
			$day ++;
		}
		?>
        </select>
        <select name="year">
		<?
		$year = date("Y")-1;
		$fun = $year;
		while( $year < $fun+5 ) { 
			if ($year == $preyear){ $thisyear = "SELECTED"; } else { $thisyear = "";}
			echo "<option value=\"$year\" $thisyear>$year</option> "; 
			$year ++;
		}
		?>
        </select>


   </td>
   </tr> 

   <tr>  
   <td valign="middle" align="right" width="*"><b>Time: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;

	<select name="hour">
		<?
		$hour = 0;
		while( $hour < 24 ) { 
			if($hour<10) { $hour = "0$hour"; } else { $hour = "$hour"; }
			if ($hour == $prehour){ $thishour = "SELECTED"; } else { $thishour = "";}
			echo "<option value=\"$hour\" $thishour>$hour</option> "; 
			$hour ++;
		}
		?>
        </select>
:
       <select name="minute">
		<?
		$minute = 00;
		while( $minute < 60 ) {
			if($minute<10) { $minute = "0$minute"; } else { $minute = "$minute"; }
			if ($minute == $preminute){ $thisminute = "SELECTED"; } else { $thisminute = "";} 
			echo "<option value=\"$minute\" $thisminute>$minute</option> "; 
			$minute ++;
		}
		?>
        </select>
(24 hour time)
   </td>
   </tr>
   


   <tr><td valign="middle" align="right" width="*"><b>&nbsp; </b></td><td valign="middle" align="left" width="*"></td>
   </tr>
   <tr>
   <td valign="middle" align="right" width="*"><b>Activity: </b><br /><br /></td>
   <td valign="middle" align="left" width="*">
   &nbsp;
<? //If it's not from the main page, grab it from the visits table	
if(isset($_GET[activity])){ 
$$_GET[activity] = "SELECTED";
} else { 
$$activity = "SELECTED";
}
?>
        <select name="activity">
		<option value="using" <? echo $using; ?>>Using the Shop</option>
		<option value="volunteering" <? echo $volunteering; ?>>Volunteering for the shop</option>
		<option value="Working" <? echo $Working; ?>>Mechanic/Admin</option>
		<option value="dogfucking" <? echo $dogfucking; ?>>Fucking the dog and drinking our beer</option>
	</select>
<br /><br />
   </td>
   </tr>
   </table>
<tr>
<td valign="middle" align="right" width="*" height="*">
<h3>Time/Date OUT</h3>


</td>
</tr>
<tr><td>
   <table class="text" width="*" border="0" bordercolor="#000000" cellpadding="0" cellspacing="0">
 
   <tr>
   <td valign="middle" align="right" width="*"><b>Date: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;
         <select name="monthout">
		<?
		$month = 1;
		while( $month < 13 ) { 
			$monthname = getmonth($month);
			if ($month == $premonthout){ $thismonth = "SELECTED"; } else { $thismonth = "";}
			echo "<option value=\"$month\" $thismonth>$monthname</option> "; 
			$month ++;
		}
		?>
        </select>

       <select name="dayout">
		<?
		$day = 1;
		while( $day < 32 ) {
			if ($day == $predayout){ $thisday = "SELECTED"; } else { $thisday = "";} 
			echo "<option value=\"$day\" $thisday>$day</option> "; 
			$day ++;
		}
		?>
        </select>
        <select name="yearout">
		<?
		$year = date("Y")-1;
		$fun = $year;
		while( $year < $fun+5 ) { 
			if ($year == $preyearout){ $thisyear = "SELECTED"; } else { $thisyear = "";}
			echo "<option value=\"$year\" $thisyear>$year</option> "; 
			$year ++;
		}
		?>
        </select>


   </td>
   </tr> 

   <tr>  
   <td valign="middle" align="right" width="*"><b>Time: </b></td>
   <td valign="middle" align="left" width="*">
   &nbsp;

	<select name="hourout">
		<?
		$hour = 0;
		while( $hour < 24 ) { 
			if($hour<10) { $hour = "0$hour"; } else { $hour = "$hour"; }
			if ($hour == $prehourout){ $thishour = "SELECTED"; } else { $thishour = "";}
			echo "<option value=\"$hour\" $thishour>$hour</option> "; 
			$hour ++;
		}
		?>
        </select>
:
       <select name="minuteout">
		<?
		$minute = 00;
		while( $minute < 60 ) {
			if($minute<10) { $minute = "0$minute"; } else { $minute = "$minute"; }
			if ($minute == $preminuteout){ $thisminute = "SELECTED"; } else { $thisminute = "";} 
			echo "<option value=\"$minute\" $thisminute>$minute</option> "; 
			$minute ++;
		}
		?>
        </select>
(24 hour time)
   </td>
   </tr>
   


   <tr><td valign="middle" align="right" width="*"><b>&nbsp; </b></td><td valign="middle" align="left" width="*"></td>
   </tr>

   </table>
</td></tr>

<tr>
<td valign="middle" align="center" width="*" height="*" style="border-bottom: 4px solid #333333; border-top: 1px dotted #333333">
<?  
if ($_GET[refer] == "membersin"){
echo "<input type=\"checkbox\" name=\"ignoreout\" id=\"ignorer\" CHECKED /> <label for=\"willcall\"><em>Check here to stay signed in (ignore signout time).</em></label><br />";
}
?>



   <input type="hidden" name="visitID" value="<? echo "$_GET[visitID]"; ?>" >
   <input type="hidden" name="userID" value="<? echo "$userID"; ?>" >
   <input type="submit" name="submit" value="Update" >&nbsp; &nbsp;
<!---   <input type="submit" name="submit" value="Sign OUT"> --->

   </td>
   </tr>
   <tr>

</table>
<br><br>
</form>	

</body>
