<?php

use app\assets\GoogleAsset;
use app\forms\RouteForm;
use yii\helpers\Url;
use yii\bootstrap5\{
    ActiveForm,
    Html
};

GoogleAsset::register($this);
/**
 * @var RouteForm $routeForm
 */
?>
<div class="row h-75">
    <div class="col-2 h-100">
        <div class="text-center">
            <div class="badge bg-primary text-wrap text-center" style="width: 6rem;">
                Recorrido
            </div>
        </div>
        <ul id="way-points" class="list-group mt-2 h-100 overflow-auto" style="max-height: calc(100% - 2em);">
        </ul>
    </div>
    <div class="col-7">
        <div id="map" class="google-map h-100"></div>
    </div>
    <div class="col-3">
        <div class="text-center">
            <div class="badge bg-primary text-wrap text-center" style="width: 6rem;">
                Ruta
            </div>
        </div>
        <?php
            $form = ActiveForm::begin([
            'id' => 'route-form',
            'options' => ['class' => 'form-horizontal'],
        ]) ?>
            <?php if(!empty($existingRoutes)) : ?>
            <div class="mb-3">
                <label for="formGroupExampleInput" class="form-label">Routas existentes</label>
                <?= Html::dropDownList("routes_list", $routeForm->id, $existingRoutes, [
                    "class" => "form-select",
                    "id" => "route-changer",
                    "prompt" => "Selecciona una ruta"
                ])?>
            </div>
            <?php endif; ?>
            <?= $form->field($routeForm, 'name') ?>
            <?= $form->field($routeForm, 'directions')->hiddenInput([
                "id" => "directions"
            ])->label(false) ?>
            <?= $form->field($routeForm, 'sourceRequests')->hiddenInput([
                "id" => "source-requests"
            ])->label(false) ?>
            <div id="way-points"></div>
            <div class="form-group">
                <div class="col-12">
                    <?= Html::button('Planear ruta', ['class' => 'btn btn-primary', 'id' => "generate"]) ?>
                    <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Nueva', "/google-maps/routes" , ['class' => 'btn btn-dark']) ?>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
<script>
    function init() {
        const directions = $("#directions").val() ? JSON.parse($("#directions").val()) : [];
        const sourceRequests = $("#source-requests").val() ? JSON.parse($("#source-requests").val()) : [];
        const googleMap = new GoogleMap({
            mapElement : document.getElementById("map"),
            markersElement : document.getElementById("way-points"),
        });
        if (directions) {
            googleMap.route.wayPoints = directions;
            googleMap.generateRoute(sourceRequests);
        }
        $("#route-changer").on("change", function(){
            window.location.href = `<?= Url::current(["route" => null])?>?route=${$(this).val()}`;
        });
        $("#generate").on("click", () => {
            if (googleMap.route.wayPoints.length < 2) {
                alert("Es necesario seleccionar minimo 2 puntos para generar na ruta");
                return;   
            }
            googleMap.generateRoute();
        });
        $("#route-form").on("beforeSubmit", function(e){
            const googleData = googleMap.getData();
            if (googleData.directions.length <= 0) {
                alert("Necesita planear la ruta antes de guardarla");
                return false;
            }
            $("#directions").val(JSON.stringify(googleData.directions));
            $("#source-requests").val(JSON.stringify(googleData.sourceRequests));
            return true;
        });
    }
</script>