<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolarPanelsController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate') . ' 00:00';
        $endDate = $request->input('endDate') . ' 23:59';
        // Read data from .csv file
        $rows = array_map('str_getcsv', file(public_path('edp-data.csv')));
        $header = array_shift($rows);
        $csvData = [];
        foreach ($rows as $row) {
            if ($row[0] >= $startDate && $row[0] <= $endDate) {
                $csvData[] = array_combine($header, $row);
            }
        }
        return response()->json($csvData);
    }
}
