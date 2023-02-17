<?php


use app\forms\RouteForm;

use yii\web\View;

use app\enums\RouteActions;

/**
 * @var RouteForm $routeForm
 * @var View $this
 */
?>
<?= $this->render("base", array_merge($_params_, [
    "action" => RouteActions::Explore,
])) ?>;
