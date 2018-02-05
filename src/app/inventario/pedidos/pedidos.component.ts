import { Component, OnInit } from '@angular/core';

import { Coneccion }        from '../../util/Coneccion.service';
import { UtilS }            from '../../util/util-s.service';
import { CatalogosService } from '../../util/catalogos.service';

@Component({
  selector: 'app-pedidos',
  templateUrl: './pedidos.component.html',
  styleUrls: ['./pedidos.component.css']
})
export class PedidosComponent implements OnInit {
  fi:Date;
  ff:Date;
  fp:Date;
  descripcion:string;

  model = {
    left: true,
    middle: false,
    right: false
  };
  registros:any;
  verDetalleSol:boolean=false;
  cargando:boolean=false;
  cproductos:any;

  regtmp:{fecha:string,monto:number,descripcion:string, id:number};
  constructor(private cnx:Coneccion, private us:UtilS, public cs:CatalogosService) {
    let hoy:Date=new Date();
    this.fi=new Date(hoy.getFullYear(), 0, 1);
    this.ff=new Date(hoy.getFullYear(), hoy.getMonth()+1, 0);
    if(!this.cs.existe('productos')){
       this.cnx.ejecutar({
            accion:'7:6'
        }).subscribe((resp:any)=>this.cargaProductosR(resp),(ru:any)=>this.error(ru));
    }else{
      console.log("catalogo en memoria");
      this.cproductos=this.cs.obtCatalogo('productos');
      console.log(this.cproductos);
    }
  }

  cargaProductosR(resp){
     this.cs.agregarCatalogo('productos',resp.productos);
     console.log('Productos cargados: '+resp.productos.length);
     this.cproductos=this.cs.obtCatalogo('productos');
     console.log(this.cproductos);
  }
  ngOnInit() {
    this.cargando=true;
    this.cnx.ejecutar({
      accion:'7:1',
       fi:this.us.DateACadena(this.fi),
       ff:this.us.DateACadena(this.ff),
    }).subscribe((resp:any)=>this.obtNominasResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaInicio(f:any){
      this.cargando=true;
      this.cnx.ejecutar({
        accion:'7:1',
        fi:this.us.DateACadena(f),
        ff:this.us.DateACadena(this.ff),
      }).subscribe((resp:any)=>this.obtNominasResp(resp),(ru:any)=>this.error(ru));
  }
  cambiaTermino(f:any){
      this.cargando=true;
      this.cnx.ejecutar({
        accion:'7:1',
        fi:this.us.DateACadena(this.fi),
        ff:this.us.DateACadena(f),
      }).subscribe((resp:any)=>this.obtNominasResp(resp),(ru:any)=>this.error(ru));
  }

  cargaDetalle(p_id:number){
      this.cargando=true;
      this.cnx.ejecutar({
        accion:'7:4',
        id:p_id
      }).subscribe((resp:any)=>this.obtDetalleResp(resp),(ru:any)=>this.error(ru));

  }

  obtDetalleResp(resp){
    this.cargando=false;
    console.log(resp);
    this.detalle=resp.datos;
    this.verDetalleSol=true;
  }



  regresar(){
     this.verDetalleSol=false;
  }

 
  obtNominasResp(resp){
    this.cargando=false;
    console.log(resp);
    this.registros=resp.datos;
    for(let r of this.registros) r.edo=0;
    
  }
 eliminarPedido(id:number){
     this.regtmp={fecha:'', descripcion:'', monto:0, id:id};
     this.cnx.ejecutar({
        accion:'7:3',
        id:id
      }).subscribe((resp:any)=>this.eliminarPedidoR(resp),(ru:any)=>this.error(ru));
  }
  eliminarPedidoR(resp){
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

  agregarPedido(v:any){
     this.cnx.ejecutar({
        accion:'7:2',
        descripcion:this.descripcion,
        fp:this.us.DateACadena(this.fp),
      }).subscribe((resp:any)=>this.agregarPedidoR(resp),(ru:any)=>this.error(ru));
     this.fp=new Date;
     this.descripcion='';

  }
  agregarPedidoR(r){
    console.log(r);
    if(r.ce>0){
      let tmp={
        id:r.id,
        descripcion:r.descripcion,
        solicitado:r.solicitado
      }
      this.registros.push(tmp);
    }
  }

  /*
  Detalle de pedido-------
  */
  detalle=[];
  productos=[];
  //model
  producto:any;
  solicitado:any;
  agregarProducto(){
    let np={
      clave:0,
      descripcion:'sin especificar',
      solicitado:0,
      precio_compra:0,
      entregado:0,
      fecha_entrega:'',
      estado:1
    }
    this.producto=this.cproductos[0];
    this.solicitado=0;
    this.detalle.push(np);
  }
  editarProducto(registro:any){
    registro.estado=1;
  }
  eliminarProducto(idx:any){
    this.detalle.splice(idx,1);
  }
  guardarProducto(registro:any){
    console.log(this.producto);
    console.log(this.solicitado);
    console.log(registro);
    registro.clave=this.producto.clave;
    registro.descripcion=this.producto.descripcion;
    registro.solicitado=this.solicitado;
    registro.precio_compra=this.producto.precio_compra;
    registro.estado=0;
  }

  error(ru){
    console.log(ru);
  }
}