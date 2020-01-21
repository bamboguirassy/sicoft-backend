
import { Component, OnInit } from '@angular/core';
import { RoleSurMarcheService } from '../role_sur_marche.service';
import { Location } from '@angular/common';
import { RoleSurMarche } from '../role_sur_marche';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-role_sur_marche-clone',
  templateUrl: './role_sur_marche-clone.component.html',
  styleUrls: ['./role_sur_marche-clone.component.scss']
})
export class RoleSurMarcheCloneComponent implements OnInit {
  role_sur_marche: RoleSurMarche;
  original: RoleSurMarche;
  constructor(public role_sur_marcheSrv: RoleSurMarcheService, public location: Location,
    public activatedRoute: ActivatedRoute, public router: Router) { }

  ngOnInit() {
    this.original = this.activatedRoute.snapshot.data['role_sur_marche'];
    this.role_sur_marche = Object.assign({}, this.original);
    this.role_sur_marche.id = null;
  }

  cloneRoleSurMarche() {
    this.role_sur_marcheSrv.clone(this.original, this.role_sur_marche)
      .subscribe((data: any) => {
        this.router.navigate([this.role_sur_marcheSrv.getRoutePrefix(), data.id]);
      }, error => this.role_sur_marcheSrv.httpSrv.handleError(error));
  }

}
