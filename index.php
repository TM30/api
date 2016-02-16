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
        echo json_encode($userController->fetchUser(intval($id))[0]);
    })->conditions(array("id" => "[0-9]+"));

    $app->get('/user/:email', function($email) use ($app, $userController) {
        $email = str_replace('%', '.', $email);
        echo json_encode($userController->fetchUserByMail(($email))[0]->id);
    });

    $app->post('/user', function() use ($app, $userController) {
        $username = $app->request->post('name');
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $email = $app->request->post('email');
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $role = $app->request->post('role');
        /*$role = intval($role);*/

        $password = $app->request->post('password');

        $userController->createUser(array(
            "name" => $username,
            "email" => $email,
            "role" => $role,
            "password" => $password
        ));
    });

    $app->put('/user/:id', function($id) use ($app, $userController) {
        $username = $app->request->post('name');
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $email = $app->request->post('email');
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $role = $app->request->post('role');
        /*$role = intval($role);*/

        $password = $app->request->post('password');

        $fieldsToUpdate = array();
        if($username)
            $fieldsToUpdate['name'] = $username;
        if($email)
            $fieldsToUpdate['email'] = $email;
        if($role)
            $fieldsToUpdate['role'] = $role;
        if($password)
            $fieldsToUpdate['password'] = $password;
        $userController->updateUser($fieldsToUpdate, intval($id));
        echo json_encode("updated successfully");
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
        echo json_encode($platformController->fetchPlatforms(intval($id))[0]);
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
        $host_name = $app->request->post('host_name');
        $host_name = filter_var($host_name, FILTER_SANITIZE_STRING);
        $ip_address = $app->request->post('ip_address');
        $ip_address = filter_var($ip_address, FILTER_SANITIZE_STRING);

        $fieldsToUpdate = array();
        if($name)
            $fieldsToUpdate['name'] = $name;
        if($host_name)
            $fieldsToUpdate['host_name'] = $host_name;
        if($ip_address)
            $fieldsToUpdate['ip_address'] = $ip_address;

        $platformController->updatePlatform($fieldsToUpdate, intval($id));

        echo json_encode("updated successfully");
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