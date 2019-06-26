<?php

class DynamoDbHelper
{
  function __construct(Aws\DynamoDb\DynamoDbClient $dynamodb, Aws\DynamoDb\Marshaler $marshaler)
  {
    $this->dynamodb = $dynamodb;
    $this->marshaler = $marshaler;
  }

  public function getDataFromDynamo(String $deviceId = null, Int $from = null, Int $to = null)
  {
    $payloads = [];
    if (!empty($deviceId)) {
      $params = $this->initParams($deviceId, $from, $to);
      $raw_data = $this->dynamodb->query($params);
      if (count($raw_data['Items']) != 0) {
        $formattedData = $this->getFormattedResult($raw_data);
        $payloads = call_user_func_array('array_merge', array_column($formattedData, 'payload'));
      }
    }
    return $payloads;
  }

  public function getLocations(array $payloads)
  {
    $result = [];
    if (count($payloads) != 0) {
      foreach ($payloads as $values) {
        $result[] = ['lat' => floatval($values['g']['la']), 'lng' => floatval($values['g']['lo'])];
      }
    } else {
      $result[] = ["lat" => 40.41695, "lng" => -3.70321];
    }
    return $result;
  }

  public function getDates(array $payloads)
  {
    $result = [];
    if (count($payloads) != 0) {
      foreach ($payloads as $values) {
        $result[] = date('Y-m-d H:i:s', intval($values['g']['t']));
      }
    }
    return $result;
  }

  public function getSensorValues(array $payloads, String $sensorName)
  {
    $result = [];
    if (count($payloads) != 0) {
      foreach ($payloads as $values) {
        $result[] = floatval($values['r'][$sensorName]);
      }
    }
    return $result;
  }

  private function initParams(String $deviceId, Int $from = null, Int $to = null)
  {
    $from_timestamp = $from || (time() - 60 * 5) * 1000;
    $to_timestamp = $to || (time() - 60 * 0) * 1000;

    $eav = $this->marshaler->marshalJson('
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

    return $params;
  }

  private function getFormattedResult(Aws\Result $dynamoJson)
  {
    $formattedResult = [[]];
    $locations = [];
    foreach ($dynamoJson['Items'] as $message) {
      $data = array(
        'deviceId' => $this->marshaler->unmarshalValue($message['deviceId']),
        'receivedTimeStamp' => $this->marshaler->unmarshalValue($message['receivedTimeStamp']),
        'payload' => $this->marshaler->unmarshalValue($message['payload'])
      );
      array_unshift($formattedResult, $data);
    }
    return array_filter($formattedResult);
  }
}
