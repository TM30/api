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
        $result = new \Controller\StatusController($platform->name);
        file_put_contents("$platform->name.json", json_encode($result));
        exit();
    }
    $i++;
}