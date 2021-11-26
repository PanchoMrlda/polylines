<?php

namespace App\Http\Controllers;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
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
        date_default_timezone_set('Europe/Madrid');
        $tableName = $request->input('tableName');
        $deviceId1 = $request->input('deviceId1');
        $deviceId2 = $request->input('deviceId2');
        $from = $request->input('from');
        $to = $request->input('to');
        $pressureInBars = $request->input('pressureInBars');
        $startHours = empty($request->input('startHours')) ? 0 : intval($request->input('startHours'));
        $endHours = empty($request->input('endHours')) ? 23 : intval($request->input('endHours'));
        if (!empty($from)) {
            // 0 to begin from 0:00, 1 to begin from 1:00, 2 to begin from 2:00...
            $from = strtotime($from) * 1000 + (3600000 * $startHours);
            if (!empty($to)) {
                $to = strtotime($to) * 1000;
            } else {
                // 23 to finish at 23:59, 22 to finish at 22:59, 22 to finish at 21:59...
                $to = (strtotime($request->input('from')) + (60 * (60 * $endHours + 59))) * 1000;
            }
        } else {
            $from = (time() - 60 * 60) * 1000;
            $to = (time() - 60 * 0) * 1000;
        }
        $dynamoQueryParams1 = [
            'tableName' => $tableName,
            'deviceId' => $deviceId1,
            'from' => $from,
            'to' => $to,
            'pressureInBars' => $pressureInBars
        ];
        $dynamoQueryParams2 = [
            'tableName' => $tableName,
            'deviceId' => $deviceId2,
            'from' => $from,
            'to' => $to,
            'pressureInBars' => $pressureInBars
        ];
        $dynamoDbService = new DynamoDbService();
        try {
            $result = [
                'from' => date('Y-m-d', $from / 1000),
                'deviceId1' => $this->getDeviceRelatedData($dynamoDbService->getRawDataFromDynamo($dynamoQueryParams1), $dynamoQueryParams1),
                'deviceId2' => $this->getDeviceRelatedData($dynamoDbService->getRawDataFromDynamo($dynamoQueryParams2), $dynamoQueryParams2)
            ];
            $status = 200;
        } catch (Exception $e) {
            $result = [
                'message' => $e->getMessage()
            ];
            $status = 422;
        }
        return response()->json($result, $status);
    }

    private function getDeviceRelatedData($dynamoDbResult, $options): array
    {
        $dynamoDbService = new DynamoDbService();
        $formattedResult = [
            'deviceName' => '',
            'dates' => [],
            'locations' => [],
            'tempInt' => [],
            'tempExt' => [],
            'highPressure' => [],
            'lowPressure' => [],
            // 'compressor' => $compressor,
            // 'blower' => $blower
            'lastReading' => '',
            'extraData' => [],
            'deviceType' => ''
        ];
        if (empty($dynamoDbResult[0]) && $options['deviceId']) {
            $dynamoDbResult = $dynamoDbService->getRawDataFromDynamo([
                'tableName' => $options['tableName'],
                'deviceId' => $options['deviceId'],
                'from' => 0,
                'to' => $options['from']
            ])[0];
            $payload = $dynamoDbService->transformData($dynamoDbResult[0]['payload'] ?? []);
            $formattedResult['lastReading'] = $dynamoDbService->getDates([$payload])[0] ?? date('Y-m-d H:i:s', 0);
            $formattedResult['deviceName'] = $dynamoDbResult[0]['deviceId'] ?? $options['deviceId'];
            $formattedResult['deviceType'] = $dynamoDbResult[0]['deviceType'] ?? null;
        } else {
            $lastEvaluatedKey = $dynamoDbResult[1];
            $formattedResult = $dynamoDbService->saveDesiredDynamoDbValues($formattedResult, $dynamoDbResult[0]);
            while (!empty($lastEvaluatedKey)) {
                if ($options['emptyReadings'] ?? false) {
                    $options['from'] = $lastEvaluatedKey['readingTimestamp']['N'];
                } else {
                    $options['to'] = $lastEvaluatedKey['readingTimestamp']['N'];
                }
                $dynamoDbResult = $dynamoDbService->getRawDataFromDynamo($options);
                $formattedResult = $dynamoDbService->saveDesiredDynamoDbValues($formattedResult, $dynamoDbResult[0]);
                $lastEvaluatedKey = $dynamoDbResult[1];
            }
        }
        $dynamoDbResult = null;
        return $formattedResult;
    }
}
