<?php

namespace App\Services\Aws;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\Sdk;

class DynamoDbService
{
    /**
     * @var DynamoDbClient
     */
    private $client;
    /**
     * @var Marshaler
     */
    private $marshaler;
    /**
     * @var array
     */
    private $definedKeys;

    public function __construct()
    {
        $sdk = new Sdk([
            'region' => env('AWS_REGION'),
            'version' => env('AWS_VERSION')
        ]);
        $this->client = $sdk->createDynamoDb();
        $this->marshaler = new Marshaler();
        $this->definedKeys = [
            '1005n' => '2104201',
            '1004n' => '5004201',
            '1003n' => '4093801',
            '1002n' => '4093901',
            '0004u' => '4090501'
        ];
    }

    public function getDataFromDynamo($options = []): array
    {
        $params = $this->getDynamoDbParams($options);
        $formattedResult = [[]];
        try {
            if (!empty($options['deviceId'])) {
                $result = $this->client->query($params);
                $formattedResult = $this->retrieveDynamoDbMessages($formattedResult, $result);
                while (!empty($result['LastEvaluatedKey'])) {
                    if ($options['emptyReadings'] ?? false) {
                        $options['from'] = $result['LastEvaluatedKey']['readingTimestamp']['N'];
                    } else {
                        $options['to'] = $result['LastEvaluatedKey']['readingTimestamp']['N'];
                    }
                    $params = $this->getDynamoDbParams($options);
                    $result = $this->client->query($params);
                    $formattedResult = $this->retrieveDynamoDbMessages($formattedResult, $result);
                }
            }
        } catch (DynamoDbException $e) {
            echo "Unable to query:\n";
            echo $e->getMessage() . "\n";
        }
        return $formattedResult;
    }

    public function getLocations(array $payloads): array
    {
        $result = [];
        if (count(collect($payloads)->flatten()) != 0) {
            foreach ($payloads as $values) {
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
        return $this->cleanCoordinates($result);
    }

    public function getDates(array $payloads): array
    {
        $result = [];
        if (count(collect($payloads)->flatten()) != 0) {
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

    public function getSensorValues(array $payloads, string $sensorName = ''): array
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

    public function convertPressureValues(array $pressureValues): array
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

    public function lowPressureCorrection(array $highPressureValues, array $lowPressureValues): array
    {
        $convertedLowPressureValues = [];
        foreach ($lowPressureValues as $index => $lowPressureValue) {
            if ($this->compressorOn($highPressureValues[$index], $lowPressureValue)) {
                array_push($convertedLowPressureValues, $lowPressureValue - 10);
            } else {
                array_push($convertedLowPressureValues, $lowPressureValue);
            }
        }
        return $convertedLowPressureValues;
    }

    public function compressorOn($highPressure, $lowPressure): bool
    {
        return $highPressure - $lowPressure >= 8;
    }
    public function retrievePayloads(array $messages): array
    {
        return array_map(function ($message) {
            return $message['payload'];
        }, array_filter($messages));
    }

    public function transformData(array $data): array
    {
        if (array_key_exists('current', $data)) {
            $result = [
                'r' => $data['current']['state']['reported'],
                'g' => [
                    't' => $data['current']['state']['reported']['ts'] / 1000,
                    'la' => explode(',', $data['current']['state']['reported']['latlng'])[0],
                    'lo' => explode(',', $data['current']['state']['reported']['latlng'])[1]
                ]
            ];
        } else {
            $result = $data;
        }
        return $result;
    }

    private function cleanCoordinates(array $coordinates): array
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

    private function getDynamoDbParams($options = []): array
    {
        $tableName = $options['tableName'] ?? 'DevicesRichDataTable';
        $scanIndexForward = $options['scanIndexForward'] ?? false;
        $eav = $this->marshaler->marshalJson('
            {
                ":deviceToFind": "' . $options['deviceId'] . '",
                ":fromTimeStamp": ' . $options['from'] . ',
                ":toTimeStamp": ' . $options['to'] . '
            }
        ');
        return [
            'TableName' => $tableName,
            'KeyConditionExpression' => "deviceId = :deviceToFind AND readingTimestamp BETWEEN :fromTimeStamp AND :toTimeStamp",
            'ExpressionAttributeValues' => $eav,
            'ScanIndexForward' => $scanIndexForward
        ];
    }

    private function retrieveDynamoDbMessages(array $formattedResult, $messages): array
    {
        foreach ($messages['Items'] as $message) {
            $data = array(
                'deviceId' => $this->marshaler->unmarshalValue($message['deviceId']),
                'readingTimestamp' => $this->marshaler->unmarshalValue($message['readingTimestamp']),
                'deviceType' => $this->marshaler->unmarshalValue($message['deviceType']),
                'payload' => $this->marshaler->unmarshalValue($message['payload'])
            );
            array_unshift($formattedResult, $data);
        }
        return $formattedResult;
    }
}