<?php
namespace app\assets;

use Exception;
use yii\helpers\Url;
use yii\web\AssetBundle;

class GoogleAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        ["libraries/google-maps/distance-overlay.js", "defer" => true],
        "libraries/google-maps/google-maps.js",
    ];
    public $css = [
        "libraries/google-maps/google-maps.css",
    ];
    public function init()
    {
        if(empty(\Yii::$app->params['googleMapsKey'])){
            throw new Exception("You need to define your google maps key");
        }
        // Allow dynamic key from config
        $googleMapsUrl = sprintf(
            "https://maps.googleapis.com/maps/api/js?key=%s&callback=init&v=weekly&libraries=places&language=es",
            \Yii::$app->params['googleMapsKey']
        );
        $this->js = array_merge([
            [$googleMapsUrl, "defer" => true]
        ], $this->js);
        parent::init();
    }
}
