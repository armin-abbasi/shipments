<?php

namespace App\Console\Commands;

use App\Models\Carrier;
use App\Models\Company;
use App\Models\Route;
use App\Models\Shipment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PopulateShipmentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve shipment information from a json file and store it in relational database.';

    private array $companyEmailMap;

    private array $carrierEmailMap;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $shipmentsJson = Storage::disk('local')->get('shipments.json');
        $shipmentsArray = json_decode($shipmentsJson, true);

        // Creating progress bar to show the advance status.
        $progressBar = $this->output->createProgressBar(count($shipmentsArray));
        $progressBar->setMessage('Inserting shipment records...');
        $progressBar->start();

        foreach ($shipmentsArray as $shipmentObject) {
            $shipment = Shipment::query()->create([
                'id'         => $shipmentObject['id'],
                'distance'   => $shipmentObject['distance'],
                'time'       => $shipmentObject['time'],
                'price'      => $this->processPriceValue($shipmentObject['distance']),
                'company_id' => $this->getCompanyID($shipmentObject['company']),
                'carrier_id' => $this->getCarrierID($shipmentObject['carrier']),
            ]);

            list($startRoute, $endRoute) = $shipmentObject['route'];

            $startRecord = Route::query()->create([
                'stop_id'  => $startRoute['stop_id'],
                'postcode' => $startRoute['postcode'],
                'city'     => $startRoute['city'],
                'country'  => $startRoute['country'],
                'type'     => 'start',
            ]);

            $endRecord = Route::query()->create([
                'stop_id'  => $endRoute['stop_id'],
                'postcode' => $endRoute['postcode'],
                'city'     => $endRoute['city'],
                'country'  => $endRoute['country'],
                'type'     => 'end',
            ]);

            $shipment->routes()->attach($startRecord->id);
            $shipment->routes()->attach($endRecord->id);

            $progressBar->advance();
        }
    }

    private function getCompanyID(array $companyObject): int
    {
        if (isset($this->companyEmailMap[$companyObject['email']])) {
            return $this->companyEmailMap[$companyObject['email']];
        }

        $company = Company::query()->create(
            [
                'email' => $companyObject['email'],
                'name'  => $companyObject['name'],
            ]
        );

        $this->companyEmailMap[$companyObject['email']] = $company->id;

        return $company->id;
    }

    private function getCarrierID(array $carrierObject): int
    {
        if (isset($this->carrierEmailMap[$carrierObject['email']])) {
            return $this->carrierEmailMap[$carrierObject['email']];
        }

        $carrier = Carrier::query()->create(
            [
                'email' => $carrierObject['email'],
                'name'  => $carrierObject['name'],
            ]
        );

        $this->carrierEmailMap[$carrierObject['email']] = $carrier->id;

        return $carrier->id;
    }

    private function processPriceValue(int $distance): float
    {
        $price = 0;

        // Calculate distance in KM
        $distance = (float)($distance / 1000);

        if ($distance <= 100) {
            $price = 30 * $distance;
        } elseif ($distance >= 101 && $distance <= 200) {
            $price = 25 * $distance;
        } elseif ($distance >= 201 && $distance <= 300) {
            $price = 20 * $distance;
        } elseif ($distance >= 301) {
            $price = 15 * $distance;
        }

        // Convert Cents to Euro
        return number_format($price / 100, 2, '.', ' ');
    }
}
