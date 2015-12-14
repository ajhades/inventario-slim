<?php
header ( 'Content-type: application / json; charset = utf-8' );
header("access-control-allow-origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: origin, content-type, accept");
date_default_timezone_set('America/Bogota');
require '../vendor/autoload.php';
require('includes/load.php');

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

$app = new \Slim\App;
require_once 'auth.php';
require_once 'services.php';

/*function verifyRequiredParams($required_fields,$request_params) {
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
        // $app = \Slim\Slim::getInstance();
        $response["status"] = "error";
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        $response['info'] = $request_params;
        $response['count'] = count($request_params);
        echoResponse(500, $response);
        $app->stop();
    }
}*/


$app->add(function ($request, $response, $next) {
    $response = $response->withAddedHeader('Content-type', 'application/json;charset=utf-8');
    $response =  $response->withStatus(201);
    $response = $next($request, $response);
    return $response;
});

function echoResponse($status_code,$r, ResponseInterface  $response) {
    $response = $response->withAddedHeader('Content-type', 'application/json;charset=utf-8');
    $response = $response->withStatus($status_code);
    $body = $response->getBody();
    $body->write(json_encode($r));
    global $db;
    if(isset($db)) { $db->db_disconnect(); }
    return $response;
}

$app->run();
?>