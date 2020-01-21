
import { Component, OnInit } from '@angular/core';
import { RoleSurMarcheService } from '../role_sur_marche.service';
import { RoleSurMarche } from '../role_sur_marche';
import { ActivatedRoute, Router } from '@angular/router';
import { Location } from '@angular/common';
import { NotificationService } from 'app/shared/services/notification.service';

@Component({
  selector: 'app-role_sur_marche-edit',
  templateUrl: './role_sur_marche-edit.component.html',
  styleUrls: ['./role_sur_marche-edit.component.scss']
})
export class RoleSurMarcheEditComponent implements OnInit {

  role_sur_marche: RoleSurMarche;
  constructor(public role_sur_marcheSrv: RoleSurMarcheService,
    public activatedRoute: ActivatedRoute,
    public router: Router, public location: Location,
    public notificationSrv: NotificationService) {
  }

  ngOnInit() {
    this.role_sur_marche = this.activatedRoute.snapshot.data['role_sur_marche'];
  }

  updateRoleSurMarche() {
    this.role_sur_marcheSrv.update(this.role_sur_marche)
      .subscribe(data => this.location.back(),
        error => this.role_sur_marcheSrv.httpSrv.handleError(error));
  }

}
