import { Injectable }   from '@angular/core';
import { Subject } 		from 'rxjs/Subject';
import { Observable }   from 'rxjs/Observable';
import { isDevMode } 	from '@angular/core';

import { Coneccion	} 	from '../Coneccion.service';


@Injectable()
export class SesionUsuario{
	
	public usuario:string;
    public nombreUsuario:string;
    public acceso:string;
    public periodo:string;

    private roles:string;
    public estadoSesion:string='conectado';
    public mensajeError:string='' ;

    public sesion= new Subject<any>();
	constructor(private cnx:Coneccion){
		this.usuario='';
	  	this.nombreUsuario='';
	  	this.roles='';
	}

	registrarUsuario(usr:string, paswd:string):Observable<any>{
		console.log('Iniciando Sesión de usuario.');
		let cuerpo=	{
			usr:usr,
			contra:paswd,
			accion:'0:1'
		};
		this.cnx.ejecutar(cuerpo).subscribe((resp:any)=>this.respuesta(resp),(ru:any)=>this.error(ru));
		return this.sesion.asObservable();
	}

	validaSesion():Subject<any>{
		this.cnx.ejecutar({
			accion:'0:2'
		}).subscribe((resp:any)=>this.respuesta(resp),(ru:any)=>this.error(ru));
		return this.sesion;
	}

	cerrarSesion():Observable<any>{
		this.cnx.ejecutar({ 
			accion:'0:3'
		}).subscribe((resp:any)=>this.respuesta(resp),(ru:any)=>this.error(ru));
		return this.sesion;
	}
	private respuesta(r:any){
		console.log(r);
		if(r.ce>0){
			if(r.accion=='0:1' || r.accion=='0:2'){
				this.estadoSesion='conectado'
				console.log(this);
				this.sesion.next(r);
			}
			if(r.accion=='0:3'){
				this.estadoSesion='desconectado'
				this.sesion.next(r);
			}
		}else{
			this.sesion.error({ce:-1, descripcion:"Imposible localizar al servidor"});		
		}
	}

	private error (error: any) {
		this.sesion.error({ce:-1, descripcion:"Fallo completo", desc:error});
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
	setEstadoSesion(estadoSesion:string){
	  	this.estadoSesion=estadoSesion;
	}
	getEstadoSesion(){
	  	return this.estadoSesion;
	}
	obtUsuario(){ 
	  	return this;
	}
	validaPermiso(rol:string){
		if(this.roles.indexOf(rol,0)>=0)
			return true;
		else
			return false;
	}
}

