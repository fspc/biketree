<?php session_start();
session_destroy();
header ("Location:members/signoutsubmit.php?visitID=0");//login.php");
?>