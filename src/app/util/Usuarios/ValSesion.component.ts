import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

import { SesionUsuario } from './SesionUsuario.service'; 

@Component({
  moduleId:  module.id, 
  selector: 'valsesion-component',
  templateUrl: 'valSesion.component.html'
  ,styleUrls:[  'valSesion.component.css']
})
export class ValSessionComponent implements OnInit{

	constructor(private su: SesionUsuario, private ruteador:Router) { 
    console.log("Validando la sesion");
  }
  ngOnInit(){
  		console.log("Validando SESION!!!!");
  		this.su.validaSesion().subscribe(resp=>this.registroExitoso(),ru=>this.registroError());
  }
  registroExitoso(){
	   console.log("Con exito: "+this.su.estadoSesion);
     console.log(this.su);
	   if(this.su.estadoSesion=='conectado')  this.ruteador.navigate(["inicio"]); 
	   else this.ruteador.navigate(['inicio']); 
	}
	registroError(){
		this.su.estadoSesion="desconectado";
		this.ruteador.navigate(['inicio']); 
	}
}