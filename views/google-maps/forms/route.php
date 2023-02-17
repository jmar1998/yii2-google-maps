<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="row">
    <div class="col-12">
        <?= Html::a(
            $modelForm->isLoaded() ? 'Reiniciar' : "Crear",
            "/google-maps/routes",
            ['class' => array_merge(["badge text-decoration-none text-wrap text-center"], [$modelForm->isLoaded() ? 'bg-danger' : 'bg-dark'])]
        ) ?>
        <?= Html::a('Explorar', ["/google-maps/explore"], ['class' => 'badge bg-success text-decoration-none text-wrap text-center']) ?>
    </div>
</div>
<?php $form = ActiveForm::begin([
    'id' => 'route-form',
    'options' => ['class' => 'form-horizontal'],
]) ?>
<?php if(!empty($existingRoutes)) : ?>
    <?= $form->field($modelForm, 'id')->dropDownList($existingRoutes, [
         "class" => "form-select",
         "id" => "route-changer",
         "prompt" => "Selecciona una ruta"
    ])->label("Routas existentes", ["class" => "form-label"])?>
<?php endif ; ?>
<?= $form->field($modelForm, 'name') ?>
<?= $form->field($modelForm, 'directions')->hiddenInput([
    "id" => "directions"
])->label(false) ?>
<?= $form->field($modelForm, 'sourceRequests')->hiddenInput([
    "id" => "source-requests"
])->label(false) ?>
<div id="way-points"></div>
<div class="form-group">
    <div class="col-12">
        <?= Html::button('Planear ruta', ['class' => 'btn btn-sm btn-primary', 'id' => "generate"]) ?>
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-sm btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script>
    $(function () {
        $("#route-changer").on("change", function(){
            window.location.href = `<?= Url::current(["route" => null])?>?route=${$(this).val()}`;
        });
    });
</script>