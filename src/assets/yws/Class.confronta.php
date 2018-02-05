<?php
class CONFRONTA
{
    private $db;
    public $parametros;
    public $elog;

    public $resultado = array(
        "ce"      => 0,
        "mensaje" => "Error. no se encontro acción en 4", 
        "accion"  =>"ninguna", 
        "datos"   =>"");
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    function log($texto){
      error_log($texto.PHP_EOL, 3, $this->elog);
    }
  
  public function obt_confronta_dia($p_fi, $p_ft){
    global $resultado;
       try{
          $stmt = $this->db->prepare(
            "select id, DATE_FORMAT(v.fecha, '%Y-%m-%d') fecha, ".
            "gastos, tarjeta, propina, calculado, contado, comentarios  ".
            "from  ".
            "  confronta_diaria as cf right join  ". 
            "  (select distinct fecha  ".
            "  from ventas where fecha>=:p_fi and fecha<=:p_ft) as v on cf.fecha=v.fecha ".
            "where v.fecha>=:p_fi and v.fecha<=:p_ft order by v.fecha desc"); 
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
  public function calc_confronta_dia($p_f){
    global $resultado;
       try{
          $sentencia = $this->db->prepare("CALL cal_confronta_dia(?,false)");
          $sentencia->bindParam(1, $p_f, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 20); 
          $sentencia->execute();
          $sentencia->closeCursor();
          $stmt = $this->db->prepare(
            "select id, DATE_FORMAT(fecha, '%d-%m-%Y') fecha, ".
            "gastos, tarjeta, propina, calculado, contado, comentarios  ".
            "from  ".
            "  confronta_diaria ". 
            "where fecha=:p_f "); 
          $stmt->bindparam(':p_f',        $p_f, PDO::PARAM_STR);
          $stmt->execute();
          $row=$stmt->fetchAll();

          $this->resultado['datos']=$row;
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='Consulta exitosa.';
          return $row;
       }
       catch(PDOException $e)
       {   
          $resultado['ce']=-1;
          $resultado['mensaje']=$e->getMessage();
          return ;
       }
  }
  function actualiza_confronta_dia($p_id, $p_contado, $p_comentarios){
      try{

          $stmt = $this->db->prepare("UPDATE confronta_diaria SET contado=:p_contado, comentarios=:p_comentarios WHERE id=:p_id");
          $stmt->bindparam(':p_contado',        $p_contado,     PDO::PARAM_INT);
          $stmt->bindparam(':p_comentarios',    $p_comentarios, PDO::PARAM_STR);
          $stmt->bindparam(':p_id',             $p_id,          PDO::PARAM_INT);
          $stmt->execute();
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='Actualizacion de confronta exitosa.';
       }
       catch(PDOException $e)
       {
           echo "Error al limpiar el dia".$p_fecha."-->".$e->getMessage();
       }    
  }
  
  public function procesar(){
    $this->resultado['accion']=$this->parametros['cn']->accion;
    $la=$datos=explode(':',$this->parametros['cn']->accion);
    $subaccion=$la[1];
    $this->log('Sub:'.$subaccion);
    switch($subaccion){
      case '1':{
         $x=$this->obt_confronta_dia($this->parametros['cn']->fi, $this->parametros['cn']->ff);
         break;
      }
      case '2':{
         $x=$this->calc_confronta_dia($this->parametros['cn']->f);
         break;
      }
      case '3':{
         $x=$this->actualiza_confronta_dia($this->parametros['cn']->id, $this->parametros['cn']->contado, $this->parametros['cn']->comentarios );
         break;
      }
      
    }
  }

  

}
?>