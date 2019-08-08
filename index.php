<?php
require 'lib/aws/aws-autoloader.php';
require 'helpers/aws/DynamoDbHelper.php';

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\DynamoDbClient;

// get the ip
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
  // check ip from share internet
  $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  // to check ip is pass from proxy
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
  $ip = $_SERVER['REMOTE_ADDR'];
}

$ipLocation = ((unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ip"))));
if ($ipLocation['geoplugin_timezone']) {
  $defaultTimezone = $ipLocation['geoplugin_timezone'];
} else {
  $defaultTimezone = 'Europe/Madrid';
}
date_default_timezone_set($defaultTimezone);
$secretsData = json_decode(file_get_contents('secrets.json'), true);

$dynamodb = new DynamoDbClient([
  'region' => $secretsData['aws']['region'],
  'version' => 'latest',
  'credentials' => array(
    'key' => $secretsData['aws']['key'],
    'secret' => $secretsData['aws']['secret']
  )
]);

$marshaler = new Marshaler();
$dynamoHelper = new DynamoDbHelper($dynamodb, $marshaler);
$deviceNames = [];
foreach ($secretsData['aws']['deviceNames'] as $array) {
  $key = array_keys($array)[0];
  $deviceNames[$key] = array_values($array[$key]);
}
$deviceId1 = $_GET['deviceId1'];
$deviceId2 = $_GET['deviceId2'];
if (!empty($_GET['from'])) {
  $from = strtotime($_GET['from']) * 1000;
  $to = (strtotime($_GET['from']) + 60 * 1439) * 1000;
} else {
  $from = (time() - 60 * 60) * 1000;
  $to = (time() - 60 * 0) * 1000;
}

try {
  $payloads1 = $dynamoHelper->getDataFromDynamo($deviceId1, $from, $to);
  if (empty(count($payloads1))) {
    $readings1 = $dynamoHelper->getDataFromDynamo($deviceId1, 0, time() * 1000);
    $lastReading1 =  date('Y-m-d H:i:s', $readings1[count($readings1) - 1]['g']['t']);
  }
  $dates1 = $dynamoHelper->getDates($payloads1);
  $locations1 = $dynamoHelper->getLocations($payloads1);
  $tempInt1 = $dynamoHelper->getSensorValues($payloads1, '1005n');
  $tempExt1 = $dynamoHelper->getSensorValues($payloads1, '1004n');
  $highPressure1 = $dynamoHelper->getSensorValues($payloads1, '1003n');
  $lowPressure1 = $dynamoHelper->getSensorValues($payloads1, '1002n');
  $compressor1 = $dynamoHelper->getSensorValues($payloads1, '0004u');
  $blower1 = $dynamoHelper->getSensorValues($payloads1, '0001u');
  $payloads2 = $dynamoHelper->getDataFromDynamo($deviceId2, $from, $to);
  if (empty(count($payloads2)) && !empty($deviceId2)) {
    $readings2 = $dynamoHelper->getDataFromDynamo($deviceId2, 0, time() * 1000);
    $lastReading2 =  date('Y-m-d H:i:s', $readings2[count($readings2) - 1]['g']['t']);
  }
  $locations2 = $dynamoHelper->getLocations($payloads2);
  $tempInt2 = $dynamoHelper->getSensorValues($payloads2, '1005n');
  $tempExt2 = $dynamoHelper->getSensorValues($payloads2, '1004n');
} catch (DynamoDbException $e) {
  echo "Unable to query:\n";
  echo $e->getMessage() . "\n";
}

include 'views/polylines.php';
