<?php

namespace App\Console\Commands;

use App\Services\EodAPI;
use Illuminate\Console\Command;

class GetStockDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:get {country} {code} {filter?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrive Stock Details from EOD API.';

    protected $api;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EodAPI $api)
    {
        parent::__construct();
        $this->api = $api;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filter = $this->argument('filter') ?? '';
        $response = $this->api->getStockDetails(
            $this->argument('country'),
            $this->argument('code'),
            $filter
        );
        $this->info($response);
    }
}
