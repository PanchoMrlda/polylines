<?php

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

class DynamoDbHelper
{
    /**
     * @var string
     */
    public $devicesSearchKey;
    /**
     * @var string
     */
    public $devicesTable;
    /**
     * @var string
     */
    public $deviceType;
    /**
     * @var Marshaler
     */
    private $marshaler;
    /**
     * @var DynamoDbClient
     */
    private $dynamodb;
    /**
     * @var array
     */
    private $definedKeys;

    function __construct(Aws\DynamoDb\DynamoDbClient $dynamodb, Aws\DynamoDb\Marshaler $marshaler)
    {
        $this->dynamodb = $dynamodb;
        $this->marshaler = $marshaler;
        $this->devicesTable = 'DevicesRichDataTable';
        $this->devicesSearchKey = 'readingTimestamp';
        $this->deviceType;
        $this->definedKeys = [
            '1005n' => '2104201',
            '1004n' => '5004201',
            '1003n' => '4093801',
            '1002n' => '4093901',
            '0004u' => '4090501'
        ];
    }

    public function getDataFromDynamo(string $deviceId = null, int $from = null, int $to = null)
    {
        $dataComplete = false;
        $payloads = [];
        if (!empty($deviceId)) {
            while (!$dataComplete) {
                $params = $this->initParams($deviceId, $from, $to);
                $rawData = $this->dynamodb->query($params);
                if (!empty($rawData['LastEvaluatedKey']) && count($rawData['LastEvaluatedKey']) > 0) {
                    $from = $rawData['LastEvaluatedKey'][$this->devicesSearchKey]['N'];
                } else {
                    $dataComplete = true;
                }
                if (count($rawData['Items']) !== 0) {
                    $formattedData = $this->getFormattedResult($rawData);
                    $this->deviceType = $formattedData[0]['deviceType'];
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
                    $payloadLocations = $values['i'] ?? [];
                    if (empty(array_key_exists('la', $payloadLocations))) {
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
                    $dateLength = strlen($values['g']['t']);
                    if ($dateLength !== 10) {
                        $substrLength = ($dateLength - 10) * (-1);
                        $correctTimestamp = intval(substr($values['g']['t'], 0, $substrLength));
                    } else {
                        $correctTimestamp = intval($values['g']['t']);
                    }
                    $result[] = date('Y-m-d H:i:s', $correctTimestamp);
                } else {
                    $timestamp = $values['i']['t'] ?? null;
                    $wrongDate = date('Y-m-d H:i:s', intval($timestamp));
                    $hourMinSec = substr($wrongDate, -9);
                    $correctTimestamp = strtotime($_GET['from'] . $hourMinSec);
                    $result[] = date('Y-m-d H:i:s', $correctTimestamp);
                }
            }
        }
        return $result;
    }

    public function getSensorValues(array $payloads, string $sensorName = '')
    {
        $result = [];
        if (count($payloads) != 0) {
            foreach ($payloads as $values) {
                if (in_array('r', array_keys($values)) && !empty($sensorName)) {
                    $possibleNames = [$sensorName, $this->convertSensorNames($sensorName)];
                    $resultNames = array_intersect($possibleNames, array_keys($values['r']));
                    $sensorRealName = array_pop($resultNames);
                    if (!empty($sensorRealName)) {
                        $result[] = floatval($values['r'][$sensorRealName]);
                    } else {
                        $result[] = null;
                    }
                } else if (in_array('r', array_keys($values)) && empty($sensorName)) {
                    $notWantedValues = [];
                    foreach ($this->definedKeys as $definedKey) {
                        $notWantedValues[$definedKey] = 0;
                    }
                    $result[] = array_diff($values['r'], $notWantedValues);
                }
            }
        }
        return $result;
    }

    public function convertPressureValues(array $pressureValues)
    {
        return array_map(function (float $value) {
            $p1 = 0.000153;
            $p2 = 0.0213;
            $p3 = 1.528;
            $p4 = 27.81;
            $scale = 0.0689475729;
            $cleanValue = $scale * (($p1 * $value ** 3.0) + ($p2 * $value ** 2.0) + ($p3 * $value) + $p4);
            return round($cleanValue, 2);
        }, $pressureValues);
    }

    private function initParams(string $deviceId, int $from = null, int $to = null)
    {
        $eav = $this->marshaler->marshalJson('
      {
        ":deviceToFind": "' . $deviceId . '",
        ":fromTimeStamp": ' . $from . ',
        ":toTimeStamp": ' . $to . '
      }
    ');

        $params = [
            'TableName' => $this->devicesTable,
            'KeyConditionExpression' => "deviceId = :deviceToFind AND $this->devicesSearchKey BETWEEN :fromTimeStamp AND :toTimeStamp",
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
                $this->devicesSearchKey => $this->marshaler->unmarshalValue($message[$this->devicesSearchKey]),
                'payload' => $this->transformData($this->marshaler->unmarshalValue($message['payload'])),
                'deviceType' => $this->marshaler->unmarshalValue($message['deviceType'])
            );
            array_unshift($formattedResult, $data);
        }
        return array_filter($formattedResult);
    }

    private function cleanCoordinates(array $coordinates)
    {
        $lastIndex = 0;
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

    private function convertSensorNames(string $sensorName)
    {
        return $this->definedKeys[$sensorName];
    }

    function transformData($data)
    {
        if (array_key_exists('current', $data)) {
            $result = [
                'r' => $data['current']['state']['reported'],
                'g' => [
                    't' => $data['current']['state']['reported']['ts'],
                    'la' => explode(',', $data['current']['state']['reported']['latlng'])[0],
                    'lo' => explode(',', $data['current']['state']['reported']['latlng'])[1]
                ]
            ];
        } else {
            $result = $data;
        }
        return $result;
    }
}
