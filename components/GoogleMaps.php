<?php
namespace app\components;

use GuzzleHttp\Client;

class GoogleMaps extends  \yii\base\Component {
    private Client $client;
    public function init()
    {
        $this->client = new Client(['base_uri' => 'https://maps.googleapis.com']);
    }
    public function getDirections(array $origin, array $destination){
        return json_decode($this->client->get('/maps/api/directions/json', ["query" => [
            "origin" => "{$origin['lat']},{$origin['lng']}",
            "destination" => "{$destination['lat']},{$destination['lng']}",
            "key" => "AIzaSyCcnaPz8q6U4h3hm5tr71oycOZ1AAMJLOw",
            "mode" => "driving"
        ]])->getBody()->getContents(), true);
    }
}