<p-toast></p-toast>
<!-- form <?= $entity_twig_var_singular ?> info -->
<form *ngIf="'<?= $entity_class_name ?>'|creable" class="form" role="form" #<?= $entity_twig_var_singular ?>Form="ngForm" autocomplete="off">
  <p-fieldset legend="Creation utilisateur" [toggleable]="true">
    <!-- debut body -->
    <div class="row">
<?php foreach ($entity_fields as $field): ?>
        <?php if($field['fieldName']!='id'){ ?>
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="col-form-label form-control-label" for="<?= $field['fieldName'] ?>"><?= ucfirst($field['fieldName']) ?></label>
                    <input [(ngModel)]="<?= $entity_twig_var_singular.'.'.$field['fieldName'] ?>" class="form-control" type="text" name="<?= $field['fieldName'] ?>" id="<?= $field['fieldName'] ?>">
                </div>
            </div>
        <?php } ?>
<?php endforeach; ?>  
    </div>
    <!-- fin body -->
    <p-toolbar>
      <button type="button" (click)="location.back()" class="btn btn-outline-secondary mr-1 pull-left"><i
          class="fa fa-reply" aria-hidden="true"></i>
        Retour</button>
      <button type="reset" class="btn btn-secondary pull-left"><i class="fa fa-eraser" aria-hidden="true"></i>
        Vider le formulaire</button>
      <button [disabled]="<?= $entity_twig_var_singular ?>Form.invalid" type="button" (click)="save<?= $entity_class_name ?>AndExit()"
        class="btn btn-outline-primary pull-right ml-1"> <i class="fa fa-save" aria-hidden="true"></i>
        Enregistrer</button>
      <button [disabled]="<?= $entity_twig_var_singular ?>Form.invalid" type="button" (click)="save<?= $entity_class_name ?>()"
        class="btn btn-outline-primary pull-right"> <i class="fa fa-save" aria-hidden="true"></i>
        Enregistrer & Créer un autre</button>
    </p-toolbar>
  </p-fieldset>
</form>
<!-- /form <?= $entity_twig_var_singular ?> info -->
