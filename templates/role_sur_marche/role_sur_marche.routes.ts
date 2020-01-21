import { Route } from "@angular/router";
import { RoleSurMarcheListComponent } from './role_sur_marche-list/role_sur_marche-list.component';
import { RoleSurMarcheNewComponent } from './role_sur_marche-new/role_sur_marche-new.component';
import { RoleSurMarcheEditComponent } from './role_sur_marche-edit/role_sur_marche-edit.component';
import { RoleSurMarcheCloneComponent } from './role_sur_marche-clone/role_sur_marche-clone.component';
import { RoleSurMarcheShowComponent } from './role_sur_marche-show/role_sur_marche-show.component';
import { MultipleRoleSurMarcheResolver } from './multiple-role_sur_marche.resolver';
import { OneRoleSurMarcheResolver } from './one-role_sur_marche.resolver';

const role_sur_marcheRoutes: Route = {
    path: 'role_sur_marche', children: [
        { path: '', component: RoleSurMarcheListComponent, resolve: { role_sur_marches: MultipleRoleSurMarcheResolver } },
        { path: 'new', component: RoleSurMarcheNewComponent },
        { path: ':id/edit', component: RoleSurMarcheEditComponent, resolve: { role_sur_marche: OneRoleSurMarcheResolver } },
        { path: ':id/clone', component: RoleSurMarcheCloneComponent, resolve: { role_sur_marche: OneRoleSurMarcheResolver } },
        { path: ':id', component: RoleSurMarcheShowComponent, resolve: { role_sur_marche: OneRoleSurMarcheResolver } }
    ]

};

export { role_sur_marcheRoutes }
