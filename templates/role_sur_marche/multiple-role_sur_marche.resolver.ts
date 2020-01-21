import { Injectable } from '@angular/core';
import { Resolve } from '@angular/router';
import { RoleSurMarcheService } from './role_sur_marche.service';
import { map, catchError } from 'rxjs/operators';
import { of } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class MultipleRoleSurMarcheResolver implements Resolve<any> {
  resolve(route: import("@angular/router").ActivatedRouteSnapshot, state: import("@angular/router").RouterStateSnapshot): any | import("rxjs").Observable<any> | Promise<any> {
    return this.role_sur_marcheSrv.findAll().pipe(map(data => {
      return data;
    }),
      catchError(error => {
        const message = `Retrieval error: ${error}`;
        this.role_sur_marcheSrv.httpSrv.handleError(error);
        return of({ role_sur_marches: null, error: message });
      }));
  }

  constructor(public role_sur_marcheSrv: RoleSurMarcheService) { }
}

