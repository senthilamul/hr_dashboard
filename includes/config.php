<?php 
session_start();
error_reporting(0);
set_time_limit(0);
$dbdatetime=date('Y-m-d H:i:s',strtotime('now'));
$dbdate=date('Y-m-d');
define("localhost","localhost");
if($_SERVER['SERVER_NAME']=='dataengineering.csscorp.com')
{
	define("SERVER","localhost");
	define("DATABASE","hr");
	define("DBUSER","ssqa");
	define("DBPASS","$!nttu@123#");
	define("SYSTEM_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/csv/");	
	define("CSV_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/csv/");	
}
if(substr_count($_SERVER['SERVER_NAME'],localhost)>0)
{
	define("SERVER","localhost");
	define("DATABASE","hr");
	define("DBUSER","root");
	define("DBPASS","");
	define("SYSTEM_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/hr/");
	define("CSV_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/business/hr/");
}
try{
	$conn = new PDO("mysql:host=".SERVER.";dbname=".DATABASE, DBUSER, DBPASS);
	//$pdo = new PDO("mysql:host=".SERVER.";dbname=".'login', DBUSER, DBPASS);
}
catch(PDOException $e){
   echo "Connection failed: " . $e->getMessage();
}
include("common_functions.php");
$commonobj	= new common_functions();

?>