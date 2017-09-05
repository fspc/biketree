<h3>&nbsp; Repairs Needed</h3>
<?


	$querytwo = "SELECT id, bikebrand, bikemodel, bikecolor, userID, id, inrepair, duedate, notes, bikestatus, DATE_FORMAT(duedate,'%a. %b. %e') as humandate FROM bikes WHERE putinservice='0000-00-00' ORDER BY duedate ASC";
	$listquery = mysql_query("$querytwo",$dbf->conn);
	echo mysql_error();
while($repairarray = mysql_fetch_array($listquery)){
		$queryuser = "SELECT first_name, last_name FROM customers WHERE id='$repairarray[userID]'";
		$userresult = mysql_query("$queryuser",$dbf->conn);
		while($userarray = mysql_fetch_array($userresult)){
			$yearmonth = date('Y-m-');
			$day = date('d') + 3;
			if ($day < 10){ $day = "0" . "$day"; }
			$threeday = "$yearmonth$day";

			if($repairarray[duedate] <= $threeday){ 
				$duestyle = "width: 180px; background: url('images/uhohbg.gif'); text-align: center; height: 27px; border: 1px solid #000000";
				$emstyle = "background: #FFFFFF; padding: 2px; border-bottom: 2px solid #000000";
				} else { $duestyle = "background: #cccccc; text-align: center;"; 
				$emstyle = "";
				}
	//One LAST thing.... if the bike is a library bike, make that the name...
			if($repairarray[bikestatus] == "library"){ $userarray[firstname] = "The "; $userarray[lastname] = "Lbirary" ; }
			echo "
			<div style=\"$duestyle\">
			<b><em style=\"$emstyle\";>Due on $repairarray[humandate]</em></b>
			</div><div style=\"width: 180px; background: #eeeeee; border: 1px solid #aaaaaa\">
			For: <b>$userarray[first_name] $userarray[last_name]</b> 
			A $repairarray[bikecolor] $repairarray[bikebrand] $repairarray[bikemodel]<b> (Tag Number $repairarray[id])</b><br />
			<a href=\"javascript:toggleDivOL('repair$repairarray[id]');\">[Info +/-]</a>";
echo "<div id=\"repair$repairarray[id]\" class=\"repairDiv\" style=\"position: absolute; left: -4000px;\">$repairarray[notes]";
echo "<a href=\"bikes/form_bikes.php?action=update&passbike=$repairarray[id]\">[Bike Pickup]</a>";
echo "</div>";

			
echo "			</div><br />";
		}

		if($repairarray[bikestatus] == "library" && $repairarray[inrepair] != 0){
				$duestyle = "background: #cccccc; text-align: center;"; 
				$emstyle = "";
				
	//One LAST thing.... if the bike is a library bike, make that the name...
		
			$librarybikes .= "
			<div style=\"$duestyle\">
			<b><em style=\"$emstyle\";>No Rush...</em></b>
			</div><div style=\"width: 180px; background: #eeeeee; border: 1px solid #aaaaaa\">
			For: <b>The Library</b> 
			A $repairarray[bikecolor] $repairarray[bikebrand] $repairarray[bikemodel]<b> (Tag Number $repairarray[id])</b><br />
			<a href=\"javascript:toggleDivOL('repair$repairarray[id]');\">[Info +/-]</a>";
$librarybikes .= "<div id=\"repair$repairarray[id]\" class=\"repairDiv\" style=\"position: absolute; left: -4000px;\">$repairarray[notes]</div>";

			
$librarybikes .= "			</div><br />";
		}


	}

echo "$librarybikes";


?>


