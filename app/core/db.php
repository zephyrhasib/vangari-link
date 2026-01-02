<?php
static $connection = null;

if ($connection instanceof mysqli){
    return $connection;
}

$config = require __DIR__ .'/../config/db.php';

$connection = new mysqli(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['dbname']

);

if ($connection->connect_error){
    die("Failed to connect database" . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

return $connection;