import { Component
	    ,OnInit
	    ,Input
	    ,Output } 		from '@angular/core';
import { Coneccion }    from '../util/Coneccion.service';
import { UtilS }      	from '../util/util-s.service';

import {DataSource} from '@angular/cdk/collections';
import {Observable} from 'rxjs/Observable';

import 'rxjs/add/observable/of';

@Component({
  selector: 'app-gastos',
  templateUrl: './gastos.component.html',
  styleUrls: ['./gastos.component.css']
})
export class GastosComponent implements OnInit {
  fi:Date;
  ff:Date;
  fg:Date;
  registros:any;
  regtmp:{fecha:string,monto:number,descripcion:string};
  constructor(private cnx:Coneccion, private us:UtilS) {
    let hoy:Date=new Date();
    this.fg=hoy;
    this.fi=new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    this.ff=new Date(hoy.getFullYear(), hoy.getMonth()+1, 0);
  }


  ngOnInit() {
    this.cnx.ejecutar({
        accion:'5:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaInicio(f:any){
      this.cnx.ejecutar({
        accion:'5:1',
        fi:this.us.DateACadena(f),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaTermino(f:any){
      this.cnx.ejecutar({
        accion:'5:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(f),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }

  obtConcentradoResp(resp){
    console.log(resp);
    this.registros=resp.datos;
  }

  enviar(dts){
    this.cnx.ejecutar({
      accion:'3:3',
      fi:this.us.DateACadena(this.fi),
      ff:this.us.DateACadena(this.ff),
    }).subscribe((resp:any)=>this.cargaResp(resp),(ru:any)=>this.error(ru));
  }

  cargaResp(resp){
    console.log(resp);
    this.registros=resp.datos;
  }
  cambiaTipo(){
    console.log("Se cambko el tipo");
     this.cnx.ejecutar({
        accion:'3:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }

  agregarGasto(f){
  	console.log(f);
  	this.regtmp={
  		fecha:this.us.DateACadena(f.fg),
        monto:f.monto,
        descripcion:f.concepto
  	}
  	 this.cnx.ejecutar({
        accion:'5:2',
        fecha:this.us.DateACadena(f.fg),
        monto:f.monto,
        descripcion:f.concepto
      }).subscribe((resp:any)=>this.agregarGastoResp(resp),(ru:any)=>this.error(ru));
  }

  agregarGastoResp(r){
  	if(r.ce>0){
  		this.registros.push(this.regtmp);
  		this.regtmp=null;
  	}
  }

  borrarGasto(f){
  	console.log(f);
  	this.regtmp={
  		fecha:f.fecha,
        monto:f.monto,
        descripcion:f.concepto
  	}
  	 this.cnx.ejecutar({
        accion:'5:3',
        fecha:f.fecha,
        monto:f.monto,
        descripcion:f.descripcion
      }).subscribe((resp:any)=>this.borrarGastoResp(resp),(ru:any)=>this.error(ru));
  }

  borrarGastoResp(r){
  	if(r.ce>0){
  		this.registros=this.registros.filter(item => (item.fecha != this.regtmp.fecha && item.monto != this.regtmp.monto && item.descripcion != this.regtmp.descripcion) );
  		this.regtmp=null;
  	}
  }

  error(ru){
    console.log(ru);
  }

}
