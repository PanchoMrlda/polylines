<?php
require 'lib/aws/aws-autoloader.php';
require 'helpers/aws/DynamoDbHelper.php';

use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\DynamoDbClient;

function getDeviceRelatedData(array $payloads, DynamoDbHelper $helper, int $to, string $deviceId = '')
{
    $lastReading = null;
    if (empty(count($payloads)) && !empty($deviceId)) {
        $readings = $helper->getDataFromDynamo($deviceId, 0, $to);
        $lastTimestamp = $readings[count($readings) - 1]['g']['t'];
        $strLastTimestamp = strval($lastTimestamp);
        if (strlen($strLastTimestamp) > 10) {
            $lastTimestamp = intval(substr($strLastTimestamp, 0, 10));
        }
        $lastReading = date('Y-m-d H:i:s', $lastTimestamp);
    }
    $dates = $helper->getDates($payloads);
    $locations = $helper->getLocations($payloads);
    $tempInt = $helper->getSensorValues($payloads, '1005n');
    $tempExt = $helper->getSensorValues($payloads, '1004n');
    $highPressure = $helper->getSensorValues($payloads, '1003n');
    $lowPressure = $helper->getSensorValues($payloads, '1002n');
    if (!empty($_GET['pressureInBars'])) {
        $highPressure = $helper->convertPressureValues($highPressure);
        $lowPressure = $helper->convertPressureValues($lowPressure);
    }
    $deviceType = $helper->deviceType;
    if ($deviceType == 'NEWTON' || $deviceType == 'EINSTEIN') {
        $extraData = $helper->getSensorValues($payloads);
    } else {
        $extraData = [];
    }
    // $compressor = $helper->getSensorValues($payloads, '0004u');
    // $blower = $helper->getSensorValues($payloads, '000u');
    return [
        'deviceName' => $deviceId,
        'dates' => $dates,
        'locations' => $locations,
        'tempInt' => $tempInt,
        'tempExt' => $tempExt,
        'highPressure' => $highPressure,
        'lowPressure' => $lowPressure,
        // 'compressor' => $compressor,
        // 'blower' => $blower
        'lastReading' => $lastReading,
        'extraData' => $extraData,
        'deviceType' => $deviceType
    ];
}

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
    $deviceId1 = empty($_GET) ? '' : $_GET['deviceId1'];
    $deviceId2 = empty($_GET) ? '' : $_GET['deviceId2'];
    $from = null;
    $numHours = empty($_GET['numHours']) ? 23 : intval($_GET['numHours']) - 1;
    if (!empty($_GET['from'])) {
        $from = strtotime($_GET['from']) * 1000;
//        Use this to select one complete week
//        $from = strtotime('this week', strtotime($_GET['from'])) * 1000;
        if (!empty($_GET['to'])) {
            $to = strtotime($_GET['to']) * 1000;
        } else {
            $to = (strtotime($_GET['from']) + 60 * ((60 * $numHours) + 59)) * 1000;
        }
    } else {
        $from = (time() - 60 * 60) * 1000;
        $to = (time() - 60 * 0) * 1000;
    }
    // Variables bus 1
    $payloads1 = $dynamoHelper->getDataFromDynamo($deviceId1, $from, $to);
    $deviceId1Data = getDeviceRelatedData($payloads1, $dynamoHelper, $to, $deviceId1);
    // Variables bus 2
    $payloads2 = $dynamoHelper->getDataFromDynamo($deviceId2, $from, $to);
    $deviceId2Data = getDeviceRelatedData($payloads2, $dynamoHelper, $to, $deviceId2);
    $dynamoDbData = [
        'from' => date('Y-m-d', $from / 1000),
        'deviceId1' => $deviceId1Data,
        'deviceId2' => $deviceId2Data
    ];
} catch (exception $e) {
    ob_start();
    print_r("\e[31m" . print_r([$e->getMessage()], true) . "\e[0m");
    error_log(ob_get_clean(), 4);
}
