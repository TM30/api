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
        $instance = \Controller\StatusController::getInstance($platform->name);
        file_put_contents("$platform->name.json", json_encode($instance->resolveModule("sms_c")));
        exit();
    }
    $i++;
}

//Wait for all the subprocesses to complete to avoid zombie processes
foreach($platforms as $key)
{
    pcntl_wait($key);
}
