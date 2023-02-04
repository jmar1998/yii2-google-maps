<?php

use app\assets\GoogleAsset;
use yii\bootstrap5\{
    ActiveForm,
    Html
};

GoogleAsset::register($this);

?>
<div class="row h-50">
    <div class="col-2 ">
        <div class="text-center">
            <div class="badge bg-primary text-wrap text-center" style="width: 6rem;">
                Recorrido
            </div>
        </div>
        <ul id="way-points" class="list-group mt-2">
        </ul>
    </div>
    <div class="col-8">
        <div id="map" class="google-map h-100"></div>
    </div>
    <div class="col-2">
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
            <?= $form->field($routeForm, 'name') ?>
            <?= $form->field($routeForm, 'waypoints')->hiddenInput([
                "id" => "waypoints"
            ])->label(false) ?>
            <div id="way-points"></div>
            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::button('Planear ruta', ['class' => 'btn btn-primary', 'id' => "generate"]) ?>
                    <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
<script>
    function init() {
        const wayPoints = $("#waypoints").val() ? JSON.parse($("#waypoints").val()) : [];
        const googleMap = new GoogleMap({
            mapElement : document.getElementById("map"),
            markersElement : document.getElementById("way-points"),
        });
        if (wayPoints) {
            googleMap.route.wayPoints = wayPoints;
            googleMap.generateRoute();
        }
        $("#generate").on("click", () => {
            if (googleMap.route.wayPoints.length < 2) {
                alert("Es necesario seleccionar minimo 2 puntos para generar na ruta");
                return;   
            }
            googleMap.generateRoute();
        });
        $("#route-form").on("beforeSubmit", function(e){
            if (googleMap.routeDrawer.directions === undefined) {
                alert("Necesita planear la ruta antes de guardarla");
                return false;
            }
           $("#waypoints").val(JSON.stringify(googleMap.getData()));
           return true;
        });
    }
</script>