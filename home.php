<?php session_start();

include ("settings.php");
include ("language/$cfg_language");
include ("classes/db_functions.php");
include ("classes/security_functions.php");

function getdailycash() {
	$today = date("Y-m-d");
	$total = 0;
	
	$cashresult = mysql_query("SELECT sale_total_cost FROM sales WHERE date='$today'");
	while ($casharray = mysql_fetch_array($cashresult)){ $total = $total + $casharray[sale_total_cost]; }
return $total;
}

function getdailyvisits() {
	$today = date("Y-m-d");
	return mysql_num_rows(mysql_query("SELECT DISTINCT userID FROM visits WHERE DATE_FORMAT(intime, '%Y-%m-%d')='$today' AND activity NOT IN ('Mechanic', 'Administrator')"));
}

function getmembercount() {
        return mysql_num_rows(mysql_query("SELECT DISTINCT sales.customer_id FROM sales, sales_items WHERE sales_items.item_id=1 AND sales.id=sales_items.sale_id AND DATE_ADD(sales.date, INTERVAL 1 YEAR)>=NOW()"));//SELECT id FROM customers"));
}

function getvolunteerhours() {

	/*$vquery = "SELECT *, DATE_FORMAT(endout,'%l:%i %p') as humanout, DATE_FORMAT(intime,'%b %e, %Y') as humanindate, DATE_FORMAT(intime,'%l:%i %p') as humanintime, UNIX_TIMESTAMP(intime) as unixin, UNIX_TIMESTAMP(endout) as unixout FROM visits WHERE endout IS NOT NULL AND activity!='dogfucking' AND activity!='using'";* /
	$vresult = mysql_query($vquery);
	if (!$vresult) { echo mysql_error(); }
	$totalseconds=0;
	while($row = mysql_fetch_array($vresult)){
	$timespent = $row[unixout] - $row[unixin];
	$totalseconds = $totalseconds + $timespent;
	}
	return round($totalseconds/3600);*/
	$vquery = "SELECT ROUND(SUM(TIMESTAMPDIFF(MINUTE,intime,endout))/60) AS total FROM visits WHERE activity NOT IN ('volunteering', 'Administrator', 'Mechanic');";
	$vresult = mysql_query($vquery);
	$row = mysql_fetch_array($vresult);
	return $row[total];
}

function getmonth($m=0) {
	return (($m==0 ) ? date("F") : date("F", mktime(0,0,0,$m)));
}


$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
$sec=new security_functions($dbf,'Public',$lang);


if(!$sec->isLoggedIn()){
	header ("location: login.php");
	exit();
}

$tablename = $cfg_tableprefix.'users';
$auth = $dbf->idToField($tablename,'type',$_SESSION['session_user_id']);
$first_name = $dbf->idToField($tablename,'first_name',$_SESSION['session_user_id']);
$last_name= $dbf->idToField($tablename,'last_name',$_SESSION['session_user_id']);

if(cfg_mustOpen && !$sec->isOpen()){
	header("location: books/openshop.php");
	exit();
}

$name=$first_name.' '.$last_name;
$dbf->optimizeTables();

?>
<HTML>
<head> 
<style type="text/css">
body{
font-family: verdana;
font-size: 12px;
margin:0;
padding:0;
line-height: 2em;
}

h3{
font-family: verdana;
font-size: 16px;
font-weight: bold;
background: #9aadd0;

}

#maincontainer{
width: 1000px; /*Width of main container*/
margin: 0 auto; /*Center container on page*/

}

#topsection{
background: #91a4ac;
height: 60px; /*Height of top section*/
width: 998px;
border-right: 1px dotted #000000;
border-left: 1px dotted #000000;
border-bottom: 1px dotted #000000;

}

#topsection h1{
margin: 0;
padding-top: 15px;
}

#contentwrapper{
float: left;
width: 100%;
}

#contentcolumn{

margin-left: 200px; /*Margin for content column. Should be (RightColumnWidth + LeftColumnWidth)*/
margin-right: 200px; 
}

#leftcolumn{

border-right: 1px dotted black;
float: left;
width: 200px; /*Width of left column in pixel*/
margin-left: -1000px; /*Set left margin to -(MainContainerWidth)*/
background: #FFFFFF;
}

#rightcolumn{

border-left: 1px dotted black;
float: left;
width: 199px; /*Width of right column in pixels*/
margin-left: -400px; /*Set right margin to -(MainContainerWidth - LeftColumnWidth)*/
background: #FFFFFF;
}


