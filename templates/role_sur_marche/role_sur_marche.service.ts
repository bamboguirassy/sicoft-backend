
import { Injectable } from '@angular/core';
import { HttpService } from 'app/shared/services/http.service';
import { RoleSurMarche } from './role_sur_marche';

@Injectable({
  providedIn: 'root'
})
export class RoleSurMarcheService {

  private routePrefix: string = 'role_sur_marche';

  constructor(public httpSrv: HttpService) { }

  findAll() {
    return this.httpSrv.get(this.getRoutePrefixWithSlash());
  }

  findOneById(id: number) {
    return this.httpSrv.get(this.getRoutePrefixWithSlash() + id);
  }

  create(role_sur_marche: RoleSurMarche) {
    return this.httpSrv.post(this.getRoutePrefixWithSlash() + 'create', role_sur_marche);
  }

  update(role_sur_marche: RoleSurMarche) {
    return this.httpSrv.put(this.getRoutePrefixWithSlash()+role_sur_marche.id+'/edit', role_sur_marche);
  }

  clone(original: RoleSurMarche, clone: RoleSurMarche) {
    return this.httpSrv.put(this.getRoutePrefixWithSlash()+original.id+'/clone', clone);
  }

  remove(role_sur_marche: RoleSurMarche) {
    return this.httpSrv.delete(this.getRoutePrefixWithSlash()+role_sur_marche.id);
  }

  removeSelection(role_sur_marches: RoleSurMarche[]) {
    return this.httpSrv.deleteMultiple(this.getRoutePrefixWithSlash()+'delete-selection/',role_sur_marches);
  }

  public getRoutePrefix(): string {
    return this.routePrefix;
  }

  private getRoutePrefixWithSlash(): string {
    return this.routePrefix+'/';
  }

}
