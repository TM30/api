<?php

require 'vendor/autoload.php';

$platforms = array(
    "freesia1" => "8585",
    "freesia2" => "8585",
    "mobility" => "8585",
    "starfish" => "8585"
);

//Counter for number of processes
$i = 1;

foreach($platforms as $key => $value) {
    $pid = pcntl_fork();
    if ( ! $pid) {
        echo 'starting child ', $i, PHP_EOL;
        $instance = \Controller\StatusController::getInstance($key, $value);
        file_put_contents("$key.json", json_encode($instance->resolveModule("sms_c")));
        exit();
    }
    $i++;
}

//Wait for all the subprocesses to complete to avoid zombie processes
foreach($platforms as $key)
{
    pcntl_wait($key);
}
