<?php
//echo 'bien', EOL; 
session_start();
//echo '2-', EOL; 
$DB_host = "localhost";
$DB_user = "yamellwf_yamel";
$DB_pass = "_yamel_texcoco_;34567";
$DB_name = "yamellwf_yamel";
$elog   = "/opt/lampp/htdocs/yws/error.log";
//echo '3-', EOL; 
try
{
     $DB_con = new PDO("mysql:host={$DB_host};dbname={$DB_name};charset=utf8",$DB_user,$DB_pass);
     //echo '4-', EOL; 
     $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     //echo '5-', EOL; 
}
catch(PDOException $e)
{
     echo $e->getMessage();
     error_log("Error al conectarse a la BD".$e.PHP_EOL, 3, $elog);
}

