<?php

session_start();
include_once 'Request.php';
include_once 'Router.php';

$_SESSION['secretsData'] = json_decode(file_get_contents('secrets.json'), true);
$_SESSION['profile']['mapTypeId'] = 'retro_map';
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
// Define routes
$router = new Router(new Request);
$router->get('/', function ($request) {
  include_once "controllers/dynamoDbController.php";
  include "views/polylines.php";
  // $params = $request->getBody();
  // return json_encode($params);
});
// $router->get('/profile', function ($request) {
//   return json_encode($_SESSION['profile']);
// });
$router->post('/profile', function ($request) {
  // return json_encode($request->getBody());
  $params = $request->getBody();
  foreach ($params as $key => $value) {
    $_SESSION['profile'][$key] = $value;
  }
  return json_encode($params);
});
