<?php

namespace Tests\Unit\Services;

use App\Interfaces\Repositories\CarrierRepositoryInterface;
use App\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Interfaces\Repositories\RouteRepositoryInterface;
use App\Interfaces\Repositories\ShipmentRepositoryInterface;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShipmentServiceTest extends TestCase
{
    private CarrierRepositoryInterface $carrierRepo;
    private CompanyRepositoryInterface $companyRepo;
    private ShipmentRepositoryInterface $shipmentRepo;
    private RouteRepositoryInterface $routeRepo;

    private ShipmentService $shipmentService;
    private array $shipmentObjects;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carrierRepo = $this->createMock(CarrierRepositoryInterface::class);
        $this->companyRepo = $this->createMock(CompanyRepositoryInterface::class);
        $this->shipmentRepo = $this->createMock(ShipmentRepositoryInterface::class);
        $this->routeRepo = $this->createMock(RouteRepositoryInterface::class);

        $this->shipmentService = new ShipmentService(
            $this->carrierRepo,
            $this->companyRepo,
            $this->shipmentRepo,
            $this->routeRepo
        );

        $this->shipmentObjects = json_decode(Storage::disk('local')->get('shipments-test.json'), true);
    }

    /**
     * @test
     */
    public function jsonDataTransfersSuccessfully()
    {
        $companyID = 0;
        $carrierID = 0;

        $startRouteID = 0;
        $endRouteID = 0;

        foreach ($this->shipmentObjects as $shipmentObject) {
            $this->shipmentRepo->expects($this->once())
                ->method('create')
                ->with([
                    'id'         => $shipmentObject['id'],
                    'distance'   => $shipmentObject['distance'],
                    'time'       => $shipmentObject['time'],
                    'price'      => $this->shipmentService->processPriceValue($shipmentObject['distance']),
                    'company_id' => ++$companyID,
                    'carrier_id' => ++$carrierID,
                ]);

            $this->companyRepo->expects($this->once())
                ->method('create')
                ->with([
                    'name'  => $shipmentObject['company']['name'],
                    'email' => $shipmentObject['company']['email'],
                ])
                ->willReturn(['id' => $companyID]);

            $this->carrierRepo->expects($this->once())
                ->method('create')
                ->with([
                    'name'  => $shipmentObject['carrier']['name'],
                    'email' => $shipmentObject['carrier']['email'],
                ])
                ->willReturn(['id' => $carrierID]);

            list($startRoute, $endRoute) = $shipmentObject['route'];

            $this->routeRepo->expects($this->exactly(2))
                ->method('create')
                ->withConsecutive(
                    [$this->equalTo([
                        'stop_id'  => $startRoute['stop_id'],
                        'postcode' => $startRoute['postcode'],
                        'city'     => $startRoute['city'],
                        'country'  => $startRoute['country'],
                        'type'     => 'start',
                    ])], [
                        $this->equalTo([
                            'stop_id'  => $endRoute['stop_id'],
                            'postcode' => $endRoute['postcode'],
                            'city'     => $endRoute['city'],
                            'country'  => $endRoute['country'],
                            'type'     => 'end',
                        ])]
                )
                ->willReturnOnConsecutiveCalls(
                    ['id' => ++$startRouteID],
                    ['id' => ++$endRouteID]
                );

            $this->shipmentRepo->expects($this->exactly(2))
                ->method('attachRoute')
                ->will($this->returnValue(null));

            $this->shipmentService->insertShipments($shipmentObject);
        }
    }

    /**
     * @test
     */
    public function checkPriceCalculations()
    {
        $this->assertEquals(112.76, $this->shipmentService->processPriceValue(751753));
        $this->assertEquals(51.09, $this->shipmentService->processPriceValue(340579));
        $this->assertEquals(59.35, $this->shipmentService->processPriceValue(395670));
    }
}
