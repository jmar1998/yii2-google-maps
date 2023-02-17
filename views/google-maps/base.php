<?php

use app\assets\GoogleAsset;
use app\enums\RouteActions;
use app\forms\RouteForm;
use yii\web\View;
GoogleAsset::register($this);
/**
 * @var RouteForm $modelForm
 * @var View $this
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
            <div class="row">
                <div class="col-12">
                    <div class="badge bg-primary text-wrap text-center" style="width: 6rem;">
                        Ruta
                    </div>
                </div>
            </div>
        <?= $this->render(sprintf("forms/%s", $action === RouteActions::Explore ? "explore" : "route"), $_params_) ?>
        </div>
    </div>
</div>
<?php
?>
<script>
    async function init() {
        const currentRoute = <?= json_encode($modelForm->id) ?>;
        const directions = $("#directions").val() ? JSON.parse($("#directions").val()) : [];
        const sourceRequests = currentRoute ? await $.get("get-route-information", {route : currentRoute}) : false;
        const googleMap = new GoogleMap({
            mapElement : document.getElementById("map"),
            markersElement : document.getElementById("way-points"),
        });
        if (directions) {
            googleMap.setTravel(directions);
        }
        if (sourceRequests) {
            googleMap.generateRoute(JSON.parse(sourceRequests));
        }
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