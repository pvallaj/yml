<?php
class NOMINAS
{
    private $db;
    public $parametros;
    public $elog;

    public $resultado = array(
        "ce"      => 0,
        "mensaje" => "Error. no se encontro acción", 
        "accion"  =>"ninguna", 
        "datos"   =>"",
        "id"=>0);
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    function log($texto){
      error_log($texto.PHP_EOL, 3, $this->elog);
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
           $x=$this->agregar($this->parametros['cn']->descripcion, $this->parametros['cn']->fi, $this->parametros['cn']->ff);
           break;
        }
        case '3':{
           $x=$this->eliminar($this->parametros['cn']->id);
           break;
        }
        case '4':{
           $x=$this->obt_detalle($this->parametros['cn']->id);
           break;
        }
        case '5':{
           $x=$this->calcular($this->parametros['cn']->id);
           break;
        }
      }
    } 
  public function obt_concentrado($p_fi, $p_ft){
       try{

          $stmt = $this->db->prepare("SELECT id, descripcion, DATE_FORMAT(inicio, '%d-%m-%Y') inicio, DATE_FORMAT(termino, '%d-%m-%Y') termino FROM nominas WHERE inicio>=:p_fi and inicio<=:p_ft order by id desc"); 
          $stmt->bindparam(':p_fi',        $p_fi, PDO::PARAM_STR);
          $stmt->bindparam(':p_ft',        $p_ft, PDO::PARAM_STR);
          $stmt->execute();
          $row=$stmt->fetchAll();
          $this->resultado['datos']=$row;
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='Consulta exitosa.';
       }
       catch(PDOException $e)
       {   
           return $e->getMessage();
       }    
  }
  public function eliminar($p_id){
       try{

          $stmt = $this->db->prepare("delete from nominas where id=:p_id"); 
          $stmt->bindparam(':p_id',        $p_id, PDO::PARAM_STR);
          $stmt->execute();
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='eliminacion exitosa.';
       }
       catch(PDOException $e)
       {   
           $this->log("6:3");
           $this->log($e->getMessage()) ;
       }    
  }
  public function calcular($p_id){
       try{

          $stmt = $this->db->prepare("call calcula_nomina_c(?, false, false)"); 
          //$stmt->bindparam(':p_id',        $p_id, PDO::PARAM_INT);
          $stmt->bindParam(1, $p_id, PDO::PARAM_STR); 
          $stmt->execute();
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='ejecución exitosa.';
       }
       catch(PDOException $e)
       {   
           $this->log("6:3");
           $this->log($e->getMessage()) ;
       }    
  }
  public function agregar($p_descripcion, $p_inicio, $p_termino){
       try{

           $stmt = $this->db->prepare("select ifnull(max(id),0)+1 siguiente from nominas");
          $stmt->execute();
          $filaId=$stmt->fetch(PDO::FETCH_ASSOC);
          $id=0;
          if($stmt->rowCount() > 0){
            $id=$filaId['siguiente'];
          }

           
           $stmt = $this->db->prepare("INSERT INTO nominas(id, descripcion, inicio, termino, estado) 
                          VALUES(:p_id, :p_descripcion, :p_inicio, :p_termino,0)");
              
           $stmt->bindparam(':p_id',          $id,             PDO::PARAM_INT);
           $stmt->bindparam(':p_descripcion', $p_descripcion,  PDO::PARAM_STR);
           $stmt->bindparam(':p_inicio',      $p_inicio,       PDO::PARAM_STR);
           $stmt->bindparam(':p_termino',     $p_termino,      PDO::PARAM_STR);
          
           $stmt->execute(); 
          $this->resultad['id']=$id;
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='nomina agregada exitosamente.';
       }
       catch(PDOException $e)
       {   
           return $e->getMessage();
       }    
  }
  //detalle de nomina
  public function obt_detalle($p_id){
       try{

           $stmt = $this->db->prepare("select ".
            "d.id_nomina, d.no_empl, r.id, r.descripcion, d.valor, ".
          "e.paterno, e.materno, e.nombre ".
          "from ".
          "  nomina_detalle d, ".
          "  nomina_reglas r, ".
          "empleado e ".
          "where ".
          "  d.no_regla=r.id ".
          "  and d.id_nomina=:p_id ".
          "and e.id=d.no_empl ".
          "order by d.no_empl, r.id");
           $stmt->bindparam(':p_id',          $p_id,             PDO::PARAM_INT);
          $stmt->execute();
          $row=$stmt->fetchAll();
          $this->resultado['datos']=$row;
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='Consulta exitosa.';
       }
       catch(PDOException $e)
       {   
           return $e->getMessage();
       }    
  }
}
?>