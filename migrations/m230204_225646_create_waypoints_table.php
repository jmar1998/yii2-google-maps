<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%waypoints}}`.
 */
class m230204_225646_create_waypoints_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%waypoint}}', [
            'id' => $this->primaryKey(),
            "route_id" => $this->integer(),
            "address" => $this->text(),
            "next_waypoint_distance" => $this->string(),
            "coordinates" => "point",
            "previous_waypoint" => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%waypoint}}');
    }
}
