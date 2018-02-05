<?php
class COMISIONES
{
    private $db;
    public $parametros;
    public $elog;

    public $resultado = array(
        "ce"      => 0,
        "mensaje" => "Error. no se encontro acción", 
        "accion"  =>"ninguna", 
        "datos"   =>"");
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    function log($texto){
      error_log($texto.PHP_EOL, 3, $this->elog);
    }
    function limpiar_dia($p_dia){
        try{
            $stmt = $this->db->prepare("DELETE FROM comisiones WHERE fecha=:p_fecha");
            $stmt->execute(array(':p_fecha'=>$p_dia));
            $this->log("Se limpio del dia ->:[".$p_dia."]");
          }catch(PDOException $e){
              log("\n2 Error Ag Venta->:[".$e->getMessage()."]");
        }    
    }

    public function agregar_comision($p_empleado,$p_nombre, $p_producto,$p_servicio,$p_comision,  $p_fecha){
       try{
          $stmt = $this->db->prepare("INSERT INTO comisiones(fecha, id, nombre, producto, servicio, comisiones) 
            VALUES(:p_fecha, :p_id, :p_nombre, :p_producto, :p_servicio, :p_comision)");
              
           $stmt->bindparam(':p_fecha',         $p_fecha,       PDO::PARAM_STR);
           $stmt->bindparam(':p_id',            $p_empleado,    PDO::PARAM_STR);
           $stmt->bindparam(':p_nombre',        $p_nombre,      PDO::PARAM_STR);
           $stmt->bindparam(':p_producto',      $p_producto,    PDO::PARAM_STR);
           $stmt->bindparam(':p_servicio',      $p_servicio,    PDO::PARAM_STR);
           $stmt->bindparam(':p_comision',      $p_comision,    PDO::PARAM_STR);  
                  
           $stmt->execute(); 
           $this->resultado['ce']=1;
           $this->resultado['mensaje']='Consulta exitosa.';
           return $stmt; 
       }catch(PDOException $e){
           $this->log("Error: ".$e->getMessage());
           $this->resultado['ce']=-1;
           $this->resultado['mensaje']='Hay un error en el proceso. Favor de revisar el log';
       }    
    }

    public function obt_concentrado($p_fi, $p_ft){
      global $resultado;
       try{
          $stmt = $this->db->prepare(
          "select ". 
          "DATE_FORMAT(a.fecha, '%d-%m-%Y') fecha, sum(a.comisiones) comisiones ".
          "from comisiones a ".
          "where a.fecha>=:p_fi and a.fecha<=:p_ft ".
          "group by a.fecha ".
          "order by a.fecha desc"); 
          $stmt->bindparam(':p_fi',        $p_fi, PDO::PARAM_STR);
          $stmt->bindparam(':p_ft',        $p_ft, PDO::PARAM_STR);
          $stmt->execute();
          $row=$stmt->fetchAll();
          $this->resultado['datos']=$row;
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='Consulta exitosa.';
          return $row;
       }
       catch(PDOException $e)
       {  
          $this->log("Error: ".$e->getMessage());
          $this->resultado['ce']=-1;
          $this->resultado['mensaje']='Hay un error en el proceso. Favor de revisar el log';
          return ;
       }
    }
    public function carga($cadena){
      global $resultado;
      $fecha="";
      $estado=0;

      $lfila=explode(PHP_EOL, $cadena);
      foreach ($lfila as $clave => $fila) {
        $datos=explode(chr(0x09),$fila);
        $dtsCero=explode(chr(0x09),$datos[0]);
        if($estado==0){
          if($datos[0]=="Sucursalfecha" || $datos[0]=="fecha"){
            $estado=1;
          }
        }
        if($estado==1){
          $tokens=explode(" ",$datos[0]);
          if(is_numeric($tokens[0])){
            $mes="";
            switch ($tokens[1]) {
              case 'ene.': $mes="01";   break;
              case 'feb.': $mes="02";   break;
              case 'mar.': $mes="03";   break;
              case 'abr.': $mes="04";   break;
              case 'may.': $mes="05";   break;
              case 'jun.': $mes="06";   break;
              case 'jul.': $mes="07";   break;
              case 'ago.': $mes="08";   break;
              case 'sep.': $mes="09";   break;
              case 'oct.': $mes="10";   break;
              case 'nov.': $mes="11";   break;
              case 'dic.': $mes="12";   break;
            }
            $fecha=trim($tokens[2])."-".$mes."-".trim($tokens[0]);
            $this->log("FECHA: ".$fecha);
            $this->limpiar_dia($fecha);
            $estado=2;
          }
        }
        if($estado==2){
          if(is_numeric(trim($datos[0]))){
            //echo '         Comision ['.trim($datos[0]).'] ['.$datos[1].'] - ['.$datos[2].'] - ['.$datos[3].'] - ['.$datos[4].'] - <br>';
               $this->agregar_comision(trim($datos[0]),$datos[1], $datos[2], $datos[3], $datos[4], $fecha); 
          }
        }
      }
      //echo 'Carga de VENTAS de '.$fecha.' Terminada';
      $this->resultado['ce']=1;
      $this->resultado['mensaje']='Consulta exitosa.';
  }
  
  
  public function procesar(){
    $this->resultado['accion']=$this->parametros['cn']->accion;
    $la=$datos=explode(':',$this->parametros['cn']->accion);
    $subaccion=$la[1];
    $this->log('Sub:'.$subaccion);
    switch($subaccion){
      case '1':{
         $x=$this->obt_concentrado($this->parametros['cn']->fi, $this->parametros['cn']->ff);
         break;
      }
      case '3':{
         $x=$this->carga($this->parametros['cn']->datos);
         if($this->resultado['ce']<0) return;
         $x=$this->obt_concentrado($this->parametros['cn']->fi, $this->parametros['cn']->ff); 
         break;
      }
    }
  } 
 
}
?>