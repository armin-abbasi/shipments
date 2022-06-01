<?php

namespace App\Repositories;

use App\Models\Carrier;
use App\Models\Company;
use App\Models\Route;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Builder;

class ShipmentRepository
{
    private int $page = 1;
    private int $perPage = 10;

    public function getAll(array $params): array
    {
        $shipmentQuery = Shipment::query()
            ->with([
                'carrier:id,name,email',
                'company:id,name,email',
                'routes:id,stop_id,city,country,postcode'
            ]);

        if (!empty($params['carrier'])) {
            $carrierIDs = Carrier::query()
                ->where('email', 'like', "%{$params['carrier']}%")
                ->orWhere('name', 'like', "%{$params['carrier']}%")
                ->get('id');

            $shipmentQuery->whereIn('carrier_id', $carrierIDs);
        }

        if (!empty($params['company'])) {
            $companyIDs = Company::query()
                ->where('email', 'like', "%{$params['company']}%")
                ->orWhere('name', 'like', "%{$params['company']}%")
                ->get('id');

            $shipmentQuery->whereIn('company_id', $companyIDs);
        }

        if (!empty($params['stop_address'])) {
            $address = $params['stop_address'];

            $shipmentQuery->whereHas('routes', function (Builder $query) use ($address) {
                return $query
                    ->where('type', 'end')
                    ->where(function (Builder $query) use ($address) {
                        return $query
                            ->orWhere('city', 'like', "%{$address}%")
                            ->orWhere('country', 'like', "%{$address}%");
                    });
            });
        }

        $page = $params['page'] ?? $this->page;
        $perPage = $params['per_page'] ?? $this->perPage;

        return $shipmentQuery->paginate((int)$perPage, ['*'], 'page', (int)$page)->toArray();
    }
}
