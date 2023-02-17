<?php

use app\models\Route;
use yii\db\Migration;

/**
 * Class m230217_164032_clean_compressed_data
 */
class m230217_164032_clean_compressed_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $routes = Route::find()->where("source_requests IS NOT NULL")->all();
        /** @var Route */
        foreach ($routes as $route) {
            $route->source_requests = base64_encode(gzencode(gzuncompress(base64_decode($route->source_requests))));
            if (!$route->save()) {
                throw new Exception("Error saving route");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230217_164032_clean_compressed_data cannot be reverted.\n";
        return false;
    }
}
