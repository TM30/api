<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim();

use Controller\AlertController;
use Controller\UserController;
use Controller\PlatformController;
use Controller\CustomError;

$userController = new UserController();
$platformController = new PlatformController();

$app->config('debug',true);
/*
$app->error(function(\Exception $e){
    CustomError::handleError($e);
});*/

$app->get('/', function(){
    echo "Welcome to API";
});

$app->group('/api', function() use ($app, $userController, $platformController) {

    ///////////////////////////////////////////////////////////////////////////////////////////////////////
                                            //CONTACTS
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

    $app->get('/users', function() use ($app, $userController) {
        echo json_encode($userController->fetchAllUsers());
    });

    $app->get('/user/:id', function($id) use ($app, $userController) {
        echo json_encode($userController->fetchUser(intval($id)));
    });

    $app->post('/user', function() use ($app, $userController) {
        $username = $app->request->post('name');
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $email = $app->request->post('email');
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $role = $app->request->post('role');
        $role = intval($role);

        $userController->createUser(array(
            "name" => $username,
            "email" => $email,
            "role" => $role
        ));
    });

    $app->put('/user/:id', function($id) use ($app, $userController) {
        $username = $app->request->post('name');
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $email = $app->request->post('email');
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $role = $app->request->post('role');
        $role = intval($role);

        $userController->updateUser(array(
            "name" => $username,
            "email" => $email,
            "role" => $role
        ), intval($id));
    });

    $app->delete('/user/:id', function($id) use ($app, $userController) {
        $userController->removeUser(intval($id));
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////
                                                //PLATFORMS
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

    $app->get("/platforms", function() use ($app, $platformController) {
        echo json_encode($platformController->fetchAllPlatforms());
    });

    $app->get("/platform/:id", function($id) use ($app, $platformController) {
        echo json_encode($platformController->fetchPlatforms(intval($id)));
    });

    $app->post("/platform", function() use ($app, $platformController) {
        $name = $app->request->post('name');
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $hostName = $app->request->post('host_name');
        $hostName = filter_var($hostName, FILTER_SANITIZE_STRING);
        $ipAddress = $app->request->post('ip_address');
        $ipAddress = filter_var($ipAddress, FILTER_SANITIZE_STRING);

        $platformController->createPlatform(array(
            "name" => $name,
            "host_name" => $hostName,
            "ip_address" => $ipAddress
        ));
    });

    $app->put("/platform/:id", function($id) use ($app, $platformController) {
        $name = $app->request->post('name');
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $hostName = $app->request->post('host_name');
        $hostName = filter_var($hostName, FILTER_SANITIZE_STRING);
        $ipAddress = $app->request->post('ip_address');
        $ipAddress = filter_var($ipAddress, FILTER_SANITIZE_STRING);

        $platformController->updatePlatform(array(
            "name" => $name,
            "host_name" => $hostName,
            "ip_address" => $ipAddress
        ), intval($id));
    });

    $app->delete("/platform/:id", function($id) use ($app, $platformController) {
        $platformController->removePlatform(intval($id));
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////
                                                //STATUSES
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    $app->get("/status/:platform/:module", function($platformName, $moduleId) use ($app) {
        $statusController = new \Controller\StatusController($platformName);
        echo json_encode($statusController->resolveModule($moduleId));
    });

    $app->get("/uptime/:platform", function($platform) use ($app) {
        $status = \Controller\StatusController::getSevassAppStatus($platform);
        echo json_encode($status);
    });
});

$app->run();