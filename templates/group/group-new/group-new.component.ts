import { Component, OnInit } from '@angular/core';
import { Group } from '../group';
import { GroupService } from '../group.service';
import { NotificationService } from 'app/shared/services/notification.service';
import { Router } from '@angular/router';
import { Location } from '@angular/common';

@Component({
  selector: 'app-group-new',
  templateUrl: './group-new.component.html',
  styleUrls: ['./group-new.component.scss']
})
export class GroupNewComponent implements OnInit {
  group: Group;
  constructor(public groupSrv: GroupService,
    public notificationSrv: NotificationService,
    public router: Router, public location: Location) {
    this.group = new Group();
  }

  ngOnInit() {
  }

  saveGroup() {
    this.groupSrv.create(this.group)
      .subscribe((data: any) => {
        this.notificationSrv.showInfo('Group créé avec succès');
        this.group = new Group();
      }, error => this.groupSrv.httpSrv.handleError(error));
  }

  saveGroupAndExit() {
    this.groupSrv.create(this.group)
      .subscribe((data: any) => {
        this.router.navigate([this.groupSrv.getRoutePrefix(), data.id]);
      }, error => this.groupSrv.httpSrv.handleError(error));
  }

}

