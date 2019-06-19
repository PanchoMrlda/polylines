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
date_default_timezone_set($ipLocation['geoplugin_timezone']);

$dynamodb = new DynamoDbClient([
  'region' => '',
  'version' => 'latest',
  'credentials' => array(
    'key' => '',
    'secret' => '',
  )
]);

$marshaler = new Marshaler();
$dynamoHelper = new DynamoDbHelper($dynamodb, $marshaler);
$deviceId1 = '190507-078349';
$deviceId2 = '190507-078350';
$from = (time() - 60 * 500) * 1000;
$to = (time() - 60 * 0) * 1000;
echo "The query timestamp is: " . date('d/m/Y H:i', $from / 1000) . ' ' . date('d/m/Y H:i', $to / 1000);

try {
  $payloads1 = $dynamoHelper->getDataFromDynamo($deviceId1, $from, $to);
  $payloads2 = $dynamoHelper->getDataFromDynamo($deviceId2, $from, $to);
} catch (DynamoDbException $e) {
  echo "Unable to query:\n";
  echo $e->getMessage() . "\n";
}

include 'views/polylines.php';
