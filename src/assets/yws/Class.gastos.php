<?php
class GASTOS
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
    function eliminar($p_dia, $p_monto, $p_descripcion){
        try{
            $stmt = $this->db->prepare("DELETE FROM gastos WHERE fecha=str_to_date(:p_fecha,'%d-%m-%Y') and monto=:p_monto and descripcion=:p_descripcion");
            $stmt->execute(array(':p_fecha'=>$p_dia, 'p_monto'=>$p_monto, 'p_descripcion'=>$p_descripcion));
            //$this->log("Se elimino el gasto ->:[".$p_dia."-".$p_descripcion."-".$p_monto."]");
            $this->resultado['ce']=1;
            $this->resultado['mensaje']='operación exitosa.';
          }catch(PDOException $e){
              log("\n2 Error Ag Venta->:[".$e->getMessage()."]");
        }    
    }

    public function agregar($p_dia, $p_monto, $p_descripcion){
       try{
          $stmt = $this->db->prepare("INSERT INTO gastos(fecha, monto, descripcion) 
            VALUES(:p_fecha, :p_monto, :p_descripcion)");
              
           $stmt->bindparam(':p_fecha',         $p_dia,           PDO::PARAM_STR);
           $stmt->bindparam(':p_monto',         $p_monto,         PDO::PARAM_STR);
           $stmt->bindparam(':p_descripcion',   $p_descripcion,   PDO::PARAM_STR);
           
                  
           $stmt->execute(); 
           $this->resultado['ce']=1;
           $this->resultado['mensaje']='registro exitoso.';
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
          "DATE_FORMAT(a.fecha, '%d-%m-%Y') fecha, monto, descripcion ".
          "from gastos a ".
          "where a.fecha>=:p_fi and a.fecha<=:p_ft ".
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
      case '2':{
         $x=$this->agregar($this->parametros['cn']->fecha, $this->parametros['cn']->monto, $this->parametros['cn']->descripcion);
         break;
      }
      case '3':{
         $x=$this->eliminar($this->parametros['cn']->fecha, $this->parametros['cn']->monto, $this->parametros['cn']->descripcion);
         break;
      }
    }
  } 
 
}
?>