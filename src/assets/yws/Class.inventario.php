<?php
class INVENTARIO
{
    private $db;
    public $elog;

    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
  
    
  public function obt_pedidos($p_fi, $p_ft){
       try{

          $stmt = $this->db->prepare("SELECT * FROM inv_pedido WHERE solicitado>=:p_fi and solicitado<=:p_ft");
          $stmt->bindparam(':p_fi',        $p_fi, PDO::PARAM_STR);
          $stmt->bindparam(':p_ft',        $p_ft, PDO::PARAM_STR);
          $stmt->execute();
          $row=$stmt->fetchAll();
          return $row;
       }
       catch(PDOException $e)
       {   
           return $e->getMessage();
       }    
  }
}
?>