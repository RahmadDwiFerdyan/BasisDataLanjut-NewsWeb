<?php 
require '../vendor/autoload.php'; // MongoDB PHP Library

use MongoDB\Client;

session_start();
$client = new Client("mongodb://localhost:27017");
$addAdmin = $client->webberita->admins;
$plainpassword = "cindy123"; 
$hash = password_hash($plainpassword, PASSWORD_DEFAULT);
$newAdmin = [
    'username' => 'cindy',
    'password' => $hash 
];

$addAdmin ->insertOne($newAdmin);