#rightercolumn{

border-left: 1px dotted black;
float: left;
width: 198px; /*Width of right column in pixels*/
margin-left: -200px; /*Set right margin to -(MainContainerWidth - LeftColumnWidth)*/
background: #FFFFFF;
}

#footer{
clear: left;
width: 100%;
background: black;
color: #FFF;
text-align: center;
padding: 4px 0;
}

#footer a{
color: #FFFF80;
}

.innertube{
margin: 10px; /*Margins for inner DIV inside each column (to provide padding)*/
margin-top: 0;
}


.lateDiv {
	width: 180px;
	padding: 2px 2px 8px 2px;
	background-color: #BBBBBB;
	color: #000000;
	border-top: 4px solid #000000;
	border-right: 1px solid #000000;
	border-bottom: 0px solid #000000;
	border-left: 1px solid #000000;

}
.testDiv {
	width: 180px;
	padding: 2px;
	margin-left: -2px;
	background-color: #FFFFFF;
	color: #000000;
	border-top: 1px dashed #000000;
	border-right: 0px solid #000000;
	border-bottom: 1px solid #000000;
	border-left: 0px solid #000000;

}
.repairDiv {
	width: 176px;
	padding: 2px;
	margin-left: -1px;
	background-color: #FFFFFF;
	color: #000000;
	border-top: 1px dashed #000000;
	border-right: 1px solid #000000;
	border-bottom: 1px solid #000000;
	border-left: 1px solid #000000;

}
</style>

<script type="text/javascript">


function toggleDivOL( elemID )
{
	var elem = document.getElementById( elemID );
	if( elem.style.position != 'absolute' )
	{
		elem.style.position = 'absolute';
		elem.style.left = '-4000px';
	}
	else
	{
		elem.style.position = 'relative';
		elem.style.left = '0px';
	}
}
</script>


