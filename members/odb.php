<?
// OLD CODE DATABASE SHIT

include ("../settings.php");

$db_host = $cfg_server;
$db_user = $cfg_username;
$db_pwd = $cfg_password;
$database = $cfg_database;


 
if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");
 
if (!mysql_select_db($database))
    die("Can't select database");
?>
