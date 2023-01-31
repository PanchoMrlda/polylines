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
        $validDevices = DB::table('devices')->whereRaw('company_id <> ""');
        $companies = $validDevices->select('company_id')->groupBy('company_id')->get();
        $vehicles = $validDevices->select('vehicle_id')->groupBy('vehicle_id')->get();
        foreach ($companies as $company) {
            foreach ($vehicles as $vehicle) {
                $devices = DB::table('devices')->where([
                    ['company_id', '=', $company->company_id],
                    ['vehicle_id', '=', $vehicle->vehicle_id]
                ])->get();
                if ($devices->count() > 0) {
                    $devicesData[$company->company_id][$vehicle->vehicle_id] = $devices->toArray();
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