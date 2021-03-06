import { Injectable } from '@angular/core';
import { Resolve } from '@angular/router';
import { CompteService } from './compte.service';
import { map, catchError } from 'rxjs/operators';
import { of } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class MultipleCompteResolver implements Resolve<any> {
  resolve(route: import("@angular/router").ActivatedRouteSnapshot, state: import("@angular/router").RouterStateSnapshot): any | import("rxjs").Observable<any> | Promise<any> {
    return this.compteSrv.findAll().pipe(map(data => {
      return data;
    }),
      catchError(error => {
        const message = `Retrieval error: ${error}`;
        this.compteSrv.httpSrv.handleError(error);
        return of({ comptes: null, error: message });
      }));
  }

  constructor(public compteSrv: CompteService) { }
}

