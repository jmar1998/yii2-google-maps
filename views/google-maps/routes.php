<?php

use app\enums\RouteActions;
use yii\web\View;
use app\forms\RouteForm;

/**
 * @var RouteForm $routeForm
 * @var View $this
 */
?>
<?= $this->render("base", array_merge($_params_, [
    "action" => RouteActions::Routes,
])) ?>