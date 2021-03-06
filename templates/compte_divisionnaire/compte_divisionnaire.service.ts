
import { Injectable } from '@angular/core';
import { HttpService } from 'src/app/shared/services/http.service';
import { CompteDivisionnaire } from './compte_divisionnaire';

@Injectable({
  providedIn: 'root'
})
export class CompteDivisionnaireService {

  private routePrefix: string = 'compte_divisionnaire';

  constructor(public httpSrv: HttpService) { }

  findAll() {
    return this.httpSrv.get(this.getRoutePrefixWithSlash());
  }

  findOneById(id: number) {
    return this.httpSrv.get(this.getRoutePrefixWithSlash() + id);
  }

  create(compte_divisionnaire: CompteDivisionnaire) {
    return this.httpSrv.post(this.getRoutePrefixWithSlash() + 'create', compte_divisionnaire);
  }

  update(compte_divisionnaire: CompteDivisionnaire) {
    return this.httpSrv.put(this.getRoutePrefixWithSlash()+compte_divisionnaire.id+'/edit', compte_divisionnaire);
  }

  clone(original: CompteDivisionnaire, clone: CompteDivisionnaire) {
    return this.httpSrv.put(this.getRoutePrefixWithSlash()+original.id+'/clone', clone);
  }

  remove(compte_divisionnaire: CompteDivisionnaire) {
    return this.httpSrv.delete(this.getRoutePrefixWithSlash()+compte_divisionnaire.id);
  }

  removeSelection(compte_divisionnaires: CompteDivisionnaire[]) {
    return this.httpSrv.deleteMultiple(this.getRoutePrefixWithSlash()+'delete-selection/',compte_divisionnaires);
  }

  public getRoutePrefix(): string {
    return this.routePrefix;
  }

  private getRoutePrefixWithSlash(): string {
    return this.routePrefix+'/';
  }

}
