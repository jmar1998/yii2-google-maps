<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%route}}`.
 */
class m230204_225633_create_route_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%route}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%route}}');
    }
}
