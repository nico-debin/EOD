<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\EodAPI;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class EodServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EodAPI::class, function($app) {
            return new EodAPI(new Client(['base_uri' => env('EOD_API_URL')]));
        });
    }
}
