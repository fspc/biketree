<h3>Repairs Needed</h3>
<?


	$querytwo = "SELECT bikebrand, bikemodel, bikecolor, userID, bikeID, duedate DATE_FORMAT(duedate,'%a. %b. %e') as humandate FROM bikes WHERE bikestatus='repair' AND putinservice!='' ORDER BY duedate ASC";
	$listquery = mysql_query("$querytwo",$dbf->conn);
	echo mysql_error();
while($repairarray = mysql_fetch_array($listquery)){
		$queryuser = "SELECT first_name, last_name FROM customers WHERE id='$repairarray[userID]'";
		$userresult = mysql_query("$queryuser",$dbf->conn);
		while($userarray = mysql_fetch_array($userresult)){
			$yearmonth = date('Y-m-');
			$day = date('d') + 3;
			$threeday = "$yearmonth$day";
			if($repairarray[duedate] <= $threeday){ echo "HOLYF FUCKN SHIT"; }
			echo "
			<div style=\"width: 176px; background: $duebg;\">
			<b><em>Due on $repairarray[humandate]</em></b>
			</div><div style=\"width: 174px; background: #eeeeee; border: 1px solid #aaaaaa\">
			++ For: <a href=\"biketree/users/getinfo.php?userID=$repairarray[userID]\">$userarray[first_name] $userarray[last_name]
			</a>
			A $repairarray[bikecolor] $repairarray[bikebrand] $repairarray[bikemodel], (number $repairarray[bikeID]) needs repairs!<br />
			</div><br />";
		}
	}




?>


