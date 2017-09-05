<?php

class security_functions
{	
  var $conn;
	var $lang;
	var $tblprefix;
	
	//defalt constructor which first checks if page is accessable.
	function security_functions($dbf,$page_type,$language)
	{
		//pre: $dbf must be a db_functions object and $page_type must be a string
		//post: denies access to page and stops php processing
		
		//$page_type will be either: Public, Admin, Sales Clerk or Report Viewer.
		//$usertype will be either: Admin, Sales Clerk or Report Viewer.
		//Their must be a session present in order to execute authoization.
		
		//sets class variables.
		$this->conn=$dbf->conn;
		$this->lang=$language;
		$this->tblprefix=$dbf->tblprefix;
		
		if(isset($_SESSION['session_user_id']))
		{
			$user_id=$_SESSION['session_user_id'];
			
			$tablename="$this->tblprefix".'users';
			$result = mysql_query("SELECT * FROM $tablename WHERE id=\"$user_id\"",$this->conn);
      //echo "$result";
			$row = mysql_fetch_assoc($result);
			$usertype= $row['type'];
      //echo "stupid";
			
			
			//If the page is not public or the user is not an Admin, investigation must continue.
			if($page_type!='Public' or $usertype!='Admin')
			{
				if($usertype!='Admin' and $usertype!='Sales Clerk' and $usertype!='Report Viewer')
				{
					//makes sure $usertype is not anything but Admin, Sales Clerk, Report Viewer

					echo "{$this->lang->attemptedSecurityBreech}";
					exit();
				}
				elseif($page_type!='Public' and $page_type!='Admin' and $page_type!='Sales Clerk' and $page_type!='Report Viewer')
				{
					//makes sure $page_type is not anything but Public, Admin, Sales Clerk or Report Viewer.

					echo "{$this->lang->attemptedSecurityBreech}";				
					exit();
				
				}
				elseif($usertype!='Admin' and $page_type=='Admin')
				{
					//if page is only intented for Admins but the user is not an admin, access is denied.

					echo "{$this->lang->mustBeAdmin}";				
					exit();	
				}
				elseif(($usertype=='Sales Clerk') and $page_type =='Report Viewer')
				{
					//Page is only intented for Report Viewers and Admins.
					
					echo "{$this->lang->mustBeReportOrAdmin}";				
					exit();
				}
				elseif(($usertype=='Report Viewer') and $page_type =='Sales Clerk')
				{
					//Page is only intented for Sales Clerks and Admins.
					
					echo "{$this->lang->mustBeSalesClerkOrAdmin}";				
					exit();
				}
			}
		}
    /*if(!$this->isLoggedIn()){
	    header("location: ../login.php");
	    exit();
    }
    if(!$this->isOpen()){
	    header("location: ../books/openshop.php");
	    exit();
    }*/
	}
	
	function isLoggedIn()
	{
		//returns boolean based on if user is logged in.
		
		if(isset($_SESSION['session_user_id']))
		{
			$user_id=$_SESSION['session_user_id'];
			$tablename="$this->tblprefix".'users';
			$result = mysql_query ("SELECT * FROM $tablename WHERE id=\"$user_id\"",$this->conn);
			$num = @mysql_num_rows($result);
			if($num> 0)
			{
				return true;
			}
			else
			{
			
				return false;
			}
		}
		return false;
	}
	
	function checkLogin($username,$password)
	{
		//pre: $username and $password must be strings. ($password is encrypted)
		//post: returns boolean based on if their login was succesfull.
		
		$tablename="$this->tblprefix".'users';
		$result = mysql_query ("SELECT * FROM $tablename WHERE username=\"$username\" and password=\"$password\"",$this->conn);	
		$num = @mysql_num_rows($result);
		
		if($num > 0)
		{
			return true;
		}
		
		return false;
	}

	function closeSale()
	{
		//deletes sessions vars 
      session_unregister('items_in_sale'); 
    	session_unregister('current_sale_customer_id'); 
    	session_unregister('current_item_search'); 
    	session_unregister('current_customer_search'); 
	}

