<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

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
        return view('polylines');
    }

    /**
     * @return Application|Factory|View
     */
    public function handwriting()
    {
        return view('handwriting');
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