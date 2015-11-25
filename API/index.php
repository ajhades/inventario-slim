<?php
header ( 'Content-type: application / json; charset = utf-8' );
date_default_timezone_set('America/Bogota');
require '../vendor/autoload.php';
require('includes/load.php');

$app = new \Slim\Slim();

require_once 'services.php';

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
}

$app->run();
?>