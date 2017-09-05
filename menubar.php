<?php session_start();

include ("settings.php");
include ("language/$cfg_language");
include ("classes/db_functions.php");
include ("classes/security_functions.php");

//create 3 objects that are needed in this script.
$lang=new language();
$dbf=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
//echo "$cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang";
$sec=new security_functions($dbf,'Public',$lang);

$tablename = $cfg_tableprefix.'users';
$auth = $dbf->idToField($tablename,'type',$_SESSION['session_user_id']);
$userLoginName= $dbf->idToField($tablename,'username',$_SESSION['session_user_id']);
//$isopen = $sec->isOpen();

$dbf->closeDBlink();


// Display HTML--
?>

<HTML>
<HEAD>
<SCRIPT LANGUAGE="Javascript">
<!---
function decision(message, url)
{
	if(confirm(message) )
  {
    parent.location.href = url;
  }
}
// --->
</SCRIPT> 


<style type="text/css"> 
 <!-- 

 a.nav:link
 {
 	font-weight:bold;
	 font-size:9pt;
	 font-family:Verdana;
	 color:white;
	

 }
 
 a.nav:visited
 {
 	font-weight:bold;
	 font-size:9pt;
	 font-family:Verdana;
	 color:white;
 }
 
 a.nav:active
 {
 	font-weight:bold;
	 font-size:9pt;
	 font-family:Verdana;
	 color:white;
 }

 a.nav:hover
 {
	 font-size:9pt;
	 font-family:Verdana;
	 color:#CCCCCC;
	

 }

 //--> 
 </style>
