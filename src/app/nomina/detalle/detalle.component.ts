import { Component, OnInit, Input } from '@angular/core';
import { Coneccion }    from '../../util/Coneccion.service';
import { UtilS }      	from '../../util/util-s.service';

@Component({
  selector: 'app-detalle-nomina',
  templateUrl: './detalle.component.html',
  styleUrls: ['./detalle.component.css']
})
export class DetalleComponent implements OnInit {
	@Input() id_nom:number;
	registros:any;
  	constructor(private cnx:Coneccion, private us:UtilS) {

  	}
  	

	ngOnInit() {
	this.cnx.ejecutar({
	    accion:'6:4',
	    id:this.id_nom
	  }).subscribe((resp:any)=>this.obtConcentradoResp(resp),(ru:any)=>this.error(ru));
	}

	obtConcentradoResp(resp){
		console.log(resp);
		this.registros=resp.datos;
	}
		error(ru){
		console.log(ru);
	}

  estiloElemento(id){
    if(id==35) return 'elemento_1';
    if(id==36) return 'elemento_2';
    return 'elemento';
  }
}
