<?php

require 'vendor/autoload.php';

$platforms = new \Controller\PlatformController();
$platforms = $platforms->fetchAllPlatformsWithColumn("name");

//Counter for number of processes
$i = 1;

foreach($platforms as $platform) {
    $pid = pcntl_fork();
    if ( ! $pid) {
        echo 'starting child ', $i, PHP_EOL;
        $controllerInstance = new \Controller\StatusController($platform->name);
        file_put_contents("/var/www/html/demo/seethru/$platform->name.json", json_encode($controllerInstance->getStatus()));
        exit();
    }
    $i++;
}