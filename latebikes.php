<h3>&nbsp; Library Loans</h3>
<?

$today = date('Y-m-d');
$todaynix = date('U');
//echo "$todaynix";
$query = "SELECT *, UNIX_TIMESTAMP(duedate) as unixdate, UNIX_TIMESTAMP(loandate) as unixloan FROM libraryloans WHERE bikeout=1 ORDER BY duedate ASC";
$latequery = mysql_query("$query",$dbf->conn);

while($loanarray = mysql_fetch_array($latequery)){

	$querytwo = "SELECT first_name, last_name FROM customers WHERE id='$loanarray[userID]'";
	$listquery = mysql_query("$querytwo",$dbf->conn);
	echo mysql_error();
while($latearray = mysql_fetch_array($listquery)){
		$dayslate = round((($todaynix-$loanarray[unixdate])/60/60/24)-1, 0);
		if ($dayslate < 0){ $dayslate = abs($dayslate) . " days remaining"; 
		$latestyle = "";
		} 
		elseif ($dayslate == 0){ $dayslate = "Due today"; 
		$latestyle = "background: yellow;";
		}
		else {$dayslate .= " days late!"; 
		$latestyle = "background: red;";
		}
		echo "<div class=\"lateDiv\"><b style=\"$latestyle\">$latearray[first_name] $latearray[last_name] </b><a href=\"members/getinfo.php?userID=$loanarray[userID]\">[?]</a><br />($dayslate) <a href=\"javascript:toggleDivOL('late$loanarray[bikeID]');\">[Info +/-]</a>";

$details = "";
$details .= "This bike (#$loanarray[bikeID]) was taken out on <b>";
$details .= date('M. j, Y',$loanarray['unixloan']);
$details .= "</b> and is/was due back on <b>";
$details .= date('M. j, Y',$loanarray['unixdate']);
$details .= "</b>";
$details .= " || <a href=\"library/index.php?passbike=$loanarray[bikeID]\">Sign Bike IN</a> ";


echo "<div id=\"late$loanarray[bikeID]\" class=\"testDiv\" style=\"position: absolute; left: -4000px;\">$details</div>";

echo "</div>";
	}

}


?>