	function checkMembership($userID)
	{
	  global $cfg_membershipID;
	  // Construct the join query
	  $memquery =  "SELECT sales.id, sales_items.sale_id, sales_items.item_id, DATE_ADD( sales.date, INTERVAL 1 YEAR ) AS expires
		FROM sales, sales_items
		WHERE sales.id = sales_items.sale_id
		AND sales_items.item_id=$cfg_membershipID
		AND sales.customer_id=$userID
		ORDER BY sales.date DESC
		LIMIT 1;";
	  //"SELECT sales.id, sales_items.sale_id, sales_items.item_id, DATE_ADD(sales.date, INTERVAL 1 YEAR) as expires ".
  	  //"FROM sales, sales_items "."WHERE sales.id = sales_items.sale_id AND sales_items.item_id = '$cfg_membershipID' AND sales.customer_id = '$userID'";
	  $memresult = mysql_query($memquery) or die(mysql_error());
		
	if(mysql_num_rows($memresult) < 1){
		return false;
	}
    // Get expiry date
	  $today = date('Y-m-d');
	  $row = mysql_fetch_array($memresult);
	  $expires = $row['expires'];
    if($row[item_id] == "1" && $expires >= $today){
      return true;
    }else{
      return false;
    }
  }

	function checkWaiver($userID)
	{
        // If Membership is ok, check waiver
        $waiverresult = mysql_query("SELECT waiver FROM customers WHERE id='$userID'");
        if (!$waiverresult) { die("Query to check on status of liability waiver failed"); }
        while ($waiverrow = mysql_fetch_array($waiverresult)) {
        if ($waiverrow[waiver] == 0 || $waiverrow[waiver] == ""){ return false; } else { return true; }
        }



	}

	function signinMember($userID, $intime, $activity)
  {
    global $cfg_reqmembership;
    $isinresult = mysql_query("SELECT userID FROM visits WHERE endout IS NULL");
    if (!$isinresult) { die("Query to show fields from table failed"); }

    while($isinrow = mysql_fetch_array($isinresult)){
	    if($userID == "$isinrow[userID]"){
        die("<b>Bike Error!! User is already signed in...</b>");
      }
	  }




						    // MAKE SURE THEY'VE PAID THEIR MEMBERSHIP (IF REQUIRED BY CONFIG FILE) 
    if($cfg_reqmembership == "1" && !$this->checkMembership($userID)){
      echo "Membership not paid or expired!<br /><a href=\"../home.php\">Go Home --&gt;</a>";
      die('');
    } 

						    // Have you been a naughty schoolchild and not signed your waiver?  PUNISH!
    if(!$this->checkWaiver($userID)){
      echo "Waiver not signed. Sign waiver, or no shop access you naughty boy!<br /><a href=\"../home.php\">Go Home --&gt;</a>";
      die('');
    }



						    // ADD IT TO THE VISITS DATABASE

    $in = mktime($_POST[hour], $_POST[minute], 0, $_POST[month], $_POST[day], $_POST[year]);
    $tdin = date('Y-m-d H:i:s');
    //$activity = $_POST[activity];

    if($userID){
      $query = "INSERT INTO `visits` (`userID` ,`intime` ,`activity`) VALUES ('$userID', '$tdin', '$activity')";
    // echo "IT FJDSFDSA $query";   
      mysql_query($query);
    }
  }

  function isOpen()
  {
    //include("settings.php");
    //echo "must open = $cfg_company";
    //if($cfg_mustOpen == "yes"){
    //echo "$this->conn";
      //return false;
    //}
      //return false;
			//$tablename="$this->tblprefix".'users';
			//$result = mysql_query("SELECT * FROM $tablename WHERE id=\"$user_id\"",$this->conn);

      /*$today = date("Y-m-d");
      $le = mysql_query("SELECT event, date FROM books WHERE event='1' OR event='2' ORDER BY listID DESC LIMIT 1", $this->conn);
      //$le = mysql_query("SELECT * FROM books");//, $this->conn) or die(mysql_error());// WHERE event='1' OR event='2' ORDER BY listID DESC LIMIT 1", $this->conn);
      $lastevent = mysql_fetch_assoc($le);
      if(!$lastevent || $lastevent['event'] == 2 || $lastevent[date] != $today){// || !mysql_num_rows(mysql_query("SELECT * FROM books WHERE date='$today' AND event='1'"))){
        return false;
      }*/return true;
    //}
    return true;
  }
  
  function isMechanicHere()
  {
    return mysql_fetch_array(mysql_query("SELECT userID FROM visits WHERE endout IS NULL AND activity='Mechanic'"));
  }

 
  function vaildMailman ($host)
  {
  $valid = @fsockopen("$host", 80, $errno, $errstr, 30);
  if ($valid) return TRUE;

  }

}

?>
