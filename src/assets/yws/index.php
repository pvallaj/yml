<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: X-Requested-With');
  header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
  error_reporting(E_ALL);
  ini_set('display_errors', 'on');

  require_once './DBConf.php';
  
  error_log("idx: Iniciando".PHP_EOL, 3, $elog);
  $p1='inicial';
  $ptrms = (array)json_decode(file_get_contents('php://input'));
  $resultado['accion']=$ptrms['cn']->accion;
  $accion='';
  if(strpos($ptrms['cn']->accion, ':')){
    $la=$datos=explode(':',$ptrms['cn']->accion);
    $accion=$la[0];
  }else{
    $accion=$ptrms['cn']->accion;
  }
  error_log("idx: Parametros leidos:".$accion.PHP_EOL, 3, $elog);
  try{
    switch($accion){
      case '0':{
        error_log("idx: En usuarios ".PHP_EOL, 3, $elog);
         include_once './Class.User.php';
         $proceso = new USER($DB_con);
         break;
      }
      case '2':{
         require_once './Class.ventas.php';
         $proceso = new VENTAS($DB_con);
         break;
      }
      case '3':{ 
         require_once './Class.comisiones.php';
         $proceso = new COMISIONES($DB_con);
         break;
      }
      case '4':{ 
         require_once './Class.confronta.php';
         $proceso = new CONFRONTA($DB_con);
         break;
      }
      case '5':{ 
         require_once './Class.gastos.php';
         $proceso = new GASTOS($DB_con);
         break;
      }
      case '6':{ 
         require_once './Class.nominas.php';
         $proceso = new NOMINAS($DB_con); 
         break;
      }
      case '7':{ 
         require_once './Class.pedidos.php';
         $proceso = new PEDIDOS($DB_con);
         break;
      }
    }
  }catch(Exception $e){
    echo json_encode($e->getMessage());
    error_log("idx: ".$e->getMessage(), 3, $elog);
    return;
  }
  $proceso->parametros=$ptrms;
  $proceso->elog=$elog;
  $proceso->procesar();
  echo json_encode($proceso->resultado);

?>