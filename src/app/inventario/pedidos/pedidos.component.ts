import { Component, OnInit } from '@angular/core';

import { Coneccion }    from '../../util/Coneccion.service';
import { UtilS }        from '../../util/util-s.service';

@Component({
  selector: 'app-pedidos',
  templateUrl: './pedidos.component.html',
  styleUrls: ['./pedidos.component.css']
})
export class PedidosComponent implements OnInit {
  fi:Date;
  ff:Date;
  descripcion:string;

  model = {
    left: true,
    middle: false,
    right: false
  };
  registros:any; 

  regtmp:{fecha:string,monto:number,descripcion:string, id:number};
  constructor(private cnx:Coneccion, private us:UtilS) {
    let hoy:Date=new Date();
    this.fi=new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    this.ff=new Date(hoy.getFullYear(), hoy.getMonth()+1, 0);
  }

  
  ngOnInit() {
    this.cnx.ejecutar({
      accion:'7:1',
       fi:this.us.DateACadena(this.fi),
       ff:this.us.DateACadena(this.ff),
    }).subscribe((resp:any)=>this.obtNominasResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaInicio(f:any){
      this.cnx.ejecutar({
        accion:'7:1',
        fi:this.us.DateACadena(f),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtNominasResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaTermino(f:any){
      this.cnx.ejecutar({
        accion:'7:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(f),
      }).subscribe((resp:any)=>this.obtNominasResp(resp),(ru:any)=>this.error(ru));
  }
  eliminar(id:number){
     this.regtmp={fecha:'', descripcion:'', monto:0, id:id};
     this.cnx.ejecutar({
        accion:'7:3',
        id:id
      }).subscribe((resp:any)=>this.eliminarNominasResp(resp),(ru:any)=>this.error(ru));
  }
  obtNominasResp(resp){
    console.log(resp);
    this.registros=resp.datos;
    for(let r of this.registros) r.edo=0;
    
  }
  eliminarNominasResp(resp){
    console.log(resp);
    if(resp.ce>0){
      this.registros=this.registros.filter(item => (item.id != this.regtmp.id ) );
    }
  }

  expander(dts:any){
    dts.edo=1;
    console.log(dts);
  }
  contraer(dts:any){
    dts.edo=0;
    console.log(dts); 
  }

  agregarNomina(v:any){
     this.cnx.ejecutar({
        accion:'7:2',
        descripcion:this.descripcion,
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.agregarNominaR(resp),(ru:any)=>this.error(ru));
  }
  agregarNominaR(r){
    console.log(r);
    if(r.ce>0){
      let tmp={
        id:r.id,
        descripcion:this.descripcion,
        inicio:this.us.DateACadena(this.fi),
        termino:this.us.DateACadena(this.ff)
      }
      this.registros.push(tmp);
    }
  }

  calcularNomina(id:any){
     this.cnx.ejecutar({
        accion:'6:5',
        id:id
      }).subscribe((resp:any)=>this.calcularNominaR(resp),(ru:any)=>this.error(ru));
  }
  calcularNominaR(r){
    console.log(r);
    if(r.ce>0){
      console.log("Nomina calculada con exito.");
    }
  }
  error(ru){
    console.log(ru);
  }
}