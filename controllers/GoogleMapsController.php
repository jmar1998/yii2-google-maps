<?php

namespace app\controllers;

use app\forms\ExplorerForm;
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
        if($this->request->isPost && $googleRoute->load($this->request->post()) && $googleRoute->save($route)){
            return $this->redirect(["routes", "route" => $googleRoute->id]);
        }
        // Load route if exists into the form
        $googleRoute->loadRoute($route);
        return $this->render('routes', [
            "modelForm" => $googleRoute,
            "existingRoutes" => $this->getExistingRoutes()
        ]);
    }
    public function actionGetRouteInformation(int $route){
        $this->response->headers->set("Content-Encoding", "gzip");
        $route = Route::findOne($route);
        return empty($route->source_requests) ? gzencode(json_encode([])) : base64_decode($route->source_requests);
    }
    public function actionExplore(int $step = 1){
        $explorerForm = new ExplorerForm(["step" => $step]);
        if($explorerForm->load($this->request->post()) && $explorerForm->loadRoute($explorerForm->id)){
            $explorerForm->step++;
            // Let this part to implement save
        }
        return $this->render('explore', [
            "existingRoutes" => $this->getExistingRoutes(),
            "modelForm" => $explorerForm,
        ]);
    }
    private function getExistingRoutes(){
        return ArrayHelper::map(Route::find()->asArray()->all(), 'id', 'name');
    }
}
