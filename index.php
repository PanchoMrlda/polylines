<?php
require 'lib/aws/aws-autoloader.php';

function getFormattedResult($dynamoJson, $marshaler)
{
  $formattedResult = [[]];
  $locations = [];
  foreach ($dynamoJson['Items'] as $message) {
    $data = array(
      'deviceId' => $marshaler->unmarshalValue($message['deviceId']),
      'receivedTimeStamp' => $marshaler->unmarshalValue($message['receivedTimeStamp']),
      'payload' => $marshaler->unmarshalValue($message['payload'])
    );
    array_unshift($formattedResult, $data);
  }
  return array_filter($formattedResult);
}

function getDataFromDynamo(Aws\DynamoDb\DynamoDbClient $dynamodb, $deviceId, $from, $to)
{
  $marshaler = new Marshaler();
  $eav = $marshaler->marshalJson('
    {
      ":deviceToFind": "' . $deviceId . '",
      ":fromTimeStamp": ' . $from . ',
      ":toTimeStamp": ' . $to . '
    }
  ');

  $params = [
    'TableName' => 'DevicesDataTable',
    'KeyConditionExpression' => "deviceId = :deviceToFind AND receivedTimeStamp BETWEEN :fromTimeStamp AND :toTimeStamp",
    'ExpressionAttributeValues' => $eav,
    'ConsistentRead' => false,
    'ScanIndexForward' => false
  ];
}

$jsonData = json_decode(file_get_contents('secrets.json'), true);

date_default_timezone_set('UTC');
$date = date('m/d/Y h:i:s a', time());
// echo "The current server timezone is: " . $date . ' '. time();

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\DynamoDbClient;

$dynamodb = new DynamoDbClient([
  'region' => '',
  'version' => 'latest',
  'credentials' => array(
    'key' => '',
    'secret'  => '',
  )
]);

$marshaler = new Marshaler();

$tableName = 'DevicesDataTable';
$deviceId1 = 'PruebaGIRA52';
$deviceId1 = '190507-078351';
$deviceId2 = '190507-078361';
$from = (time() - 60 * 500) * 1000;
$to = (time() - 60 * 0) * 1000;
echo "The query timestamp is: " . date('d/m/Y H:i', $from / 1000) . ' ' . date('d/m/Y H:i', $to / 1000);

$eav1 = $marshaler->marshalJson('
  {
    ":deviceToFind": "' . $deviceId1 . '",
    ":fromTimeStamp": ' . $from . ',
    ":toTimeStamp": ' . $to . '
  }
');

$params1 = [
  'TableName' => $tableName,
  // 'KeyConditionExpression' => "deviceId = :deviceToFind",
  'KeyConditionExpression' => "deviceId = :deviceToFind AND receivedTimeStamp BETWEEN :fromTimeStamp AND :toTimeStamp",
  'ExpressionAttributeValues' => $eav1,
  'ConsistentRead' => false,
  'ScanIndexForward' => false
];

$eav2 = $marshaler->marshalJson('
  {
    ":deviceToFind": "' . $deviceId2 . '",
    ":fromTimeStamp": ' . $from . ',
    ":toTimeStamp": ' . $to . '
  }
');

$params2 = [
  'TableName' => $tableName,
  // 'KeyConditionExpression' => "deviceId = :deviceToFind",
  'KeyConditionExpression' => "deviceId = :deviceToFind AND receivedTimeStamp BETWEEN :fromTimeStamp AND :toTimeStamp",
  'ExpressionAttributeValues' => $eav2,
  'ConsistentRead' => false,
  'ScanIndexForward' => false
];

try {
  $result1 = $dynamodb->query($params1);
  $result2 = $dynamodb->query($params2);

  $result1 = getDataFromDynamo($dynamodb, $deviceId1, $from, $to);

  if (count($result1['Items']) != 0) {
    $formattedResult1 = getFormattedResult($result1, $marshaler);
    $payloads1 = call_user_func_array('array_merge', array_column($formattedResult1, 'payload'));
  }
  if (count($result2['Items']) != 0) {
    $formattedResult2 = getFormattedResult($result2, $marshaler);
    $payloads2 = call_user_func_array('array_merge', array_column($formattedResult2, 'payload'));
  }
} catch (DynamoDbException $e) {
  echo "Unable to query:\n";
  echo $e->getMessage() . "\n";
}

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

include 'views/polylines.php';
