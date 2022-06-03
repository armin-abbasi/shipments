<?php

namespace App\Console\Commands;

use App\Services\ShipmentService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
     * @param ShipmentService $shipmentService
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(ShipmentService $shipmentService)
    {
        $shipmentsJson = Storage::disk('local')->get('shipments.json');
        $shipmentsArray = json_decode($shipmentsJson, true);

        // Creating progress bar to show the advance status.
        $progressBar = $this->output->createProgressBar(count($shipmentsArray));
        $progressBar->setMessage('Inserting shipment records...');
        $progressBar->start();

        foreach ($shipmentsArray as $shipmentObject) {
            $shipmentService->insertShipments($shipmentObject);
            $progressBar->advance();
        }
    }
}
