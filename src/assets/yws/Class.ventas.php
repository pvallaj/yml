<?php
class VENTAS
{
    private $db;
    public $elog;
    public $parametros;
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

          $stmt = $this->db->prepare("DELETE FROM ventas WHERE fecha=:p_fecha");
          $stmt->bindparam(':p_fecha',          $p_dia,        PDO::PARAM_STR);
          $stmt->execute();
          $this->log('Se limpio el dia '.$p_dia);
       }
       catch(PDOException $e)
       {
           $this->log("Error al limpiar el dia".$p_fecha."-->".$e->getMessage());
       }    
    }

  public function agregar_venta($p_tipo,$p_descripcion, $p_ventas,$p_clientes,$p_cantidad, $p_subtotal, $p_comision, $p_total, $p_fecha){
       try{

          //echo "\nAntes de insert:[".$p_tipo."][", EOL;
          //error_log("antes INSERT Ag Venta ", 3, $this->$elog));
          $desc=explode(" ", $p_descripcion);
          $p_id_prod=$desc[0];
          $stmt = $this->db->prepare("INSERT INTO ventas(tipo, descripcion, ventas, clientes, cantidad, subtotal, comision, total, fecha, id_prod) 
            VALUES(:p_tipo, :p_descripcion, :p_ventas, :p_clientes, :p_cantidad, :p_subtotal, :p_comision, :p_total, :p_fecha, :p_id_prod)");
              
           $stmt->bindparam(':p_tipo',          $p_tipo,        PDO::PARAM_STR);
           $stmt->bindparam(':p_descripcion',   $p_descripcion, PDO::PARAM_STR);
           $stmt->bindparam(':p_ventas',        $p_ventas,      PDO::PARAM_INT);
           $stmt->bindparam(':p_clientes',      $p_clientes,    PDO::PARAM_INT);
           $stmt->bindparam(':p_cantidad',      $p_cantidad,    PDO::PARAM_INT); 
           $stmt->bindparam(':p_subtotal',      $p_subtotal,    PDO::PARAM_STR);
           $stmt->bindparam(':p_comision',      $p_comision,    PDO::PARAM_STR);  
           $stmt->bindparam(':p_total',         $p_total,       PDO::PARAM_STR);  
           $stmt->bindparam(':p_fecha',         $p_fecha,       PDO::PARAM_STR); 
           $stmt->bindparam(':p_id_prod',       $p_id_prod,     PDO::PARAM_INT);      
           $stmt->execute(); 
   
           return $stmt; 
       }catch(PDOException $e){
           $this->log("\n2 Error Ag Venta->:[".$e->getMessage()."]");
       }    
    }


  public function obt_concentrado($p_fi, $p_ft){
    global $resultado;
       try{
          $stmt = $this->db->prepare(
            "select ".
           "DATE_FORMAT(a.fecha, '%d-%m-%Y') fecha, sum(a.efectivo) efectivo, sum(a.tarjeta) tarjeta, sum(a.efectivo)+sum(a.tarjeta) total  ".
          "from  ".
          "(select  fecha, total efectivo, 0 tarjeta ".
          "from ventas  ".
          "where  tipo='Pago' and descripcion='EFECTIVO' and fecha>=:p_fi and fecha<=:p_ft ".
          "union ".
          "select fecha, 0 efectivo, total tarjeta ".
          "from ventas  ".
          "where  tipo='Pago' and descripcion='TARJETA' and fecha>=:p_fi and fecha<=:p_ft) a ".
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
          $this->resultado['ce']=-1;
          $this->resultado['mensaje']=$e->getMessage();
          return ;
       }
  }
  public function carga($cadena){
    global $resultado;
    $fecha="";
    $estado=0;
    error_log("\n1->:[".$cadena."]", 3, $this->elog);
    $lfila=explode(PHP_EOL, $cadena);
    foreach ($lfila as $clave => $fila) {
      error_log("\n2 Analizando->:[".$fila."]", 3, $this->elog);
      $fila=str_replace(",", "", $fila);
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
          //Se encontro la fecha.
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
          //echo $tokens[2]."-".$mes."-".$tokens[0], EOL;
          //se eliminan los registros del dia a cargar, si existen.
          error_log("\n2 se limpia la fecha->:[".$fecha."]", 3, $this->elog);
          $this->limpiar_dia($fecha);
          //echo "FECHA: ".$fecha, EOL;
          $estado=2;
        }
      }
      if($estado==2){
          if(
            trim($datos[0])=="Producto" ||  
            trim($datos[0])=="Servicio" || 
            trim($datos[0])=="Pago" || 
            trim($datos[0])=="Bonificación"){
            $datos[5]=str_replace(" ", "", $datos[5]);
            $datos[7]=str_replace(" ", "", $datos[7]);
            /*echo '         VENTA ['.trim($datos[0]).'] ['.$datos[1].'] - ['.$datos[2].'] - ['.$datos[3].'] - ['.$datos[4].'] - ['.$datos[5].'] - ['.$datos[6].'] - ['.$datos[7].'] - <br>';*/
            error_log('\n         VENTA ['.trim($datos[0]).'] ['.$datos[1].'] - ['.$datos[2].'] - ['.$datos[3].'] - ['.$datos[4].'] - ['.$datos[5].'] - ['.$datos[6].'] - ['.$datos[7].'] - <br>', 3, $this->elog);
            $this->agregar_venta(
              trim($datos[0]),$datos[1], $datos[2], $datos[3], $datos[4], $datos[5], $datos[6], $datos[7], $fecha); 
          }
      }
    }
    $this->resultado['ce']=1;
    $this->resultado['mensaje']='carga de '.$fecha.", terminada.";
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