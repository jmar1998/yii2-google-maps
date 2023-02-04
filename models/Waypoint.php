<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "waypoint".
 *
 * @property int $id
 * @property int $route_id
 * @property string|null $address
 * @property string|null $coordinates
 * @property string|null $next_waypoint_distance
 * @property int|null $previous_waypoint
 *
 * @property Waypoint $previousWaypoint
 * @property Route $route
 * @property Waypoint[] $waypoints
 */
class Waypoint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'waypoint';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id'], 'required'],
            [['route_id', 'previous_waypoint'], 'default', 'value' => null],
            [['route_id', 'previous_waypoint'], 'integer'],
            [['address', 'coordinates', 'next_waypoint_distance'], 'string'],
            [['route_id'], 'exist', 'skipOnError' => true, 'targetClass' => Route::class, 'targetAttribute' => ['route_id' => 'id']],
            [['previous_waypoint'], 'exist', 'skipOnError' => true, 'targetClass' => Waypoint::class, 'targetAttribute' => ['previous_waypoint' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route_id' => 'Route ID',
            'address' => 'Address',
            'coordinates' => 'Coordinates',
            'next_waypoint_distance' => 'Next Waypoint Distance',
            'previous_waypoint' => 'Previous Waypoint',
        ];
    }

    /**
     * Gets query for [[PreviousWaypoint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPreviousWaypoint()
    {
        return $this->hasOne(Waypoint::class, ['id' => 'previous_waypoint']);
    }

    /**
     * Gets query for [[Route]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoute()
    {
        return $this->hasOne(Route::class, ['id' => 'route_id']);
    }

    /**
     * Gets query for [[Waypoints]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWaypoints()
    {
        return $this->hasMany(Waypoint::class, ['previous_waypoint' => 'id']);
    }
}