</head>
<body>
<?php 
if($auth=="Admin") 
{ 
?>
<p>
<img border="0" src="images/home_print.gif" width="33" height="29" valign="top"><font color="#005B7F" size="4">&nbsp;<b><?php echo $lang->home ?></b></font></p>
<p><font face="Verdana" size="2"><?php echo "$lang->welcomeTo $cfg_company's -BikeTree- bike co-op management software."; ?> </font></p>
<ul>
  <li><font face="Verdana" size="2"><a href="<?php echo "backupDB.php?onlyDB=$cfg_database&StartBackup=complete&nohtml=1"?>" ><?php echo $lang->backupDatabase ?></a></font></li>
  <li><font face="Verdana" size="2"><a href="sales/sale_ui.php"><?php echo $lang->processSale ?></a></font></li>
  <li><font face="Verdana" size="2"><a href="users/index.php"><?php echo $lang->addRemoveManageUsers ?></a></font></li>
  <li><font face="Verdana" size="2"><a href="customers/index.php"><?php echo $lang->addRemoveManageCustomers ?></a></font></li>
  <li><font face="Verdana" size="2"><a href="items/index.php"><?php echo $lang->addRemoveManageItems ?></a></font></li>
  <li><font face="Verdana" size="2"><a href="reports/index.php"><?php echo $lang->viewReports ?></a></font></li>
  <li><font face="Verdana" size="2"><a href="settings/index.php"><?php echo $lang->configureSettings ?></a></font></li>
  <li><font face="Verdana" size="2"><a href="http://forums.phppointofsale.com"><?php echo $lang->viewOnlineSupport ?></a><br>&nbsp;</font></li>

</ul>
<?php } elseif($auth=="Sales Clerk") { ?>
<!---
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" 

bordercolor="#111111" width="550" id="AutoNumber1">
  <tr>
    <td width="37">
    <img border="0" src="images/home_print.gif" width="33" height="29"></td>
    <td width="513"><font face="Verdana" size="4" color="#336699"><?php echo "$name 
    $lang->home" ?></font></td>
  </tr>
</table>
--->
<div id="maincontainer">

<div id="topsection"><div class="innertube"><b style="
border-top: 1px solid #FFFFFF; 
border-left: 1px solid #FFFFFF; 
border-right: 1px solid #000000; 
border-bottom: 1px solid #000000; 
background: #CCCCCC; 
padding: 0px 2px 2px 2px;">Quick Tasks & Stats</b>
<span style="font-weight: bold; text-align: left; padding-left: 50px;">Today's Cash: $<? echo getdailycash(); ?></span><span style="font-weight: bold; text-align: left; padding-left: 50px;">Visitors Today: <? echo getdailyvisits(); ?></span><span style="font-weight: bold; text-align: left; padding-left: 50px;">Current Member Count: <? echo getmembercount(); ?></span><span style="font-weight: bold; text-align: left; padding-left: 50px;">Total Volunteer Hours: <? echo getvolunteerhours(); ?></span><div style="width: 980px; line-height: 0; border-bottom: 1px dashed #000000"> </div>


<?
// sending query
$userLogin = $_SESSION['session_user_id'];
$data = $dbf->idToField($cfg_tableprefix.'users', 'settings', $userLogin);
$firstlast = $data;// & 1);
$query = "SELECT id,first_name,last_name FROM customers ORDER BY ";
$sortedlink = "<a href=\"settingsupdate.php?mask=1";
//echo "$firstlast";
if($firstlast){
	$query.= "first_name ASC";
	$sortedlink.= "&op=1\">First
	 Last</a>";
}else{
	$query.= "last_name ASC";
	$sortedlink.= "\">Last, First</a>";
}
$result = mysql_query($query);
if (!$result) {
    die("Query to show fields from table failed");
}
$fields_num = mysql_num_fields($result);
?>

	<!--div style="text-align: left;margin-top=-5px;padding-top=-10px;font-size=5px;">Last, First</div-->
<div width="450px" style="float: left;">
<form name=booking enctype="multipart/form-data" method="POST" action="members/signinsubmit.php" style="text-align: right; margin-bottom: 0px; padding-bottom: 0px">
<font face="Verdana" size="2">Sign In (<span style="font-size: 9px;"><? echo "$sortedlink"; ?></span>):</font>
    <select name="userID">
		<?
		while($field = mysql_fetch_array($result)) {
			if($firstlast){
				echo "<option value=\"$field[id]\">$field[first_name] $field[last_name] </option> ";
			}else{
				echo "<option value=\"$field[id]\">$field[last_name], $field[first_name] </option> ";
			}
		}
		?>
        </select>
        <font face="Verdana" size="2">Doing:</font>
        <select name="activity">
		<? if(!$sec->isMechanicHere()){ echo "<option value=\"Mechanic\" SELECTED>Mechanic</option><option value=\"using\">";}else{echo "<option value=\"using\" SELECTED>";} ?>
		Using the Shop</option>
		<option value="volunteering">Volunteering</option>
		<!--option value="Working">Mechanic/Admin</option-->
		<option value="dogfucking">Hanging Out</option>
		<option value="train_mech">Mechanic Training</option>
	</select>
   <input type="submit" name="submit" value="Sign IN" >

	</form>
	</div>
	<div width="450px"><form name=booking enctype="multipart/form-data" method="POST" action="library/form_library.php" style="text-align: right;">
	<font face="Verdana" size="2">Library sign in/out</font>
	&nbsp;<input type="text" name="bikeID" value="Bike Number..." size="10" onfocus="this.value = '';">
	<input type="submit" name="submit" value="Ok Go!">
</form>


</div></div>

<div id="contentwrapper">
	<div id="contentcolumn">
		<div class="innertube"> <br /><? include('membersin.php'); ?></div>
	</div>
</div>

<div id="leftcolumn">
	<div class="innertube"><br /><? include('latebikes.php'); ?></div>
	<!--div class="innertube"><br /><? include('repairsneeded.php'); ?></div -->

</div>

<!--div id="rightcolumn">
<div class="innertube"><br /><? include('repairsneeded.php'); ?></div>
</div-->

<div id="rightercolumn">
	<div class="innertube"><br /><? include('generaltodo.php'); ?></div>
</div>



</div>



<?php


}
else
{
?>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" 

bordercolor="#111111" width="550" id="AutoNumber1">
  <tr>
    <td width="37">
    <img border="0" src="images/home_print.gif" width="33" height="29"></td>
    <td width="513"><font face="Verdana" size="4" color="#336699"><?php echo "$name 
    $lang->home"?></font></td>
  </tr>
</table>
<p><font face="Verdana" size="2"><?php echo "$lang->welcomeTo $cfg_company $lang->reportViewerHomeWelcomeMessage"; ?>


<?php
}
$dbf->closeDBlink();

?>
