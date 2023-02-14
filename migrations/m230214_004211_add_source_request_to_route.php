<?php

use yii\db\Migration;

/**
 * Class m230214_004211_add_source_request_to_route
 */
class m230214_004211_add_source_request_to_route extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("route", "source_requests", $this->json()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("route", "source_requests");
    }
}
