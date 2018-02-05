<?php
ini_set('display_errors', 'On');
class USER
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
    public function registrar($p_ap_pat,$p_ap_mat, $p_nombre,$p_correo,$p_password)
    {
       try
       {
          //select ifnull(max(id),0)+1 from usuarios
          $stmt = $this->db->prepare("select ifnull(max(id),0)+1 siguiente from usuarios");
          $stmt->execute();
          $filaId=$stmt->fetch(PDO::FETCH_ASSOC);
          $idUsuario=0;
          if($stmt->rowCount() > 0){
            $idUsuario=$filaId['siguiente'];
          }

           $new_password = password_hash($p_password, PASSWORD_DEFAULT);
          
           $stmt = $this->db->prepare("INSERT INTO usuarios(id, correo, ap_pat, ap_mat, nombre, password) 
                          VALUES(:p_id, :p_correo, :p_ap_pat, :p_ap_mat, :p_nombre, :p_password)");
              
           $stmt->bindparam(':p_id',        $idUsuario,     PDO::PARAM_INT);
           $stmt->bindparam(':p_correo',    $p_correo,      PDO::PARAM_STR);
           $stmt->bindparam(':p_ap_pat',    $p_ap_pat,      PDO::PARAM_STR);
           $stmt->bindparam(':p_ap_mat',    $p_ap_mat,      PDO::PARAM_STR);
           $stmt->bindparam(':p_nombre',    $p_nombre,      PDO::PARAM_STR);
           $stmt->bindparam(':p_password',  $new_password,  PDO::PARAM_STR);            
           $stmt->execute(); 
   
           $this->resultado['ce']=1;
           $this->resultado['mensaje']='Usuario registrado exitosamente.';
       }catch(PDOException $e){
           $this->log($e->getMessage());
           $this->resultado['ce']=-1;
           $this->resultado['mensaje']='Ocurrio un error. Favor de revisar el LOG.';
       }    
    }
 
    public function login($p_correo,$p_password)
    {
       try{
          $stmt = $this->db->prepare("SELECT password, id FROM usuarios WHERE correo=:p_correo LIMIT 1");
          $stmt->execute(array(':p_correo'=>$p_correo));
          $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
          if($stmt->rowCount() > 0){
             if(password_verify($p_password, $userRow['password'])){
                $_SESSION['user_session'] = $userRow['id'];
                $this->resultado['ce']=1;
                $this->resultado['mensaje']='Session iniciada.';
             }else{
                $this->resultado['ce']=-1;
                $this->resultado['mensaje']='Usuario o contraseña incorrecta.';
             }
          }
       }catch(PDOException $e){
           $this->log($e->getMessage());
           $this->resultado['ce']=-1;
           $this->resultado['mensaje']='Ocurrio un error. Favor de revisar el LOG.';
       }
   }
   public function obtUsuarios(){
      try{
          $stmt = $this->db->prepare("SELECT * FROM usuarios ");
          $stmt->execute();
          $this->resultado["datos"]=$stmt->fetchAll(PDO::FETCH_ASSOC);
          $this->resultado['ce']=1;
          $this->resultado['mensaje']='consulta exitosa.';
       }catch(PDOException $e){
           $this->log($e->getMessage());
           $this->resultado['ce']=-1;
           $this->resultado['mensaje']='Ocurrio un error. Favor de revisar el LOG.';
       }
   }
   public function estaRegistrado(){
      if(isset($_SESSION['user_session'])){
         $this->resultado['ce']=1;
         $this->resultado['mensaje']='Session activa.';
      }else{
         $this->resultado['ce']=-1;
         $this->resultado['mensaje']='Sin session.';
      }
   }
 
   public function redirect($url)
   {
       header("Location: $url");
   }
 
   public function cerrar_sesion()
   {
        session_destroy();
        unset($_SESSION['user_session']);
        $this->resultado['ce']=1;
        $this->resultado['mensaje']='session terminada.';
   }

   public function procesar(){
    $this->resultado['accion']=$this->parametros['cn']->accion;
    $la=$datos=explode(':',$this->parametros['cn']->accion);
    $subaccion=$la[1];
    $this->log('Sub:'.$subaccion);
    switch($subaccion){
      case '1':{
         $x=$this->login($this->parametros['cn']->usr, $this->parametros['cn']->contra);
         break;
      }
      case '2':{
         $x=$this->estaRegistrado();
         break;
      }
      case '3':{
         $x=$this->cerrar_sesion();
         break;
      }
    }
  }
}
?>