<?php
require '../vendor/autoload.php';

function connectMongoDB() {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    return $client->webberita->news;
}
?>
