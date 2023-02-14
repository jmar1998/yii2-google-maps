<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "route".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $source_requests
 *
 * @property Waypoint[] $waypoints
 */
class Route extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source_requests'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'source_requests' => 'Source Requests',
        ];
    }

    /**
     * Gets query for [[Waypoints]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWaypoints()
    {
        return $this->hasMany(Waypoint::class, ['route_id' => 'id']);
    }
}
