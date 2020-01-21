import { Injectable } from '@angular/core';
import { Resolve } from '@angular/router';
import { map, catchError } from 'rxjs/operators';
import { of } from 'rxjs';
import { RoleSurMarcheService } from './role_sur_marche.service';

@Injectable({
  providedIn: 'root'
})
export class OneRoleSurMarcheResolver implements Resolve<any> {
  resolve(route: import("@angular/router").ActivatedRouteSnapshot, state: import("@angular/router").RouterStateSnapshot) {
    return this.role_sur_marcheSrv.findOneById(route.params.id).pipe(map(data => {
      return data;
    }),
    catchError(error => {
      const message = `Retrieval error: ${error}`;
      return of({ role_sur_marche: null, error: message });
    }));
  }

  constructor(public role_sur_marcheSrv:RoleSurMarcheService) { }
}

