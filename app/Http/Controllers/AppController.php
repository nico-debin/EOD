<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\EodAPI;

class AppController extends Controller
{
    protected $api;

    public function __construct(EodAPI $api)
    {
        $this->api = $api;
    }

    public function getStockDetails(string $country, string $code)
    {
        $response = $this->api->getStockDetails($country, $code);
        return response()->json(json_decode($response, true));
    }
}
