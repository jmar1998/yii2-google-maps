<?php

namespace app\forms;

use Exception;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\{
    Route,
    Waypoint
};
use yii\db\{
    ActiveQuery,
    Expression
};
use yii\web\NotFoundHttpException;

/**
 * Form class for routes
 * This model is used as a proxy from GoogleMaps functionalities
 * Mainly to save the information and transform it into a relational way
 * Also we handle the conversion between database and view
 */
class RouteForm extends \yii\base\Model
{
    /**
     * Route id
     * @var int
     */
    public $id;
    /**
     * Route name
     * @var string
     */
    public $name;
    /**
     * Original requests
     * @var string
     */
    public $sourceRequests;
    /**
     * Directions
     * @var string
     */
    public $directions;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['sourceRequests', 'directions'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Nombre',
        ];
    }
    public function loadRoute(?int $route){
        if($route === null){
            return;
        }
        /** @var Route */
        $route = Route::find()
            ->where(["route.id" => $route])
            ->innerJoinWith([
                'waypoints' => function(ActiveQuery $query){
                    $query->select([
                        "route_id",
                        new Expression("coordinates[0] as lat"),
                        new Expression("coordinates[1] as lng")
                    ])->asArray();
                }
            ])
            ->one();
        if (empty($route)) {
            throw new NotFoundHttpException("The requested route don't exists!");
        }
        $this->id = $route->id;
        $this->name = $route->name;
        $this->directions = json_encode(array_map(function(array $wayPoint){
            return [
                "lat" => (float) $wayPoint['lat'],
                "lng" => (float) $wayPoint['lng'],
            ];
        }, $route->waypoints));
        // Decompress when is needed
        $this->sourceRequests = empty($route['source_requests']) ? null : gzuncompress(base64_decode($route['source_requests']));
    }
    public function save(?int $route){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $routeModel = $route ? Route::findOne($route) : new Route();
            $routeModel->setAttributes([
                "name" => $this->name,
                // We compress the data because this could be a huge string
                "source_requests" => base64_encode(gzcompress($this->sourceRequests, 9))
            ]);
            if(!$routeModel->save()){
                throw new Exception("Error saving route model");
            }
            if($route){
                // If is performed a save we delete the waypoints related
                Waypoint::deleteAll(["route_id" => $route]);
            }
            /** @var Waypoint|null */
            $previousWaypoint = null;
            foreach (json_decode($this->directions, true) as $wayPoint) {
                $wayPointModel = new Waypoint();
                $wayPointModel->setAttributes([
                    "address" => $wayPoint['address'],
                    "next_waypoint_distance" => $wayPoint['distanceToNextPoint']['text'] ?? null,
                    "coordinates" => sprintf("(%s, %s)", $wayPoint['location']['lat'], $wayPoint['location']['lng']),
                    "previous_waypoint" => $previousWaypoint->id ?? null
                ]);
                $routeModel->link('waypoints', $wayPointModel);
                // Keep the previous reference to set previous_waypoint
                $previousWaypoint = $wayPointModel;
            }
            $transaction->commit();
            $this->id = $routeModel->id;
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return false;
    }
}
