import { Injectable }               from '@angular/core';
import { Http, Response }           from '@angular/http';
import { Headers, RequestOptions }  from '@angular/http';
import { Observable }               from 'rxjs/Observable';
import { isDevMode }                from '@angular/core';


import 'rxjs/operator';
import 'rxjs/Rx';

@Injectable()
export class Coneccion{
  private headers:Headers;
  private url:string
  private opciones:RequestOptions;
  private servl:string='http://192.168.64.2';
  public  componente:string='/index.php';
  public  metodo:string='';
  public  base:string='/yws';
  public  basel:string='/yws';

  private almacen=[];

  agregarAlmacen(id:string, arr:any){
    this.almacen.push({id:id,contenido:arr});
  }

  existeElementoAlamacen(id:string){
    for(let e of this.almacen){
      if(e.id==id) return true;
    }
    return false;
  }
  recuperaAlmacen(id){
    for(let e of this.almacen){
      if(e.id==id) return e.contenido;
    }
    return null;  
  }

  constructor(private http:Http){
    this.headers = new Headers({ 'Content-Type': 'application/json' });
    this.opciones = new RequestOptions({ headers: this.headers });
  }

  ejecutar(parametros:any):Observable<any>{
    
    if(isDevMode())
      this.url=this.servl+this.basel+this.componente;
    else
      this.url=this.base+this.componente+'.cfc?method='+this.metodo;
    let cuerpo=  JSON.stringify({cn:parametros});
    console.log('Iniciando peticion. '+this.url);
    return this.http.post(  this.url,   cuerpo, {headers:this.headers})
      .map(this.respuesta.bind(this))
      .catch(this.error.bind(this));
  }

  private respuesta(data:Response){
    let r=data.json();
    //return r.datos;
    return r;
  }

  private error (error: any) {
     let errMsg = (error.message) ? error.message : error.status ? `${error.status} - ${error.statusText}` : 'Server error';
     console.log(error);
     console.log(errMsg);
     return Observable.throw(errMsg)
  }

}

