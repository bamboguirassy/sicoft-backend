import { Component, OnInit } from '@angular/core';
import { RoleSurMarche } from '../role_sur_marche';
import { ActivatedRoute, Router } from '@angular/router';
import { RoleSurMarcheService } from '../role_sur_marche.service';
import { Location } from '@angular/common';
import { NotificationService } from 'app/shared/services/notification.service';

@Component({
  selector: 'app-role_sur_marche-show',
  templateUrl: './role_sur_marche-show.component.html',
  styleUrls: ['./role_sur_marche-show.component.scss']
})
export class RoleSurMarcheShowComponent implements OnInit {

  role_sur_marche: RoleSurMarche;
  constructor(public activatedRoute: ActivatedRoute,
    public role_sur_marcheSrv: RoleSurMarcheService, public location: Location,
    public router: Router, public notificationSrv: NotificationService) {
  }

  ngOnInit() {
    this.role_sur_marche = this.activatedRoute.snapshot.data['role_sur_marche'];
  }

  removeRoleSurMarche() {
    this.role_sur_marcheSrv.remove(this.role_sur_marche)
      .subscribe(data => this.router.navigate([this.role_sur_marcheSrv.getRoutePrefix()]),
        error =>  this.role_sur_marcheSrv.httpSrv.handleError(error));
  }
  
  refresh(){
    this.role_sur_marcheSrv.findOneById(this.role_sur_marche.id)
    .subscribe((data:any)=>this.role_sur_marche=data,
      error=>this.role_sur_marcheSrv.httpSrv.handleError(error));
  }

}

