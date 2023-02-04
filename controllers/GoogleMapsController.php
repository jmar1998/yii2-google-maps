<?php

namespace app\controllers;

use app\forms\RouteForm;
use app\models\Route;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Class to manage google maps processes
 */
class GoogleMapsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * Action to manage routes from google maps
     *
     * @return string
     */
    public function actionRoutes(?int $route = null)
    {
        $googleRoute = new RouteForm();
        $existingRoutes = ArrayHelper::map(Route::find()->asArray()->all(), 'id', 'name');
        if($this->request->isPost && $googleRoute->load($this->request->post()) && $googleRoute->save($route)){
            return $this->redirect(["routes", "route" => $googleRoute->id]);
        }
        // Load route if exists into the form
        $googleRoute->loadRoute($route);
        return $this->render('index', [
            "routeForm" => $googleRoute,
            "existingRoutes" => $existingRoutes
        ]);
    }
}
