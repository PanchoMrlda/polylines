<?php

ini_set('memory_limit', '256M');
require 'lib/qr/phpqrcode.php';
session_name('polyliner');
session_start();
include_once 'Request.php';
include_once 'Router.php';

$_SESSION['secretsData'] = json_decode(file_get_contents('secrets.json'), true);
if (empty($_SESSION['profile']['mapTypeId'])) {
    $_SESSION['profile']['mapTypeId'] = 'retro_map';
}
// Get the ip address
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    // check ip from share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // to check ip is pass from proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
// Get geolocation
$ipLocation = ((unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ip"))));
if ($ipLocation['geoplugin_timezone']) {
    $defaultTimezone = $ipLocation['geoplugin_timezone'];
} else {
    $defaultTimezone = 'Europe/Madrid';
}
date_default_timezone_set($defaultTimezone);
$router = new Router(new Request);

// Define GET routes
$router->get('/handwriting', function () {
    include "views/handwriting.php";
});
$router->get('/devices/config', function () {
    include "views/devicesConfig.php";
});
$router->get('/qr', function () {
    $localIpAddress = exec("ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1'");
    echo QRcode::png("http://$localIpAddress:8000/?from=2019-04-03&deviceId1=DTEST_GIRA02");
//    $serverUrl = $_SERVER['HTTP_HOST'];
//  echo QRcode::png("http://$serverUrl/?from=2019-04-03&deviceId1=DTEST_GIRA02");
});
$router->get('/dynamo', function () {
    $dynamoDbData = null;
    include_once "controllers/dynamoDbController.php";
    return json_encode($dynamoDbData);
});
$router->get('/profile', function () {
    return json_encode($_SESSION['profile']);
});
$router->get('/', function () {
    include_once "controllers/dynamoDbController.php";
    include "views/polylines.php";
});

// Define POST routes
$router->post('/profile', function () {
    $jsonParams = file_get_contents('php://input');
    $params = json_decode($jsonParams, true);
    foreach ($params as $key => $value) {
        $_SESSION['profile'][$key] = $value;
    }
    return json_encode($params);
});
$router->post('/config', function () {
    // Handling data in JSON format on the server-side using PHP
    header('Content-type: application/json');
    $jsonParams = file_get_contents('php://input');
    $params = json_decode($jsonParams, true);
    include_once "controllers/devicesController.php";
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename('config.hvc'));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize('config.hvc'));
});
