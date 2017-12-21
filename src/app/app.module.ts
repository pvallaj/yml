import { BrowserModule } 		      from '@angular/platform-browser';
import { FormsModule } 			      from "@angular/forms";
import { NgModule } 			        from '@angular/core';
import { NgbModule } 			        from '@ng-bootstrap/ng-bootstrap';
import { RouterModule } 		      from '@angular/router';
import { HttpModule }             from '@angular/http';
import { MdNativeDateModule,
         MdSelectModule, 
         MdSidenavModule,
         MdButtonModule, 
         MdIconModule,
         MatTableModule,
         MdDatepickerModule,
         MatInputModule,
         MdTooltipModule,
         MdTableModule,
         MdPaginatorModule
         }      from '@angular/material';
import { MatTable }                from '@angular/material/table';
import { BrowserAnimationsModule}  from '@angular/platform-browser/animations';

import { Registro } 			        from './util/Usuarios/registro.component';
import { ValSessionComponent }    from './util/Usuarios/ValSesion.component';
import { SesionUsuario } 		      from './util/Usuarios/SesionUsuario.service';
import { UtilS }                  from './util/util-s.service';
import { Coneccion } 			        from './util/Coneccion.service';

import { AppComponent } 		      from './app.component';
import { InicioComponent } 		    from './inicio/inicio.component'
import { NominaComponent}         from './nomina/nomina.component';

import 'hammerjs';
import { PedidosComponent }       from './inventario/pedidos/pedidos.component';
import { CargaComponent }         from './ventas/carga/carga.component';
import { ConfrtDiaComponent }     from './ventas/confrt-dia/confrt-dia.component';
import { GastosComponent }        from './gastos/gastos.component';
import { DetalleComponent }       from './nomina/detalle/detalle.component';

import { AuthService }            from './util/Usuarios/auth-service.service';
import { Privada }                from './util/Usuarios/seccion-privada';
//Estor agregando un comentario.
@NgModule({
  declarations: [
    AppComponent
    ,Registro
    ,InicioComponent
    ,ValSessionComponent
    ,NominaComponent
    ,PedidosComponent, CargaComponent, ConfrtDiaComponent, GastosComponent, DetalleComponent
  ],
  imports: [
    MdSidenavModule, MdIconModule, MdButtonModule, MdTooltipModule, MatInputModule, MdDatepickerModule, MdNativeDateModule, MdSelectModule,
    MatTableModule, MdPaginatorModule,
    BrowserAnimationsModule,
    HttpModule, BrowserModule, 	NgbModule.forRoot(), 	FormsModule, 
    RouterModule.forRoot([
            { path: '',                  component: InicioComponent  },
            { path: 'inicio',            component: InicioComponent },
            { path: 'nomina',            component: NominaComponent ,   canActivate:[Privada]},
            { path: 'pedidos',           component: PedidosComponent,   canActivate:[Privada]  },
            { path: 'carga',             component: CargaComponent,     canActivate:[Privada]  },
            { path: 'confronta',         component: ConfrtDiaComponent, canActivate:[Privada]  },
            { path: 'gastos',            component: GastosComponent,    canActivate:[Privada]  }
          ])
  ],
  providers: [ AuthService, SesionUsuario, Coneccion, UtilS, Privada],
  bootstrap: [AppComponent]
})
export class AppModule { }
