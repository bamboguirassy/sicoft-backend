import { Component, OnInit } from '@angular/core';
import { RoleSurMarche } from '../role_sur_marche';
import { RoleSurMarcheService } from '../role_sur_marche.service';
import { NotificationService } from 'app/shared/services/notification.service';
import { Router } from '@angular/router';
import { Location } from '@angular/common';

@Component({
  selector: 'app-role_sur_marche-new',
  templateUrl: './role_sur_marche-new.component.html',
  styleUrls: ['./role_sur_marche-new.component.scss']
})
export class RoleSurMarcheNewComponent implements OnInit {
  role_sur_marche: RoleSurMarche;
  constructor(public role_sur_marcheSrv: RoleSurMarcheService,
    public notificationSrv: NotificationService,
    public router: Router, public location: Location) {
    this.role_sur_marche = new RoleSurMarche();
  }

  ngOnInit() {
  }

  saveRoleSurMarche() {
    this.role_sur_marcheSrv.create(this.role_sur_marche)
      .subscribe((data: any) => {
        this.notificationSrv.showInfo('RoleSurMarche créé avec succès');
        this.role_sur_marche = new RoleSurMarche();
      }, error => this.role_sur_marcheSrv.httpSrv.handleError(error));
  }

  saveRoleSurMarcheAndExit() {
    this.role_sur_marcheSrv.create(this.role_sur_marche)
      .subscribe((data: any) => {
        this.router.navigate([this.role_sur_marcheSrv.getRoutePrefix(), data.id]);
      }, error => this.role_sur_marcheSrv.httpSrv.handleError(error));
  }

}

