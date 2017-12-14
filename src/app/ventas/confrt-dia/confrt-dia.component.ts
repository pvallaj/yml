
import { Component
    ,OnInit 
    ,Input
    ,Output } from '@angular/core';
import { Coneccion }  from '../../util/Coneccion.service';
import { UtilS }      from '../../util/util-s.service';

@Component({
  selector: 'confronta-dia',
  templateUrl: './confrt-dia.component.html',
  styleUrls: ['./confrt-dia.component.css']
})
export class ConfrtDiaComponent implements OnInit {
  @Input() tipo:string;
  datos:string="";
  contado:number;
  comentarios:string;
  fi:Date;
  ff:Date;

  regTemp:any;
  constructor(private cnx:Coneccion, private us:UtilS) {
    if(!this.tipo) this.tipo='ventas';
    let hoy:Date=new Date();

    this.fi=new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    this.ff=new Date(hoy.getFullYear(), hoy.getMonth()+1, 0);

  }
  registros:any;

  ngOnInit() {
    this.cnx.ejecutar({
      accion:'4:1',
      fi:this.us.DateACadena(this.fi),
      ff:this.us.DateACadena(this.ff),
    }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }

  cambiaInicio(e:any){
   this.cnx.ejecutar({
      accion:'4:1',
      fi:this.us.DateACadena(e),
      ff:this.us.DateACadena(this.ff),
    }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru)); 
  }
  cambiaTermino(e:any){
   this.cnx.ejecutar({
      accion:'4:1',
      fi:this.us.DateACadena(this.fi),
      ff:this.us.DateACadena(e),
    }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru)); 
  }

  obtConcentradoResp(resp){
    console.log(resp);
    this.registros=resp.datos;
    for(let c of this.registros) c.editado=false;
  }

  /*enviar(dts){
    console.log(this.datos);
    this.cnx.ejecutar({
      accion:(this.tipo=='ventas')?'carga ventas':'carga comisiones',
      datos:this.datos,
      fi:'2017-09-01',
      ff:'2017-09-30'
    }).subscribe((resp:any)=>this.cargaResp(resp),(ru:any)=>this.error(ru));
  }
  cargaResp(resp){
    console.log(resp);
    this.registros=resp.datos;
  }*/
  
  editar(dts:any){
    //dts.editado=true;
    this.regTemp=dts;
    this.cnx.ejecutar({
      accion:'4:2',
      f:dts.fecha
    }).subscribe((resp:any)=>this.calcConfrontaResp(resp),(ru:any)=>this.error(ru));
  }
  calcConfrontaResp(resp:any){

    this.regTemp.id=resp.datos[0].id;
    this.regTemp.calculado=resp.datos[0].calculado;
    this.regTemp.gastos=resp.datos[0].gastos;
    this.regTemp.propina=resp.datos[0].propina;
    this.regTemp.tarjeta=resp.datos[0].tarjeta;
    this.regTemp.editado=true;
  }

  guardarregistro(dts:any){
    //dts.editado=true;
    console.log(this.contado, this.comentarios);
    this.regTemp=dts;
    this.regTemp.contado=this.contado;
    this.regTemp.comentarios=this.comentarios;
    this.cnx.ejecutar({
      accion:'4:3',
      id:dts.id,
      contado:this.contado,
      comentarios:this.comentarios
    }).subscribe((resp:any)=>this.guardaConfrontaResp(resp),(ru:any)=>this.error(ru));
  }
  guardaConfrontaResp(resp){
      console.log(resp);
      this.regTemp.editado=false;
  }

  error(ru){
    console.log(ru);
  }

}

