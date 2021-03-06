import { Route } from "@angular/router";
import { ExerciceSourceFinancementListComponent } from './exercice_source_financement-list/exercice_source_financement-list.component';
import { ExerciceSourceFinancementNewComponent } from './exercice_source_financement-new/exercice_source_financement-new.component';
import { ExerciceSourceFinancementEditComponent } from './exercice_source_financement-edit/exercice_source_financement-edit.component';
import { ExerciceSourceFinancementCloneComponent } from './exercice_source_financement-clone/exercice_source_financement-clone.component';
import { ExerciceSourceFinancementShowComponent } from './exercice_source_financement-show/exercice_source_financement-show.component';
import { MultipleExerciceSourceFinancementResolver } from './multiple-exercice_source_financement.resolver';
import { OneExerciceSourceFinancementResolver } from './one-exercice_source_financement.resolver';

const exercice_source_financementRoutes: Route = {
    path: 'exercice_source_financement', children: [
        { path: '', component: ExerciceSourceFinancementListComponent, resolve: { exercice_source_financements: MultipleExerciceSourceFinancementResolver } },
        { path: 'new', component: ExerciceSourceFinancementNewComponent },
        { path: ':id/edit', component: ExerciceSourceFinancementEditComponent, resolve: { exercice_source_financement: OneExerciceSourceFinancementResolver } },
        { path: ':id/clone', component: ExerciceSourceFinancementCloneComponent, resolve: { exercice_source_financement: OneExerciceSourceFinancementResolver } },
        { path: ':id', component: ExerciceSourceFinancementShowComponent, resolve: { exercice_source_financement: OneExerciceSourceFinancementResolver } }
    ]

};

export { exercice_source_financementRoutes }
