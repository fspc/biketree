<h3><br /> Membership List </h3>

<?php
// sending query
$result = mysql_query("SELECT userID,fname,lname,email FROM users ORDER BY lname ASC");
if (!$result) {
    die("Query to show fields from table failed");
}

$fields_num = mysql_num_fields($result);

//echo "<h1>Table: {$table}</h1>";
echo "<table style=\"float: left;\" border=0 cellpadding=3 cellspacing=0><tr>";
// printing table headers
// echo "<td>First Name</td><td>Last Name</td><td>Edit User</td><td>More Info</td>";
echo "</tr>\n";
// printing table rows
$colorbit = 1;
while($row = mysql_fetch_array($result) )
{


       if ($colorbit == 1){ echo "<tr style=\"background: #DDDDDD;\">";
	$idhide = "#CCCCCC";
	$colorbit = 2;
	} else { 
	echo "<tr style=\"background: #BBBBBB;\">";
	$colorbit = 1;
	$idhide = "#999999";
 	}
   
	echo "<td>$row[lname], </td><td>$row[fname]</td>";
	echo "<td style=\"border-left: 1px solid #000000; padding-left: 6px; padding-right: 8px;\"> <a href=\"index.php?mode=users&action=add&userID=$row[userID]\">Edit Member</a></td><td style=\"border-left: 1px solid #000000;\"><a href=\"index.php?mode=users&action=getinfo&userID=$row[userID]\">More Info</a></td>"; 
    echo "</tr>\n";
}
mysql_free_result($result);
?>


