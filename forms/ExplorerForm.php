<?php
namespace app\forms;

use app\forms\RouteForm;

class ExplorerForm extends RouteForm {
    public $route;
    public $step;
    public $refPoint;
    public function rules()
    {
        return array_merge(parent::rules(), [
            ["step", "integer"]
        ]);
    }
}