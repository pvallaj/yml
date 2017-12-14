import { Injectable } from '@angular/core';
@Injectable()
export class UsuarioService {
  private usuario:string;
  private nombreUsuario:string;
  private roles:string;
  private estadoSesion:boolean;
  constructor() {  
  	this.usuario='';
  	this.nombreUsuario='';
  	this.roles='';
  	this.estadoSesion=false;
  }
  setUsuario(usuario:string){
  	this.usuario=usuario;
  }
  getUsuario(){
  	return this.usuario;
  }
  setNombreUsuario(nombreUsuario:string){
  	this.nombreUsuario=nombreUsuario;
  }
  getNombreUsuario(){
  	return this.nombreUsuario;
  }
  setRoles(roles:string){
  	this.roles=roles;
  }
  getRoles(){
  	return this.roles;
  }
  setEstadoSesion(estadoSesion:boolean){
  	this.estadoSesion=estadoSesion;
  }
  getEstadoSesion(){
  	return this.estadoSesion;
  }
  obtUsuario(){
  	return this;
  }
}