<?php
namespace app\assets;

use yii\web\AssetBundle;

class GoogleAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        "libraries/google-maps/google-maps.js",
        ["https://maps.googleapis.com/maps/api/js?key=AIzaSyCcnaPz8q6U4h3hm5tr71oycOZ1AAMJLOw&callback=init&v=weekly&libraries=places&language=es", "defer" => true]
    ];
    public $css = [
        "libraries/google-maps/google-maps.css",
    ];
}
