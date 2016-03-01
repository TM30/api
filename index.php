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

        if (file_exists("users.json")) {
            echo file_get_contents('users.json');
            return;
        }
        echo $data = json_encode($userController->fetchAllUsers());
        file_put_contents('users.json', $data);
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
        file_put_contents('users.json', json_encode($userController->fetchAllUsers()));
        echo json_encode(array('message'=>"User has been created  successfully.."));
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
        file_put_contents('users.json', json_encode($userController->fetchAllUsers()));
        echo json_encode(array('message'=>"user updated successfully.."));
    });

    $app->delete('/user/:id', function($id) use ($app, $userController) {
        $userController->removeUser(intval($id));
        file_put_contents('users.json', json_encode($userController->fetchAllUsers()));
        echo json_encode(array('message'=>"User Deleted Successfully..."));
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////
                                                //PLATFORMS
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

    $app->get("/platforms", function() use ($app, $platformController) {

        if (file_exists("platforms.json")) {
            echo file_get_contents('platforms.json');
            return;
        }
        echo $data = json_encode($platformController->fetchAllPlatforms());
        file_put_contents('platforms.json', $data);
    });

    $app->get("/platform/:id", function($id) use ($app, $platformController) {
        echo json_encode($platformController->fetchPlatforms(intval($id))[0]);
    });

    $app->post("/platform", function() use ($app, $platformController) {
        $name = $app->request->post('name');
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $bl_gate = $app->request->post('bl_gate');
        $bl_gate = filter_var($bl_gate, FILTER_VALIDATE_URL);

        $bc_gate = $app->request->post('bc_gate');
        $bc_gate = filter_var($bc_gate, FILTER_VALIDATE_URL);

        $sev_app = $app->request->post('sev_app');
        $sev_app = filter_var($sev_app, FILTER_VALIDATE_URL);

        $tech_admin_email = $app->request->post('tech_admin_email');
        $tech_admin_email = filter_var($tech_admin_email, FILTER_VALIDATE_EMAIL);

        $ops_admin_email = $app->request->post('ops_admin_email');
        $ops_admin_email = filter_var($ops_admin_email, FILTER_VALIDATE_EMAIL);

        $gen_admin_email = $app->request->post('gen_admin_email');
        $gen_admin_email = filter_var($gen_admin_email, FILTER_VALIDATE_EMAIL);

        $ipAddress = $app->request->post('ip_address');
        $ipAddress = filter_var($ipAddress, FILTER_SANITIZE_STRING);

        $platformController->createPlatform(array(
            "name" => $name,
            "ip_address" => $ipAddress,
            "bl_gate" => $bl_gate,
            "bc_gate" => $bc_gate,
            "sev_app" => $sev_app,
            "tech_admin_email" => $tech_admin_email,
            "ops_admin_email" => $ops_admin_email,
            "gen_admin_email" => $gen_admin_email
        ));

        file_put_contents('platforms.json', json_encode($platformController->fetchAllPlatforms()));
        echo json_encode(array('message'=>"Platform has been created  successfully.."));
    });

    $app->put("/platform/:id", function($id) use ($app, $platformController) {
        $name = $app->request->post('name');
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $bl_gate = $app->request->post('bl_gate');
        $bl_gate = filter_var($bl_gate, FILTER_VALIDATE_URL);

        $bc_gate = $app->request->post('bc_gate');
        $bc_gate = filter_var($bc_gate, FILTER_VALIDATE_URL);

        $sev_app = $app->request->post('sev_app');
        $sev_app = filter_var($sev_app, FILTER_VALIDATE_URL);

        $tech_admin_email = $app->request->post('tech_admin_email');
        $tech_admin_email = filter_var($tech_admin_email, FILTER_VALIDATE_EMAIL);

        $ops_admin_email = $app->request->post('ops_admin_email');
        $ops_admin_email = filter_var($ops_admin_email, FILTER_VALIDATE_EMAIL);

        $gen_admin_email = $app->request->post('gen_admin_email');
        $gen_admin_email = filter_var($gen_admin_email, FILTER_VALIDATE_EMAIL);

        $ipAddress = $app->request->post('ip_address');
        $ipAddress = filter_var($ipAddress, FILTER_SANITIZE_STRING);

        $fieldsToUpdate = array();
        if($name)
            $fieldsToUpdate['name'] = $name;
        if($bl_gate)
            $fieldsToUpdate['bl_gate'] = $bl_gate;
        if($bc_gate)
            $fieldsToUpdate['bc_gate'] = $bc_gate;
        if($sev_app)
            $fieldsToUpdate['sev_app'] = $sev_app;
        if($tech_admin_email)
            $fieldsToUpdate['tech_admin_email'] = $tech_admin_email;
        if($ops_admin_email)
            $fieldsToUpdate['ops_admin_email'] = $ops_admin_email;
        if($gen_admin_email)
            $fieldsToUpdate['gen_admin_email'] = $gen_admin_email;
        if($ipAddress)
            $fieldsToUpdate['ip_address'] = $ipAddress;

        $platformController->updatePlatform($fieldsToUpdate, intval($id));

        file_put_contents('platforms.json', json_encode($platformController->fetchAllPlatforms()));
        echo json_encode(array('message'=>"Platform has been updated  successfully.."));

    });

    $app->delete("/platform/:id", function($id) use ($app, $platformController) {
        $platformController->removePlatform(intval($id));

        file_put_contents('platforms.json', json_encode($platformController->fetchAllPlatforms()));
        echo json_encode(array('message'=>"Platform has been deleted successfully.."));
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////
                                                //STATUSES
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    $app->get("/status/:platform/:module", function($platformName, $moduleId) use ($app) {
        $statusController = new \Controller\StatusController($platformName, 8585, $moduleId);
        echo json_encode($statusController->getStatus());
    });

    $app->get("/uptime/:platform", function($platform) use ($app) {
        $status = \Controller\StatusController::getSevassAppStatus($platform);
        echo json_encode($status);
    });
});

$app->run();