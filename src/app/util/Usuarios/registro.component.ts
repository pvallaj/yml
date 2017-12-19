import { Component, Input }   from '@angular/core';
import { Router }             from '@angular/router';
import 'rxjs/add/operator/catch';

import { SesionUsuario }      from './SesionUsuario.service';
import { AuthService } from './auth-service.service';

@Component({
  moduleId:   module.id,
  selector:   'registro-usuario',
  templateUrl:'registro.component.html',
  styleUrls:  [ './registro.component.css']
})
export class Registro {
  @Input() tipo:string;

  submitted =    false;
  ruteador :     Router;
  closeResult:   string;
  usuario:string="";
  password:string="";

  constructor( private ru:Router, private su:SesionUsuario, private auth:AuthService) {
      console.log("llegamos al registro");
  }
  registrarUsuario() { 
    console.log("Inicio de REGISTRO de USUARIO");
    this.su.registrarUsuario(this.usuario, this.password).subscribe(data=>this.registroExitoso(data), err=>this.registroError(err));
  }
  registroExitoso(r){
    console.log("Exito");
    console.log(r);
    this.ru.navigate(['nomina']);
  }
  registroError(e){
    console.log(e);
   //this.ds.cierraEspera();
  }
  
  salir() { 
    //this.su.cerrarSesion().subscribe(data=>this.salir_r(data), err=>this.registroError(err));
    this.auth.logout();
  }
  salir_r(r){
    console.log("Cerrar sesion con exito: ");
    this.ru.navigate(['inicio']); 
  }



  ////--------------------------
  autenticar(){
    this.auth.login();
  }
  
}