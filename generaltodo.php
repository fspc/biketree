<h3>&nbsp; General To-Do</h3>
<?


	$querytwo = "SELECT * FROM todolist WHERE completed=0";
	$todoquery = mysql_query("$querytwo",$dbf->conn);
	echo mysql_error();
while($todoarray = mysql_fetch_array($todoquery)){

			echo "
			<div style=\"background: #cccccc; text-align: center;\">
			<b><em>To Do: $todoarray[name]</em></b>
			</div><div style=\"width: 180px; background: #eeeeee; border: 1px solid #aaaaaa\">

			<a href=\"javascript:toggleDivOL('todo$todoarray[id]');\">[Info/Edit +/-]</a>";
echo "<div id=\"todo$todoarray[id]\" class=\"repairDiv\" style=\"position: absolute; left: -4000px;\">";
echo "<form name=todo$todoarray[id] enctype=\"multipart/form-data\" method=\"POST\" action=\"todosubmit.php?id=$todoarray[id]&action=update\">";
echo "<textarea name=\"content\" rows=\"12\" style=\"margin: 0px; padding: 0px;\">$todoarray[content]</textarea>";
echo "<input type=\"submit\" value=\"Save Changes\"><br /></form>";
echo "<a href=\"todosubmit.php?action=update&completed=yes&id=$todoarray[id]\">[Task Completed]</a>";
echo "</div>";

						//FORM FOR NEW TO DO ITEMS
echo "			</div><br />";
		}

			echo "
			<div style=\"background: #cccccc; text-align: center;\">
			<b><em>To Do: Add a new item</em></b>
			</div><div style=\"width: 180px; background: #eeeeee; border: 1px solid #aaaaaa\">";
echo "<form name=addtodo enctype=\"multipart/form-data\" method=\"POST\" action=\"todosubmit.php?action=insert\">";
echo "<input type=\"text\" value=\"...name goes here\" name=\"name\" size=\"16\">";
echo "<div id=\"addtodo\" class=\"repairDiv\">";
echo "<textarea name=\"content\" rows=\"8\" style=\"margin: 0px; padding: 0px;\">Description goes here..</textarea>";
echo "<input type=\"submit\" value=\"Add Item\"><br /></form>";
echo "</div>";

			
echo "			</div><br />";

?>


