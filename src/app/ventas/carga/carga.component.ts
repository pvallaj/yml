import { Component
    ,OnInit 
    ,Input
    ,Output } from '@angular/core';
import { Coneccion }      from '../../util/Coneccion.service';
import { UtilS }      from '../../util/util-s.service';



@Component({
  selector: 'app-carga',
  templateUrl: './carga.component.html',
  styleUrls: ['./carga.component.css']
})
export class CargaComponent implements OnInit {
  tipo:any;
  tipos=[{id:1, descripcion:'ventas'},{id:2, descripcion:'comisiones'}];
  datos:string="";
  totalEfectivo:number=0;
  totalTarjeta:number=0;
  total:number=0;
  totalComisiones:number=0;
  registros:any; 
  cargando:boolean=false;

  fi:Date;
  ff:Date;
  constructor(private cnx:Coneccion, private us:UtilS) {
    this.tipo=this.tipos[0];
    let hoy:Date=new Date();

    this.fi=new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    this.ff=new Date(hoy.getFullYear(), hoy.getMonth()+1, 0);
  }
  

  totalizar(){
    this.totalEfectivo=0;
    this.totalTarjeta=0;
    this.total=0;
    this.totalComisiones=0;
 
    for(let reg of this.registros){
      if(reg.comisiones){
        this.totalComisiones+=parseFloat(reg.comisiones);
      }else{
        this.totalEfectivo+=parseFloat(reg.efectivo);
        this.totalTarjeta+=parseFloat(reg.tarjeta);
        this.total+=parseFloat(reg.total);
      }
    }
  }

  ngOnInit() {
    this.cargando=true;
    this.cnx.ejecutar({
        accion:(this.tipo.descripcion=='ventas')?'2:1':'3:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaInicio(f:any){
      this.cargando=true;
      this.cnx.ejecutar({
        accion:(this.tipo.descripcion=='ventas')?'2:1':'3:1',
        fi:this.us.DateACadena(f),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaTermino(f:any){
      this.cargando=true;
      this.cnx.ejecutar({
        accion:(this.tipo.descripcion=='ventas')?'2:1:':'3:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(f),
      }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
  }
  
  obtConcentradoResp(resp){
    console.log(resp);
    this.registros=resp.datos;
    this.totalizar();
    this.cargando=false;
  }

  enviar(dts){
    this.cargando=true;
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
    this.datos="";
    this.cargando=false;
  }

  cambiaTipo(){
    console.log("Se cambko el tipo");
    this.cargando=true;
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

