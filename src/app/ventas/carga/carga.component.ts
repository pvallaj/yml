import { Component
    ,OnInit 
    ,Input
    ,Output } from '@angular/core';
import { Coneccion }      from '../../util/Coneccion.service';
import { UtilS }      from '../../util/util-s.service';

import {DataSource} from '@angular/cdk/collections';
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/observable/of';

@Component({
  selector: 'app-carga',
  templateUrl: './carga.component.html',
  styleUrls: ['./carga.component.css']
})
export class CargaComponent implements OnInit {
  displayedColumns = ['fecha','efectivo', 'tarjeta', 'total'];
  tipo:any;
  tipos=[{id:1, descripcion:'ventas'},{id:2, descripcion:'comisiones'}];
  datos:string="";
  totalEfectivo:number=0;
  totalTarjeta:number=0;
  total:number=0;
  totalComisiones:number=0;

  fi:Date;
  ff:Date;
  constructor(private cnx:Coneccion, private us:UtilS) {
    this.tipo=this.tipos[0];
    let hoy:Date=new Date();

    this.fi=new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    this.ff=new Date(hoy.getFullYear(), hoy.getMonth()+1, 0);
    this.registros=new YamelDataSource(cnx);
  }
  registros:YamelDataSource; 

  totalizar(){
    this.totalEfectivo=0;
    this.totalTarjeta=0;
    this.total=0;
    this.totalComisiones=0;
 
    /*for(let reg of this.registros){
      if(reg.comisiones){
        this.totalComisiones+=parseFloat(reg.comisiones);
      }else{
        this.totalEfectivo+=parseFloat(reg.efectivo);
        this.totalTarjeta+=parseFloat(reg.tarjeta);
        this.total+=parseFloat(reg.total);
      }
    }*/
  }

  ngOnInit() {
    this.cnx.ejecutar({
        accion:(this.tipo.descripcion=='ventas')?'2:1':'3:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaInicio(f:any){
      this.cnx.ejecutar({
        accion:(this.tipo.descripcion=='ventas')?'2:1':'3:1',
        fi:this.us.DateACadena(f),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaTermino(f:any){
      this.cnx.ejecutar({
        accion:(this.tipo.descripcion=='ventas')?'2:1:':'3:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(f),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  
  obtConcentradoResp(resp){
    console.log(resp);
    if(!this.registros)
      this.registros=new YamelDataSource(resp.datos);
    else
      this.registros.actDatos(resp.datos);
    this.registros.checkar();
    this.totalizar();
  }

  enviar(dts){
    console.log(this.datos);
    this.cnx.ejecutar({
      accion:(this.tipo.descripcion=='ventas')?'2:3':'3:3',
      datos:this.datos,
      fi:this.us.DateACadena(this.fi),
      ff:this.us.DateACadena(this.ff),
    }).subscribe((resp:any)=>this.cargaResp(resp),(ru:any)=>this.error(ru));
  }

  cargaResp(resp){
    console.log(resp);
    this.registros=resp.datos;
    this.datos="";
  }
  cambiaTipo(){
    console.log("Se cambko el tipo");
     this.cnx.ejecutar({
        accion:(this.tipo.descripcion=='ventas')?'2:1':'3:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }

  
  error(ru){
    console.log(ru);
  }

}

export interface Carga {
  fecha:    string;
  efectivo: number;
  tarjeta:  number;
  total:    number;
}

export class YamelDataSource extends DataSource<any> {

  public registros:Carga[]=[];
  constructor(private cnx:Coneccion) {
    super();
    //this.registros=regs;
  }

  /** Connect function called by the table to retrieve one stream containing the data to render. */
  connect(): Observable<Carga[]> {
    return this.cnx.ejecutar({
        accion:'2:1',
        fi:'2017-12-01',
        ff:'2017-12-31'
      });
  }

  actDatos(regs:any){
    //this.registros=regs; 
    if(regs){
      for(let u of regs){
         var t:Carga={
              fecha:    "",
              efectivo: 0,
              tarjeta:  0,
              total:    0};
         t.efectivo=u.efectivo;
         t.fecha=u.fecha;
         t.tarjeta=u.tarjeta;
         t.total=u.total;
         this.registros.push(t);
      }
    }
  }
  checkar(){
    console.log('----------------------------');
    console.log(this.registros);
    console.log('----------------------------');
  }
  disconnect() {}
}