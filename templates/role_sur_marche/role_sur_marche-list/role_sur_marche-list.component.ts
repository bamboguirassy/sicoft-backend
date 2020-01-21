import { Component, OnInit } from '@angular/core';
import { RoleSurMarche } from '../role_sur_marche';
import { ActivatedRoute, Router } from '@angular/router';
import { RoleSurMarcheService } from '../role_sur_marche.service';
import { role_sur_marcheColumns, allowedRoleSurMarcheFieldsForFilter } from '../role_sur_marche.columns';
import { ExportService } from 'app/shared/services/export.service';
import { MenuItem } from 'primeng/api';
import { AuthService } from 'app/shared/services/auth.service';
import { NotificationService } from 'app/shared/services/notification.service';


@Component({
  selector: 'app-role_sur_marche-list',
  templateUrl: './role_sur_marche-list.component.html',
  styleUrls: ['./role_sur_marche-list.component.scss']
})
export class RoleSurMarcheListComponent implements OnInit {

  role_sur_marches: RoleSurMarche[] = [];
  selectedRoleSurMarches: RoleSurMarche[];
  selectedRoleSurMarche: RoleSurMarche;
  clonedRoleSurMarches: RoleSurMarche[];

  cMenuItems: MenuItem[]=[];

  tableColumns = role_sur_marcheColumns;
  //allowed fields for filter
  globalFilterFields = allowedRoleSurMarcheFieldsForFilter;


  constructor(private activatedRoute: ActivatedRoute,
    public role_sur_marcheSrv: RoleSurMarcheService, public exportSrv: ExportService,
    private router: Router, public authSrv: AuthService,
    public notificationSrv: NotificationService) { }

  ngOnInit() {
    if(this.authSrv.checkShowAccess('RoleSurMarche')){
      this.cMenuItems.push({ label: 'Afficher détails', icon: 'pi pi-eye', command: (event) => this.viewRoleSurMarche(this.selectedRoleSurMarche) });
    }
    if(this.authSrv.checkEditAccess('RoleSurMarche')){
      this.cMenuItems.push({ label: 'Modifier', icon: 'pi pi-pencil', command: (event) => this.editRoleSurMarche(this.selectedRoleSurMarche) })
    }
    if(this.authSrv.checkCloneAccess('RoleSurMarche')){
      this.cMenuItems.push({ label: 'Cloner', icon: 'pi pi-clone', command: (event) => this.cloneRoleSurMarche(this.selectedRoleSurMarche) })
    }
    if(this.authSrv.checkDeleteAccess('RoleSurMarche')){
      this.cMenuItems.push({ label: 'Supprimer', icon: 'pi pi-times', command: (event) => this.deleteRoleSurMarche(this.selectedRoleSurMarche) })
    }

    this.role_sur_marches = this.activatedRoute.snapshot.data['role_sur_marches'];
  }

  viewRoleSurMarche(role_sur_marche: RoleSurMarche) {
      this.router.navigate([this.role_sur_marcheSrv.getRoutePrefix(), role_sur_marche.id]);

  }

  editRoleSurMarche(role_sur_marche: RoleSurMarche) {
      this.router.navigate([this.role_sur_marcheSrv.getRoutePrefix(), role_sur_marche.id, 'edit']);
  }

  cloneRoleSurMarche(role_sur_marche: RoleSurMarche) {
      this.router.navigate([this.role_sur_marcheSrv.getRoutePrefix(), role_sur_marche.id, 'clone']);
  }

  deleteRoleSurMarche(role_sur_marche: RoleSurMarche) {
      this.role_sur_marcheSrv.remove(role_sur_marche)
        .subscribe(data => this.refreshList(), error => this.role_sur_marcheSrv.httpSrv.handleError(error));
  }

  deleteSelectedRoleSurMarches(role_sur_marche: RoleSurMarche) {
      if (this.selectedRoleSurMarches) {
        this.role_sur_marcheSrv.removeSelection(this.selectedRoleSurMarches)
          .subscribe(data => this.refreshList(), error => this.role_sur_marcheSrv.httpSrv.handleError(error));
      } else {
        this.role_sur_marcheSrv.httpSrv.notificationSrv.showWarning("Selectionner au moins un élement");
      }
  }

  refreshList() {
    this.role_sur_marcheSrv.findAll()
      .subscribe((data: any) => this.role_sur_marches = data, error => this.role_sur_marcheSrv.httpSrv.handleError(error));
  }

  exportPdf() {
    this.exportSrv.exportPdf(this.tableColumns, this.role_sur_marches, 'role_sur_marches');
  }

  exportExcel() {
    this.exportSrv.exportExcel(this.role_sur_marches);
  }

  saveAsExcelFile(buffer: any, fileName: string): void {
    this.exportSrv.saveAsExcelFile(buffer, fileName);
  }

}