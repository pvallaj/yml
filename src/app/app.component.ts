import { Component } from '@angular/core';
import { SesionUsuario } from './util/Usuarios/SesionUsuario.service';
import { Router } from '@angular/router';
import { AuthService } from './util/Usuarios/auth-service.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  titulo = 'app';
  subtitulo='subtitle';
  mnActivo:boolean=false;
  opciones=[
  	{etiqueta:'Pedidos',   tt:'Registro de pedidos realizados', accion:'pedidos',     icono:'shopping_cart'},
  	{etiqueta:'Carga',     tt:'Carga de datos',                 accion:'carga',       icono:'date_range'},
    {etiqueta:'Confronta', tt:'Confronta diaria',               accion:'confronta',   icono:'compare_arrows'},
    {etiqueta:'Gastos',    tt:'Registro de gastos',             accion:'gastos',      icono:'attach_money'},
    {etiqueta:'Nominas',   tt:'Calculo de nómina semanl',       accion:'nomina',      icono:'local_atm'}
  ];
  constructor(public su: SesionUsuario,
  			  private ru:Router,
          private auth:AuthService) { 
    auth.handleAuthentication();

  	console.log('--------------');
    console.log(auth.isAuthenticated());
    console.log('--------------');
  	console.log(su);
  } 

  ADMenu(){
  	this.mnActivo=!this.mnActivo;
  }
  accionMenu(a){
  	this.ru.navigate([a]);
  }
}
