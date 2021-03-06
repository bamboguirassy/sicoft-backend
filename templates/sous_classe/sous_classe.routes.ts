import { Route } from "@angular/router";
import { SousClasseListComponent } from './sous_classe-list/sous_classe-list.component';
import { SousClasseNewComponent } from './sous_classe-new/sous_classe-new.component';
import { SousClasseEditComponent } from './sous_classe-edit/sous_classe-edit.component';
import { SousClasseCloneComponent } from './sous_classe-clone/sous_classe-clone.component';
import { SousClasseShowComponent } from './sous_classe-show/sous_classe-show.component';
import { MultipleSousClasseResolver } from './multiple-sous_classe.resolver';
import { OneSousClasseResolver } from './one-sous_classe.resolver';

const sous_classeRoutes: Route = {
    path: 'sous_classe', children: [
        { path: '', component: SousClasseListComponent, resolve: { sous_classes: MultipleSousClasseResolver } },
        { path: 'new', component: SousClasseNewComponent },
        { path: ':id/edit', component: SousClasseEditComponent, resolve: { sous_classe: OneSousClasseResolver } },
        { path: ':id/clone', component: SousClasseCloneComponent, resolve: { sous_classe: OneSousClasseResolver } },
        { path: ':id', component: SousClasseShowComponent, resolve: { sous_classe: OneSousClasseResolver } }
    ]

};

export { sous_classeRoutes }
