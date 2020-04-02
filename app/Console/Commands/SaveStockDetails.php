<?php

namespace App\Console\Commands;

use App\EodStockDetail;
use App\Services\EodAPI;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SaveStockDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:save {country} {code?} {--all} {--silent} {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Stock Details from EOD API. Data is saved in the database.';

    protected $api;

    protected $specialFields = [
        'General' => [
            'Code', 'CountryISO', 'Type', 'Name', 'Exchange', 'Sector', 'Industry', 'GicSector', 'GicGroup', 'GicIndustry', 'GicSubIndustry'
        ],
    ];

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
        $country = $this->argument('country');
        if ($this->option('all')) {
            if ($this->option('silent') || $this->confirm('Saving ALL stocks. Do you wish to continue?')) {
                $this->saveAllStocks($country);
            }
        } else {
            $code = $this->argument('code') ?? $this->ask('Stock code?');
            $response = $this->api->getStockDetails($country, $code);
            $this->info($response);
        }
    }

    private function saveAllStocks(string $country)
    {
        $this->info('Saving ALL stocks');

        $path = $this->validateInputFile();
        $progressBar = $this->output->createProgressBar($this->getFileAmountOfLines($path));
        $progressBar->start();

        $file = fopen($path, 'r');
        while(!feof($file)) {
            if ($code = trim(fgets($file))) {
                try {
                    $response = $this->api->getStockDetails($country, $code);
                    $stockDetails = json_decode($response, true);

                    $model = EodStockDetail::firstOrNew([
                        'ticker' => $stockDetails['General']['Code'] . '.' . $stockDetails['General']['CountryISO'],
                    ]);

                    // Fill special fields
                    foreach($this->specialFields as $vertical => $verticalSpecialFields) {
                        foreach($verticalSpecialFields as $specialField) {
                            $lowerCaseField = strtolower($specialField);
                            $model->$lowerCaseField = $stockDetails[$vertical][$specialField];
                        }
                    }

                    foreach(array_keys($stockDetails) as $vertical) {
                        $model->$vertical = json_encode($stockDetails[$vertical]);
                    }

                    $model->save();
                } catch (\Exception $e) {
                    $message = "Unexpected error with ticker $code.$country: " . $e->getMessage();
                    Log::error($message);
                    $this->error($message);
                } finally {
                    $progressBar->advance();
                }
            }
        }
        fclose($file);

        $progressBar->finish();
    }

    private function validateInputFile()
    {
        $path = storage_path($this->option('file'));
        while (!file_exists($path)) {
            if ($this->option('silent')) {
                $this->error('Missing file');
                return;
            }
            $path = storage_path($this->ask('Input file path?'));
        }
        return $path;
    }

    private function getFileAmountOfLines($file) {
        $file = new \SplFileObject($file, 'r');
        $file->seek(PHP_INT_MAX);
        return $file->key() + 1;
    }
}
