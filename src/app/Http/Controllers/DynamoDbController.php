<?php

namespace App\Http\Controllers;

require __DIR__ . '/../../../vendor/autoload.php';

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Aws\DynamoDbService;

class DynamoDbController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        date_default_timezone_set('UTC');
        $tableName = $request->input('tableName');
        $deviceId1 = $request->input('deviceId1');
        $deviceId2 = $request->input('deviceId2');
        $from = $request->input('from');
        $to = $request->input('to');
        $pressureInBars = $request->input('pressureInBars');
        if (!empty($from)) {
            $from = strtotime($from) * 1000;
            if (!empty($to)) {
                $to = strtotime($to) * 1000;
            } else {
                $to = (strtotime($request->input('from')) + (60 * 1439)) * 1000;
            }
        } else {
            $from = (time() - 60 * 60) * 1000;
            $to = (time() - 60 * 0) * 1000;
        }
        $dynamoQueryParams = [
            'tableName' => $tableName,
            'deviceId' => $deviceId1,
            'from' => $from,
            'to' => $to,
            'pressureInBars' => $pressureInBars
        ];
        $dynamoDbService = new DynamoDbService();
        $deviceId1Payloads = $dynamoDbService->getDataFromDynamo($dynamoQueryParams);
        $deviceId1Data = $this->getDeviceRelatedData($deviceId1Payloads, $dynamoDbService, $dynamoQueryParams);
        $dynamoQueryParams['deviceId'] = $deviceId2;
        $deviceId2Payloads = $dynamoDbService->getDataFromDynamo($dynamoQueryParams);
        $deviceId2Data = $this->getDeviceRelatedData($deviceId2Payloads, $dynamoDbService, $dynamoQueryParams);
        return response()->json(
            [
                'from' => date('Y-m-d', $from / 1000),
                'deviceId1' => $deviceId1Data,
                'deviceId2' => $deviceId2Data
            ]
        );
    }

    private function getDeviceRelatedData(array $messages, DynamoDbService $helper, $options): array
    {
        $tableName = $options['tableName'];
        $deviceId = $options['deviceId'];
        $to = $options['to'];
        $pressureInBars = $options['pressureInBars'];
        $lastReading = null;
        $payloads = $helper->retrievePayloads($messages);
        if (empty(count(collect($payloads)->flatten())) && !empty($deviceId)) {
            $lastReadings = $helper->getDataFromDynamo([
                'tableName' => $tableName,
                'deviceId' => $deviceId,
                'from' => 0,
                'to' => $to
            ]);
            $lastPayloads = array_map(function ($reading) use ($helper) {
                return $helper->transformData($reading);
            }, $helper->retrievePayloads($lastReadings));
            $timestamp = $lastPayloads[count($lastPayloads) - 2]['g']['t'] ?? null;
            $lastReading = date('Y-m-d H:i:s', $timestamp);
        } else {
            $payloads = array_map(function ($payload) use ($helper) {
                return $helper->transformData($payload);
            }, $payloads);
        }
        $dates = $helper->getDates($payloads);
        $locations = $helper->getLocations($payloads);
        $tempInt = $helper->getSensorValues($payloads, '1005n');
        $tempExt = $helper->getSensorValues($payloads, '1004n');
        $highPressure = $helper->getSensorValues($payloads, '1003n');
        $lowPressure = $helper->getSensorValues($payloads, '1002n');
        if (!empty($pressureInBars)) {
            $highPressure = $helper->convertPressureValues($highPressure);
            $lowPressure = $helper->convertPressureValues($lowPressure);
        }
        $deviceType = $messages[0]['deviceType'] ?? null;
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
}
