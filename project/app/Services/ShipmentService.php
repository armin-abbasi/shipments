<?php


namespace App\Services;


use App\Interfaces\Repositories\CarrierRepositoryInterface;
use App\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Interfaces\Repositories\RouteRepositoryInterface;
use App\Interfaces\Repositories\ShipmentRepositoryInterface;

class ShipmentService
{
    public CarrierRepositoryInterface $carrierRepo;
    public CompanyRepositoryInterface $companyRepo;
    public ShipmentRepositoryInterface $shipmentRepo;
    public RouteRepositoryInterface $routeRepo;

    private array $companyEmailMap;
    private array $carrierEmailMap;

    /**
     * ShipmentService constructor.
     * @param CarrierRepositoryInterface $carrierRepository
     * @param CompanyRepositoryInterface $companyRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param RouteRepositoryInterface $routeRepository
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        CompanyRepositoryInterface $companyRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        RouteRepositoryInterface $routeRepository
    )
    {
        $this->carrierRepo = $carrierRepository;
        $this->companyRepo = $companyRepository;
        $this->shipmentRepo = $shipmentRepository;
        $this->routeRepo = $routeRepository;
    }

    /**
     * @param array $shipmentObject
     */
    public function insertShipments(array $shipmentObject): void
    {
        $this->shipmentRepo->create([
            'id'         => $shipmentObject['id'],
            'distance'   => $shipmentObject['distance'],
            'time'       => $shipmentObject['time'],
            'price'      => $this->processPriceValue($shipmentObject['distance']),
            'company_id' => $this->getCompanyID($shipmentObject['company']),
            'carrier_id' => $this->getCarrierID($shipmentObject['carrier']),
        ]);

        list($startRouteID, $endRouteID) = $this->createRoute($shipmentObject);

        $this->shipmentRepo->attachRoute($startRouteID);
        $this->shipmentRepo->attachRoute($endRouteID);
    }

    /**
     * @param int $distance
     * @return float
     */
    public function processPriceValue(int $distance): float
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

    /**
     * @param array $companyObject
     * @return int
     */
    private function getCompanyID(array $companyObject): int
    {
        if (isset($this->companyEmailMap[$companyObject['email']])) {
            return $this->companyEmailMap[$companyObject['email']];
        }

        $company = $this->companyRepo->create(
            [
                'email' => $companyObject['email'],
                'name'  => $companyObject['name'],
            ]
        );

        $this->companyEmailMap[$companyObject['email']] = $company['id'];

        return $company['id'];
    }

    /**
     * @param array $carrierObject
     * @return int
     */
    private function getCarrierID(array $carrierObject): int
    {
        if (isset($this->carrierEmailMap[$carrierObject['email']])) {
            return $this->carrierEmailMap[$carrierObject['email']];
        }

        $carrier = $this->carrierRepo->create(
            [
                'email' => $carrierObject['email'],
                'name'  => $carrierObject['name'],
            ]
        );

        $this->carrierEmailMap[$carrierObject['email']] = $carrier['id'];

        return $carrier['id'];
    }

    /**
     * @param array $shipmentObject
     * @return array
     */
    private function createRoute(array $shipmentObject): array
    {
        list($startRoute, $endRoute) = $shipmentObject['route'];

        $startRoute = $this->routeRepo->create([
            'stop_id'  => $startRoute['stop_id'],
            'postcode' => $startRoute['postcode'],
            'city'     => $startRoute['city'],
            'country'  => $startRoute['country'],
            'type'     => 'start',
        ]);

        $endRoute = $this->routeRepo->create([
            'stop_id'  => $endRoute['stop_id'],
            'postcode' => $endRoute['postcode'],
            'city'     => $endRoute['city'],
            'country'  => $endRoute['country'],
            'type'     => 'end',
        ]);

        return [$startRoute['id'], $endRoute['id']];
    }
}
