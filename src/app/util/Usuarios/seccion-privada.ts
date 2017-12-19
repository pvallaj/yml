import { Injectable } from '@angular/core';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot, CanActivate } from '@angular/router';
import { AuthService } from './auth-service.service';

@Injectable()
export class Privada implements CanActivate{
	
	constructor(private auth:AuthService) {}

	canActivate(next:ActivatedRouteSnapshot, state:RouterStateSnapshot){

		return this.auth.isAuthenticated();
	}
}