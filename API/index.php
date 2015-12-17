<?php
header ( 'Content-type: application / json; charset = utf-8' );
header("access-control-allow-origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: origin, content-type, accept");
date_default_timezone_set('America/Bogota');
require '../vendor/autoload.php';
require('includes/load.php');
require_once('TokenAuth.php');

$app = new \Slim\Slim();
$autenticacion_v2 =  function ( $role = '' ) {
    return function () use ( $role ) {
        $app = \Slim\Slim::getInstance();
        $tokenAuth = $app->request()->headers()->get('Authorization');
        //Check if our token is valid
        if (validateToken($tokenAuth)) {
            //Get the user and make it available for the controller
           $usrObj = getUserByToken($tokenAuth);
            $app->auth_user = $usrObj;
            if ($usrObj['user_level']=='1' || in_array($usrObj['user_level'], $role)) {
               //Update token's expiration
                keepTokenAlive($tokenAuth);
            } else {
                $response["message"] = "Usuario no autorizado, por favor comuniquese con el Admin";
                echoResponse(401, $response);
            }
            
            
        } else {
            $response["message"] = "Inicie session";
            echoResponse(401, $response);
        }
    };
};
require_once 'auth.php';
require_once 'services.php';

// $app->add(new \TokenAuth());
function borrarToken()
{
    $app = \Slim\Slim::getInstance();
    $tokenAuth = $app->request()->headers()->get('Authorization');
        
        if (validateToken($tokenAuth)) {
           $usrObj = getUserByToken($tokenAuth);
           updateToken($usrObj['id'],'','');
            $app->auth_user = array();
        } else {
            $response["message"] = "Inicie session";
            echoResponse(401, $response);
        }
}

function verifyRequiredParams($required_fields,$request_params) {
    $error = false;
    $error_fields = "";
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["status"] = "error";
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        $response['info'] = $request_params;
        $response['count'] = count($request_params);
        echoResponse(500, $response);
        $app->stop();
    }
}

function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // $app = new \Slim\Slim();
    // Http response code
    $r = $app->response();
    // $app->response()->setStatus($status_code);
    $app->response()->headers->set('Content-Type', 'application/json;charset=utf-8');
    $r->setStatus($status_code);
    // $response->setBody('Foo');

    // setting response content type to json
    // $app->contentType('application/json');
    // $response['status'] = $app->response->getStatus();
    // $r = $response->getBody();
    $r->body(json_encode($response));
    global $db;
    if(isset($db)) { $db->db_disconnect(); }
    $app->stop();

    /**
     * Prepare new response object
     */
    /*$res = new \Slim\Http\Response();
    $res->setStatus($status_code);
    $res->write($response);
    $res->headers->set('Content-Type', 'application/json;charset=utf-8');

    $array = $res->finalize();
    echo json_encode($array);*/
}

$app->run();
?>