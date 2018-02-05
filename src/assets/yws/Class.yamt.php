<?php
class YAMT
{
    private $db;
    public $parametros;
    public $this->$elog);

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
      error_log($texto.PHP_EOL, 3, $this->elog));
    }
    
    
  function limpiar_existencia_dia($p_dia){
      try{

          $stmt = $this->db->prepare("DELETE FROM existencia WHERE fecha=:p_fecha");
          $stmt->execute(array(':p_fecha'=>$p_dia));
       }
       catch(PDOException $e)
       {
           echo "Error al limpiar el dia".$p_fecha."-->".$e->getMessage();
       }    
  }
  public function agregar_existencia($p_clave,$p_descripcion, $p_unidades, $p_fecha){
       try{

          $stmt = $this->db->prepare("SELECT count(*) existe FROM c_producto WHERE clave=:p_clave");
          $stmt->execute(array(':p_clave'=>$p_clave));
          $row=$stmt->fetch(PDO::FETCH_ASSOC);
          echo "Existe: (".$row['existe'].") En el catalogo", EOL;
          if($row['existe']==0) {
              //Se egrega al catalogo
            $stmt = $this->db->prepare("INSERT INTO c_producto(clave, descripcion, precio_compra, precio_venta) VALUES(:p_clave, :p_descripcion, 0.0, 0.0)");
              
             $stmt->bindparam(':p_clave',         $p_clave,       PDO::PARAM_STR);
             $stmt->bindparam(':p_descripcion',   $p_descripcion, PDO::PARAM_STR);
             
             $stmt->execute(); 
             echo "Se agrego el producto: ".$p_descripcion, EOL;
          }

          $stmt = $this->db->prepare("INSERT INTO existencia(clave, fecha, unidades) VALUES(:p_clave, :p_fecha, :p_unidades)");
          $stmt->bindparam(':p_clave',      $p_clave,     PDO::PARAM_STR);
          $stmt->bindparam(':p_fecha',      $p_fecha,     PDO::PARAM_STR);
          $stmt->bindparam(':p_unidades',   $p_unidades,  PDO::PARAM_STR);
             
          $stmt->execute(); 
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
       }    
  }
  
  public function agregar_existencia_y($p_clave,$p_descripcion, $p_existencia, $p_fecha){
       try{

          $stmt = $this->db->prepare("SELECT count(*) existe FROM c_productos WHERE clave=:p_clave");
          $stmt->execute(array(':p_clave'=>$p_clave));
          $row=$stmt->fetch(PDO::FETCH_ASSOC);
    
          if($row['existe']>=1) {
            return ;
          }

          //error_log("\nAntes de insert:[".$p_tipo."][");
          $stmt = $this->db->prepare("INSERT INTO c_productos(clave, descripcion, existencia, precio_compra, precio_venta) VALUES(:p_clave, :p_descripcion, :p_existencia, 0.0, 0.0)");
              
           $stmt->bindparam(':p_clave',         $p_clave,       PDO::PARAM_STR);
           $stmt->bindparam(':p_descripcion',   $p_descripcion, PDO::PARAM_STR);
           $stmt->bindparam(':p_existencia',    $p_existencia,  PDO::PARAM_INT);
           $stmt->execute(); 
           
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
       }    
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
          $resultado['datos']=$row;
          $resultado['ce']=1;
          $resultado['mensaje']='Consulta exitosa.';
          return $row;
       }
       catch(PDOException $e)
       {   
          $resultado['ce']=-1;
          $resultado['mensaje']=$e->getMessage();
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

          $resultado['datos']=$row;
          $resultado['ce']=1;
          $resultado['mensaje']='Consulta exitosa.';
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
          $resultado['ce']=1;
          $resultado['mensaje']='Actualizacion de confronta exitosa.';
       }
       catch(PDOException $e)
       {
           echo "Error al limpiar el dia".$p_fecha."-->".$e->getMessage();
       }    
  }
}
?>