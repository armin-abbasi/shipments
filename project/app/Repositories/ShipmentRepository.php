<?php

namespace App\Repositories;

use App\Models\Carrier;
use App\Models\Company;
use App\Models\Shipment;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Interfaces\Repositories\ShipmentRepositoryInterface;

class ShipmentRepository extends BaseRepository implements ShipmentRepositoryInterface
{
    const ALL_RELATIONS = [
        'carrier:id,name,email',
        'company:id,name,email',
        'routes:id,stop_id,city,country,postcode'
    ];

    /**
     * ShipmentRepository constructor.
     * @param Shipment|Builder $shipmentModel
     */
    public function __construct(Shipment $shipmentModel)
    {
        $this->model = $shipmentModel;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getAll(array $params): array
    {
        $this->model = $this->model
            ->with(self::ALL_RELATIONS);

        if (!empty($params['carrier'])) {
            $carrierIDs = Carrier::query()
                ->where('email', 'like', "%{$params['carrier']}%")
                ->orWhere('name', 'like', "%{$params['carrier']}%")
                ->get('id');

            $this->model = $this->model->whereIn('carrier_id', $carrierIDs);
        }

        if (!empty($params['company'])) {
            $companyIDs = Company::query()
                ->where('email', 'like', "%{$params['company']}%")
                ->orWhere('name', 'like', "%{$params['company']}%")
                ->get('id');

            $this->model = $this->model->whereIn('company_id', $companyIDs);
        }

        if (!empty($params['stop_address'])) {
            $address = $params['stop_address'];

            $this->model = $this->model->whereHas('routes', function (Builder $query) use ($address) {
                return $query
                    ->where('type', 'end')
                    ->where(function (Builder $query) use ($address) {
                        return $query
                            ->orWhere('city', 'like', "%{$address}%")
                            ->orWhere('country', 'like', "%{$address}%");
                    });
            });
        }

        return $this->paginate($params);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getOneShipment(int $id): array
    {
        return $this->model->with(self::ALL_RELATIONS)->findOrFail($id)->toArray();
    }

    /**
     * @param array $params
     * @return array
     */
    public function getCarrierShipments(array $params): array
    {
        $this->model = $this->model->with(self::ALL_RELATIONS)
            ->where('carrier_id', $params['carrier_id']);

        return $this->paginate($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getCompanyShipments(array $params): array
    {
        $this->model = $this->model->with(self::ALL_RELATIONS)
            ->where('company_id', $params['company_id']);

        return $this->paginate($params);
    }

    /**
     * @param int $routeID
     * @return array
     */
    public function getRouteShipments(int $routeID): array
    {
        return $this->model->with(self::ALL_RELATIONS)
            ->whereHas('routes', function (Builder $query) use ($routeID) {
                return $query->where('id', $routeID);
            })->get()->toArray();
    }

    /**
     * @param int $shipmentID
     * @param int $routeID
     */
    public function attachRoute(int $shipmentID, int $routeID): void
    {
        $this->model->findOrFail($shipmentID)->routes()->attach($routeID);
    }
}
