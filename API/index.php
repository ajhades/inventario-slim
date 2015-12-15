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

function verifyRequiredParams($required_fields,$request_params, ResponseInterface  $response) {
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
        $output = array();
        // $app = \Slim\Slim::getInstance();
        $output["status"] = "error";
        $output["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        $output['info'] = $request_params;
        $output['count'] = count($request_params);
        return echoResponse(500, $output,$response);
    }
}


$app->add(function ($request, $response, $next) {
    $response = $response->withAddedHeader('Content-type', 'application/json;charset=utf-8');
    // $response =  $response->withStatus(201);
    $response = $next($request, $response);
    return $response;
});

function echoResponse($status_code,$input, ResponseInterface  $response) {
    $response = $response->withStatus($status_code)->write(json_encode($input));
    global $db;
    if(isset($db)) { $db->db_disconnect(); }
    return $response;
    // $app-stop();
}

$app->run();
?>