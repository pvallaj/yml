<?php
ini_set('display_errors', 'On');
require_once './DBConf.php';
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

//$user->register('Valladares','Justo', 'Paulino','pvallaj','QuetZalCoat1');
//$resultado=$user->login('pvallaj','QuetZalCoat1');
/*require_once './Class.inventario.php';
        $inventario = new INVENTARIO($DB_con);
        $resultado['datos']=$inventario->obt_pedidos('2017-01-01', '2017-12-31');

$resp=array("ce"=>1,"mensaje"=>"Proceso terminado exitosamente.", "datos"=>$resultado); 
echo json_encode($resp);*/

//creacion de nomina
require_once './Class.nominas.php';
        $inventario = new NOMINAS($DB_con);
        $resultado['datos']=$inventario->crea_nomina('Semana 39 2017', '2017-09-24', '2017-09-30');
$resp=array("ce"=>1,"mensaje"=>"Proceso terminado exitosamente.", "datos"=>$resultado); 
echo json_encode($resp);