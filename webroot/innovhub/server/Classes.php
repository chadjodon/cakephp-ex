<?php
session_start();

//$baseDir = $_SERVER["DOCUMENT_ROOT"]."/";
$baseDir = $_SERVER["DOCUMENT_ROOT"]."webroot/innovhub/";
$baseURL="http://".$_SERVER['HTTP_HOST']."/innovhub/";
$baseURLSSL="http://".$_SERVER['HTTP_HOST']."/innovhub/";

$useDB = TRUE;
//$dbHost='database';
//$dbPW='UklH.np3shdrS';
//$dbName='ddbuser';
//$db='ddb';


   $dbHost = getenv(strtoupper(getenv("DATABASE_SERVICE_NAME"))."_SERVICE_HOST");
   $dbName = getenv("DATABASE_USER");
   $dbPW = getenv("DATABASE_PASSWORD");
   $db='default';

$masterkey = "C*9_k!fbai{=-,..qZe|";
$usertypeview = TRUE;

date_default_timezone_set('America/New_York');



$rootDir=$baseDir."";
$jsFolder = "js/";
$styleFolder = "style/";
$codeFolder = "server/";
$customCodeFolder = $codeFolder;

$adminFolder = "server/admin/";
$codeDir=$rootDir.$codeFolder;
$configDir=$baseDir."";
$csvuploadDir = "server/upload/";

$srvyDir = $rootDir.$codeFolder."upload/";
$srvyURL = $baseURL.$codeFolder."upload/";

ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
$defaultTheme = "0";
error_reporting(0);
//error_reporting(E_ALL);
//$printstuff = true;
$maintenance=0;      // maintenance=1 will always display a "temporary unavailable page" unless maintenance=2 parameter is passed


include $codeDir."validator.php";
include $codeDir."Context.php";
include $codeDir."TrackerArchive.php";
include $codeDir."glossary.php";
include $codeDir."survey.php";
include $codeDir."mysqlaccess.php";
include $codeDir."util.php";
include $codeDir."template.php";
include $codeDir."useracct.php";
include $codeDir."version.php";
include $codeDir."scheduler.php";
include $codeDir."WebsiteData.php";
include $codeDir."JSFXMLWriter.php";


?>
