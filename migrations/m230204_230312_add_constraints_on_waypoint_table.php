<?php

use yii\db\Migration;

/**
 * Class m230204_230312_add_constraints_on_waypoint_table
 */
class m230204_230312_add_constraints_on_waypoint_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey("waypoint_fk", "waypoint", "route_id", "route", "id", "CASCADE");
        $this->addForeignKey("waypoint_fk_waypoint_prev", "waypoint", "previous_waypoint", "waypoint", "id", "CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("waypoint_fk", "waypoint");
        $this->dropForeignKey("waypoint_fk_waypoint_prev", "waypoint");
    }
}