<TITLE>The Bike Tree</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
</HEAD>
<BODY BGCOLOR=#FFFFFF LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0 background="images/menubar_bg.gif"><center>
<TABLE WIDTH=770 BORDER=0 CELLPADDING=0 CELLSPACING=0 style="border-collapse: collapse" bordercolor="#111111">
	<TR>
		<TD width="434" background="images/menubar_01.gif" height="78">
			<div align="center">
              <center>
              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="95%" id="AutoNumber1">
                <tr>
                  <td width="100%"><b>
                  <font face="Verdana" color="#FFFFFF" size="4">The Bike Tree</font></b></td>
                </tr>
                <tr>
                  <td width="100%">
                  <font face="Verdana" size="1" color="#FFFFFF">An open source mash up for Calgary's bike co-ops! 
                  </font></td>
                </tr>
              </table>
              </center>
            </div>
        </TD>
		<?php if($auth=="Admin") { ?>
		<TD WIDTH="62" HEIGHT="78" style="cursor: hand;" onClick="window.open('home.php','MainFrame')">
		
		<center><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href="home.php" target="MainFrame" class="nav"><?php echo $lang->home ?></a></center>
		
		</TD>
		<TD WIDTH="65" HEIGHT="78" style="cursor: hand;" onClick="window.open('customers/index.php','MainFrame')">
		
		<center><br><br>&nbsp;<a href="customers/index.php" target="MainFrame" class="nav"><?php echo $lang->customers ?></a></center>

		</TD>
		<TD WIDTH="48" HEIGHT="78" style="cursor: hand;" onClick="window.open('items/index.php','MainFrame')">
			
		<center><br><br>&nbsp;<a href="items/index.php" target="MainFrame" class="nav"><?php echo $lang->items ?></a></center>

		</TD>
		<TD WIDTH="52" HEIGHT="78" style="cursor: hand;" onClick="window.open('reports/index.php','MainFrame')">
		
		<center><br><br>&nbsp;<a href="reports/index.php" target="MainFrame" class="nav"><?php echo $lang->reports ?></a></center>

		</TD>
		<TD WIDTH="42" HEIGHT="78" style="cursor: hand;" onClick="window.open('sales/index.php','MainFrame')">
		
		<center><br><br>&nbsp;<a href="sales/index.php" target="MainFrame" class="nav"><?php echo $lang->sales ?></a></center>

		</TD>
		<TD WIDTH="42" HEIGHT="78">
		
		
		</TD>
		<TD WIDTH="47" HEIGHT="78" style="cursor: hand;" onClick="window.open('settings/index.php','MainFrame')">
	
		<center><br><br>&nbsp;<a href="settings/index.php" target="MainFrame" class="nav"><?php echo $lang->config ?></a></center>

		</TD>
		
		
	</TR>
	<?php } if($auth=="Sales Clerk") { ?>
		
		<TD background="images/menubar_sales_01.gif" WIDTH="62" HEIGHT="78">
			
		
		</TD>
	


		<TD background="images/menubar_sales_05.gif" WIDTH="47" HEIGHT="78" style="background-position: top center; cursor: hand;" onClick="window.open('home.php','MainFrame')">
			
		<center><br><br>&nbsp;<a href="home.php" target="MainFrame" class="nav"><?php echo $lang->home ?></a></center>

		</TD>
		
	
		<TD background="images/menubar_03.gif" WIDTH="74" HEIGHT="78" style="background-position: top center; cursor: hand;" onClick="window.open('customers/index.php','MainFrame')">
		
		<center><br><br>&nbsp;<a href="customers/index.php" target="MainFrame" class="nav">Members</a></center>

		</TD>
	
		
		<TD background="images/menubar_sales_04.gif" WIDTH="59" HEIGHT="78" style="background-position: top center;" onClick="window.open('library/index.php','MainFrame')">
			
		<center><br><br>&nbsp;<a href="library/index.php" target="MainFrame" class="nav">Library</a></center>		

		</TD>
		

<TD background="images/menubar_sales_02.gif" WIDTH="70" HEIGHT="78" style="background-position: top center;" onClick="window.open('bikes/index.php','MainFrame')">
			
		<center><br><br>&nbsp;<a href="bikes/index.php" target="MainFrame" class="nav">Bikes</a></center>		

		</TD>
			
		<TD background="images/menubar_sales_06.gif" WIDTH="50" HEIGHT="78" style="background-position: top center; cursor: hand;" onClick="window.open('sales/sale_ui.php','MainFrame')">
		
		<center><br><br>&nbsp;<a href="sales/sale_ui.php" target="MainFrame" class="nav"><?php echo $lang->sales ?></a></center>

		</TD>
		
	<?php } if($auth=="Report Viewer") { ?>
		
		<TD background="images/menubar_reports_01.gif" WIDTH="62" HEIGHT="78">
			
		
		</TD>
		
		<TD background="images/menubar_reports_02.gif" WIDTH="65" HEIGHT="78">
			
		
		</TD>
	
	
		<TD background="images/menubar_reports_03.gif" WIDTH="48" HEIGHT="78">
			
		
		</TD>
	
		
		<TD background="images/menubar_reports_04.gif" WIDTH="52" HEIGHT="78">
			
		
		</TD>
		
		<TD background="images/menubar_reports_05.gif" WIDTH="42" HEIGHT="78" style="cursor: hand;" onClick="window.open('home.php','MainFrame')">
		
		<center><br><br>&nbsp;<a href="home.php" target="MainFrame" class="nav"><?php echo $lang->home ?></a></center>

		
		</TD>
		
		<TD background="images/menubar_reports_06.gif" WIDTH="47" HEIGHT="78" style="cursor: hand;" onClick="window.open('reports/index.php','MainFrame')">
		
		<center><br><br>&nbsp;<a href="reports/index.php" target="MainFrame" class="nav"><?php echo $lang->reports ?></a></center>

		
		</TD>
	</TR>
	<?php } ?>
	<TR>
		<TD COLSPAN=4 width="609" bgcolor="#0A6184"style="background: #0A6184 url('images/menubar_bottom.gif')" height="22">
			<div align="center">
              <center>
              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="97%" id="AutoNumber2">
                <tr>
                  <td width="100%"><b>
                  <font face="Verdana" size="1" color="#FFFFFF">
				  <?php echo $lang->welcome ?>
				  <?php echo $userLoginName; ?>!
				  | <a href="javascript:decision('<?php echo $lang->logoutConfirm ?>','logout.php')"><font color="#FFFFFF">
                  <?php
					if($auth=="Sales Clerk"){
						echo "Log Out</a>";
						$dbf2=new db_functions($cfg_server,$cfg_username,$cfg_password,$cfg_database,$cfg_tableprefix,$cfg_theme,$lang);
						$sec2=new security_functions($dbf2,'Sales Clerk',$lang);
						//$secp=new security_functions($dbf,'Sales Clerk',$lang);
						//if($sec2->isOpen()){
						//	echo "&nbsp;|&nbsp;<a href=\"books/closeshop.php\" target=\"MainFrame\"><font color=\"#FFFFFF\">Close Shop";
						//}
					}else{
						echo "$lang->logout";
					}
				  ?></font></a></font></b></td>
                </tr>
              </table>
              </center>
            </div>
        </TD>
		<TD COLSPAN=3 width="141" bgcolor="#0A6184" height="22">
			<div align="center">
              <center>
              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="95%" id="AutoNumber3">
                <tr>
                  <td width="100%">
                  <p align="right"><b>
                  <font face="Verdana" size="1" color="#FFFFFF"><?php echo date("F j, Y"); ?></font></b></td>
                </tr>
              </table>
              </center>
            </div>
        </TD>
	</TR>
	</TABLE></center>
</BODY>
</HTML>
