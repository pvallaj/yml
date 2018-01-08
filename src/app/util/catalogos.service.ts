import { Injectable } from '@angular/core';

@Injectable()
export class CatalogosService {
  catalogos=[];
  constructor() { }
  agregarCatalogo(nombre:String, catalogo:any){
    for(let i:number=0;i<this.catalogos.length;i++){
      if(this.catalogos[i].nombre==nombre) return;
    }
    this.catalogos.push({nombre:nombre, catalogo:catalogo});
  }
  existe(nombre:String){
    for(let i:number=0;i<this.catalogos.length;i++){
      if(this.catalogos[i].nombre==nombre) return true;
    }
    return false;
  }

  obtCatalogo(nombre:String){
    for(let i:number=0;i<this.catalogos.length;i++){
      if(this.catalogos[i].nombre==nombre) return this.catalogos[i].catalogo;
    }
    return null;
  }
}
