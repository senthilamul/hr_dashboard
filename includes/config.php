<?php 
//session_start();
error_reporting(0);
set_time_limit(0);
ini_set('session.cookie_lifetime',  60);
date_default_timezone_set('Asia/Kolkata');
$dbdatetime=date('Y-m-d H:i:s',strtotime('now'));
$dbdate=date('Y-m-d');
define("localhost","localhost");
//require_once("../sso/sso_config.php");
if($_SERVER['SERVER_NAME']=='dataengineering.csscorp.com')
{
	define("SERVER","dataengineering.csscorp.com");
	define("DATABASE","hr");
	define("DBUSER","ssqa");
	define("DBPASS","$!nttu@123#");
	define("SYSTEM_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/csvupload/");	
	define("CSV_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/business/attrition/");	
}
if(substr_count($_SERVER['SERVER_NAME'],localhost)>0)
{
	define("SERVER","localhost");
	define("DATABASE","hr");
	define("DBUSER","root");
	define("DBPASS","");
	define("SYSTEM_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/csvupload/");
	define("CSV_ROOT_PATH",$_SERVER["DOCUMENT_ROOT"]."/business/attrition/");
}
try{
	$conn = new PDO("mysql:host=".SERVER.";dbname=".DATABASE, DBUSER, DBPASS);
	$pdo = new PDO("mysql:host=".SERVER.";dbname=".'login', DBUSER, DBPASS);
}
catch(PDOException $e){
   echo "Connection failed: " . $e->getMessage();
}
include("common_functions.php");
$commonobj	= new common_functions();
$financialMonthArr = array("Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","Jan","Feb","Mar");

$financial_months = $commonobj->getQry("select distinct master_month from `emp_master_table`");
foreach($financial_months as $month){
	list($monthName,$yearName) = explode("/",$month['master_month']);
	$yearArr[] = $yearName;
	$monthArr[] = $monthName;
}
$yrArr = array_unique($yearArr);

foreach($yrArr as $year){
	$yr = $year;
	$yrplusone = $year+1;
	foreach($financialMonthArr as $month){
		$monthNum = date("m",strtotime($month));
		if($monthNum >=4 && $monthNum <= 12){
			$financialmonthresArr[$year][] = $month."/".$yr;
		}elseif($monthNum >=1 && $monthNum<=3){
			$financialmonthresArr[$year][] = $month."/".$yrplusone;
		}
	}
}

?>