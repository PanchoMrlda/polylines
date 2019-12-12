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
    $dataComplete = false;
    $payloads = [];
    if (!empty($deviceId)) {
      while (!$dataComplete) {
        $params = $this->initParams($deviceId, $from, $to);
        $rawData = $this->dynamodb->query($params);
        if (count($rawData['LastEvaluatedKey']) > 0) {
          $from = $rawData['LastEvaluatedKey']['readingTimestamp']['N'];
        } else {
          $dataComplete = true;
        }
        if (count($rawData['Items']) != 0) {
          $formattedData = $this->getFormattedResult($rawData);
          if (in_array('L', array_keys($rawData['Items'][0]['payload']))) {
            $extractedData = call_user_func_array('array_merge', array_column($formattedData, 'payload'));
            $payloads = array_merge($payloads, $extractedData);
          } else {
            $extractedData = array_column($formattedData, 'payload');
            $payloads = array_merge($payloads, $extractedData);
          }
        }
      }
    }
    return $payloads;
  }

  public function getLocations(array $payloads)
  {
    $result = [];
    if (count($payloads) != 0) {
      foreach ($payloads as $index => $values) {
        if (in_array('g', array_keys($values))) {
          $result[] = [
            'lat' => floatval($values['g']['la']),
            'lng' => floatval($values['g']['lo'])
          ];
        } else {
          if (empty(array_key_exists('la', $values['i']))) {
            $result[] = [
              'lat' => 0.0,
              'lng' => 0.0
            ];
          } else {
            $result[] = [
              'lat' => floatval($values['i']['la']),
              'lng' => floatval($values['i']['lo'])
            ];
          }
        }
      }
    } else {
      $result[] = ["lat" => 40.446800, "lng" => -3.55802];
    }
    $result = $this->cleanCoordinates($result);
    return $result;
  }

  public function getDates(array $payloads)
  {
    $result = [];
    if (count($payloads) != 0) {
      foreach ($payloads as $values) {
        if (in_array('g', array_keys($values))) {
          $result[] = date('Y-m-d H:i:s', intval($values['g']['t']));
        } else {
          $wrongDate = date('Y-m-d H:i:s', intval($values['i']['t']));
          $hourMinSec = substr($wrongDate, -9);
          $correctTimestamp = strtotime($_GET['from'] . $hourMinSec);
          $result[] = date('Y-m-d H:i:s', $correctTimestamp);
        }
      }
    }
    return $result;
  }

  public function getSensorValues(array $payloads, String $sensorName)
  {
    $result = [];
    if (count($payloads) != 0) {
      foreach ($payloads as $values) {
        if (in_array('r', array_keys($values))) {
          if (in_array($sensorName, array_keys($values['r']))) {
            $result[] = floatval($values['r'][$sensorName]);
          } else {
            if ($sensorName == '1005n') {
              $result[] = floatval($values['r']['ld1temp']) / 4;
            } else if ($sensorName == '1004n') {
              $result[] = floatval($values['r']['exttemp']) / 4;
            }
          }
        }
      }
    }
    return $result;
  }

  public function convertPressureValues(array $pressureValues)
  {
    return array_map(function (Float $value) {
      $p1 = 0.000153;
      $p2 = 0.0213;
      $p3 = 1.528;
      $p4 = 27.81;
      $scale = 0.0689475729;
      $cleanValue = $scale * (($p1 * $value ** 3.0) + ($p2 * $value ** 2.0) + ($p3 * $value) + $p4);
      return round($cleanValue, 2);
    }, $pressureValues);
  }

  private function initParams(String $deviceId, Int $from = null, Int $to = null)
  {
    $eav = $this->marshaler->marshalJson('
      {
        ":deviceToFind": "' . $deviceId . '",
        ":fromTimeStamp": ' . $from . ',
        ":toTimeStamp": ' . $to . '
      }
    ');

    $params = [
      'TableName' => 'DevicesRichDataTable',
      'KeyConditionExpression' => "deviceId = :deviceToFind AND readingTimestamp BETWEEN :fromTimeStamp AND :toTimeStamp",
      'ExpressionAttributeValues' => $eav,
      'ConsistentRead' => false,
      'ScanIndexForward' => false
    ];

    return $params;
  }

  private function getFormattedResult(Aws\Result $dynamoJson)
  {
    $formattedResult = [[]];
    foreach ($dynamoJson['Items'] as $message) {
      $data = array(
        'deviceId' => $this->marshaler->unmarshalValue($message['deviceId']),
        'readingTimestamp' => $this->marshaler->unmarshalValue($message['readingTimestamp']),
        'payload' => $this->marshaler->unmarshalValue($message['payload'])
      );
      array_unshift($formattedResult, $data);
    }
    return array_filter($formattedResult);
  }

  private function cleanCoordinates(array $coordinates)
  {
    foreach ($coordinates as $index => $coordinate) {
      if ($coordinate['lat'] == 0) {
        $nextIndex = $index;
        while ($coordinates[$nextIndex]['lat'] == 0) {
          $nextIndex = $nextIndex + 1;
          if ($nextIndex >= count($coordinates)) {
            $lastCoordinate = $coordinates[$lastIndex];
            $coordinates[$index] = $lastCoordinate;
            break 2;
          }
        }
        $nextCoordinate = $coordinates[$nextIndex];
        $coordinates[$index] = $nextCoordinate;
      } else {
        $lastIndex = $index;
      }
    }
    $wrongIndexes = array_keys(array_filter($coordinates, function ($value) {
      return $value['lat'] == 0;
    }));
    foreach ($wrongIndexes as $index) {
      $coordinates[$index] = $lastCoordinate;
    }
    return $coordinates;
  }
}
