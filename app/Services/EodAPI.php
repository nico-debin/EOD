<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class EodAPI
{
    protected $httpClient;

    public function __construct(Client $client)
    {
        $this->httpClient = $client;
    }

    public function getStockDetails(string $country, string $code, string $filter = '')
    {
        $ticker = "$code.$country";

        $params = [];
        if ($filter) {
            $params['filter'] = $filter;
        }

        $response = $this->doRequest('GET', "fundamentals/$ticker", $params);
        return $response;
    }

    private function doRequest($method, $url, $params = []) {
        $params = [
            'query' => array_merge(
                ['api_token' => env('EOD_API_KEY')],
                $params
            ),
        ];

        $response = $this->httpClient->request($method, $url, $params);
        return $response->getBody()->getContents();

//        $key = md5($method . $url . serialize($params));
//
//        $ttl = 4 * 60 * 60; // 4 hours
//        return Cache::remember($key, $ttl, function() use ($method, $url, $params) {
//            $response = $this->httpClient->request($method, $url, $params);
//            return $response->getBody()->getContents();
//        });
    }
}
