<?php
class PEDIDOS
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
           $x=$this->agregar($this->parametros['cn']->descripcion, $this->parametros['cn']->fp); 
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

        case '6':{
           $x=$this->obtCatProducto(); 
           break;
        }
      }
    } 
  public function obt_concentrado($p_fi, $p_ft){
       try{

          $stmt = $this->db->prepare("SELECT id, descripcion, DATE_FORMAT(solicitado, '%d-%m-%Y') solicitado, DATE_FORMAT(entregado, '%d-%m-%Y') entregado FROM inv_pedido WHERE solicitado>=:p_fi and solicitado<=:p_ft order by id desc"); 
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
          $stmt = $this->db->prepare("delete from inv_det_pedido_entregado where id_pedido=:p_id"); 
          $stmt->bindparam(':p_id',        $p_id, PDO::PARAM_STR);
          $stmt->execute();
          $stmt = $this->db->prepare("delete from inv_det_pedido_entregado where id_pedido=:p_id"); 
          $stmt->bindparam(':p_id',        $p_id, PDO::PARAM_STR);
          $stmt->execute();
          $stmt = $this->db->prepare("delete from inv_pedido where id=:p_id"); 
          $stmt->bindparam(':p_id',        $p_id, PDO::PARAM_STR);
          $stmt->execute();
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='eliminacion exitosa.';
       }
       catch(PDOException $e)
       {   
           $this->log("7:eliminar");
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
           $this->log("Pedidos:...");
           $this->log($e->getMessage()) ;
       }    
  }
  public function agregar($p_descripcion, $p_solicitado){
       try{

          $stmt = $this->db->prepare("select ifnull(max(id),0)+1 siguiente from inv_pedido");
          $stmt->execute();
          $filaId=$stmt->fetch(PDO::FETCH_ASSOC);
          $id=0;
    
          $id=$filaId['siguiente'];

          $stmt = $this->db->prepare("INSERT INTO inv_pedido(id, descripcion, solicitado, entregado) 
                          VALUES(:p_id, :p_descripcion, :p_solicitado,null)");
              
          $stmt->bindparam(':p_id',          $id,             PDO::PARAM_INT);
          $stmt->bindparam(':p_descripcion', $p_descripcion,  PDO::PARAM_STR);
          $stmt->bindparam(':p_solicitado',  $p_solicitado,   PDO::PARAM_STR);
          
           $stmt->execute(); 
          $this->resultado['id']=$id;
          $this->resultado['descripcion']=$p_descripcion;
          $this->resultado['solicitado']=$p_solicitado;
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='Pedido agregado exitosamente.';
       }
       catch(PDOException $e)
       {   
           $this->log("Pedidos:agregar");
           $this->log($e->getMessage()) ; 
       }    
  }

  public function obt_detalle($p_id){
       try{

           $stmt = $this->db->prepare("select ".
            "ds.id_prod, cp.descripcion producto, ds.cantidad, cp.precio_compra precio ".
          "from ".
          "  inv_det_pedido_solicitado ds, ".
          "  c_producto cp ".
          "where ".
          "  ds.id_prod=cp.clave".
          "  and ds.id_pedido=:p_id ".
          "order by ds.id_prod");
           $stmt->bindparam(':p_id',          $p_id,             PDO::PARAM_INT);
          $stmt->execute();
          $row=$stmt->fetchAll();
          $this->resultado['datos']=$row;
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='Consulta exitosa.';
       }
       catch(PDOException $e)
       {   
          $this->log("Pedidos:obt_detalle");
          $this->log($e->getMessage()) ;
       }    
  }

  public function obtCatProducto(){
       try{

          $stmt = $this->db->prepare("SELECT clave, descripcion, precio_compra FROM c_producto order by descripcion"); 
          $stmt->execute();
          $row=$stmt->fetchAll();
          $this->resultado['productos']=$row; 
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