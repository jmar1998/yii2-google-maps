<?php

use app\forms\RouteForm;
use yii\bootstrap5\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
/**
 * @var RouteForm $routeForm
 * @var View $this
 */
?>

<div class="row">
    <div class="col-12">
        <?php if($modelForm->isLoaded()) : ?>
            <?= Html::a('Reiniciar',
                ["/google-maps/explore"],
                ['class' => "badge text-decoration-none text-wrap text-center bg-danger"]
            ) ?>
        <?php endif;?>
        <?= Html::a('Gestionar', ["/google-maps/routes"], ['class' => 'badge bg-success text-decoration-none text-wrap text-center']) ?>
    </div>
</div>
<?php $form = ActiveForm::begin([
    'id' => 'explorer-form',
    'options' => ['class' => 'form-horizontal'],
]) ?>
<div class="card border-0" data-step="1">
    <div class="card-header bg-primary text-white fw-bolder text-step">
        Selecciona la ruta
    </div>
    <div class="card-body p-0" style="display:none">
        <?= $form->field($modelForm, 'id')->dropDownList($existingRoutes, [
            "class" => "form-select rounded-0",
            "id" => "route-changer",
            "prompt" => "Selecciona una ruta"
        ])->label(false) ?>
        <?= $form->field($modelForm, 'directions')->hiddenInput([
            "id" => "directions"
        ])->label(false) ?>
        <?= $form->field($modelForm, 'sourceRequests')->hiddenInput([
            "id" => "source-requests"
        ])->label(false) ?>
    </div>
</div>
<div class="card border" data-step="2">
    <div class="card-header bg-primary text-white fw-bolder text-step">
        Establezca un punto de referencia en el mapa
    </div>
    <div class="card-body p-0" style="display:none">
        <?= $form->field($modelForm, 'refPoint')->textInput([
            "readonly" => true,
            "id" => "ref-point",
            "class" => "rounded-0 form-control"
        ])->label(false) ?>
    </div>
</div>
<div class="card border" data-step="3">
    <div class="card-header bg-primary text-white fw-bolder text-step">
        Resultados
    </div>
    <div class="card-body p-0" id="results" style="display:none">
        <dl class="mb-0">
            <dt>Distancia</dt>
            <dd class="mb-0" id="distance"></dd>
            <dt>Duración</dt>
            <dd class="mb-0" id="duration"></dd>
        </dl>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script>
    $("#map").on("mapReady", function (e, googleMap) {
        let marker = null;
        googleMap.setMarkerCallback(function(markerElement, position){
            if (!marker) marker = markerElement;
            marker.setPosition(position);
            marker.setLabel(null);
            marker.setMap(googleMap.map);
            $("#ref-point").val(position);
            googleMap.compareLocations(position).then((results) => {
                updateStep(3);
                $("#distance").text(results.routeInfo.distance.text);
                $("#duration").text(results.routeInfo.duration.text);
            });
            return false;
        });
    });
    
    function updateStep(step){
        $("[data-step]").each(function() {
            $(this).find(".card-body").toggle($(this).data("step") <= step);
            if ($(this).data("step") > step) {
                $(this).find(".card-header").removeClass("bg-primary").addClass("bg-secondary");
            } else {
                $(this).find(".card-header").removeClass("bg-secondary").addClass("bg-primary");
            }
        });
    }
    updateStep(<?= json_encode($modelForm->step) ?>);
    $("#route-changer").on("change", function() {
        $("#step").val(1);
        $("#explorer-form").submit();
    });
    
</script>