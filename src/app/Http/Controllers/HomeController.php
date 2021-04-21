<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomeController
{
    /**
     * HomeController constructor.
     * @param
     */
    public function __construct()
    {

    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $devicesData = [[]];
        $validDevices = DB::table('devices')->whereRaw('companyId <> ""');
        $companies = $validDevices->select('companyId')->groupBy('companyId')->get();
        $vehicles = $validDevices->select('vehicleId')->groupBy('vehicleId')->get();
        foreach ($companies as $company) {
            foreach ($vehicles as $vehicle) {
                $devices = DB::table('devices')->where([
                    ['companyId', '=', $company->companyId],
                    ['vehicleId', '=', $vehicle->vehicleId]
                ])->get();
                if ($devices->count() > 0) {
                    $devicesData[$company->companyId][$vehicle->vehicleId] = $devices->toArray();
                }
            }
        }
        return view('polylines', [
            'devicesData' => $devicesData
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function handwriting()
    {
        return view('handwriting');
    }

    /**
     * @return Application|Factory|View
     */
    public function raspberryPi()
    {
        return view('raspberryPi');
    }

    /**
     * @return JsonResponse
     */
    public function profile()
    {
        return response()->json([
            'mapTypeId' => 'retro_map'
        ]);
    }
}