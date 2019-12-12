<?php
require 'lib/aws/aws-autoloader.php';
require 'helpers/aws/DynamoDbHelper.php';

use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\DynamoDbClient;

$dynamodb = new DynamoDbClient([
  'region' => $_SESSION['secretsData']['aws']['region'],
  'version' => 'latest',
  'credentials' => array(
    'key' => $_SESSION['secretsData']['aws']['key'],
    'secret' => $_SESSION['secretsData']['aws']['secret']
  )
]);

try {
  $marshaler = new Marshaler();
  $dynamoHelper = new DynamoDbHelper($dynamodb, $marshaler);
  $deviceId1 = $_GET['deviceId1'];
  $deviceId2 = $_GET['deviceId2'];
  if (!empty($_GET['from'])) {
    $from = strtotime($_GET['from']) * 1000;
    if (!empty($_GET['to'])) {
      $to = strtotime($_GET['to']) * 1000;
    } else {
      $to = (strtotime($_GET['from']) + 60 * 1439) * 1000;
    }
  } else {
    $from = (time() - 60 * 60) * 1000;
    $to = (time() - 60 * 0) * 1000;
  }
  // Variables bus 1
  $payloads1 = $dynamoHelper->getDataFromDynamo($deviceId1, $from, $to);
  if (empty(count($payloads1)) && !empty($deviceId1)) {
    $readings1 = $dynamoHelper->getDataFromDynamo($deviceId1, 0, $to);
    $lastReading1 = date('Y-m-d H:i:s', $readings1[count($readings1) - 1]['g']['t']);
  } else {
    $lastReading1 = null;
  }
  $dates1 = $dynamoHelper->getDates($payloads1);
  $locations1 = $dynamoHelper->getLocations($payloads1);
  $tempInt1 = $dynamoHelper->getSensorValues($payloads1, '1005n');
  $tempExt1 = $dynamoHelper->getSensorValues($payloads1, '1004n');
  $highPressure1 = $dynamoHelper->getSensorValues($payloads1, '1003n');
  $lowPressure1 = $dynamoHelper->getSensorValues($payloads1, '1002n');
  // $compressor1 = $dynamoHelper->getSensorValues($payloads1, '0004u');
  // $blower1 = $dynamoHelper->getSensorValues($payloads1, '0001u');

  // Variables bus 2
  $payloads2 = $dynamoHelper->getDataFromDynamo($deviceId2, $from, $to);
  if (empty(count($payloads2)) && !empty($deviceId2)) {
    $readings2 = $dynamoHelper->getDataFromDynamo($deviceId2, 0, time() * 1000);
    $lastReading2 = date('Y-m-d H:i:s', $readings2[count($readings2) - 1]['g']['t']);
  } else {
    $lastReading2 = null;
  }
  $dates2 = $dynamoHelper->getDates($payloads2);
  $locations2 = $dynamoHelper->getLocations($payloads2);
  $tempInt2 = $dynamoHelper->getSensorValues($payloads2, '1005n');
  $tempExt2 = $dynamoHelper->getSensorValues($payloads2, '1004n');
  $highPressure2 = $dynamoHelper->getSensorValues($payloads2, '1003n');
  $lowPressure2 = $dynamoHelper->getSensorValues($payloads2, '1002n');
  // $compressor2 = $dynamoHelper->getSensorValues($payloads2, '0004u');
  // $blower2 = $dynamoHelper->getSensorValues($payloads2, '0001u');

  $dynamoDbData = [
    'from' => date('Y-m-d', $from / 1000),
    'deviceId1' => [
      'deviceName' => $deviceId1,
      'dates1' => $dates1,
      'locations1' => $locations1,
      'tempInt1' => $tempInt1,
      'tempExt1' => $tempExt1,
      'highPressure1' => $highPressure1,
      'lowPressure1' => $lowPressure1,
      // 'compressor1' => $compressor1,
      // 'blower1' => $blower1
      'lastReading' => $lastReading1
    ],
    'deviceId2' => [
      'deviceName' => $deviceId2,
      'dates2' => $dates2,
      'locations2' => $locations2,
      'tempInt2' => $tempInt2,
      'tempExt2' => $tempExt2,
      'highPressure2' => $highPressure2,
      'lowPressure2' => $lowPressure2,
      // 'compressor2' => $compressor2,
      // 'blower2' => $blower2
      'lastReading' => $lastReading2
    ]
  ];
} catch (exception $e) {
  ob_start();
  print_r("\e[31m" . print_r([$e->getMessage()], true) . "\e[0m");
  error_log(ob_get_clean(), 4);
}
