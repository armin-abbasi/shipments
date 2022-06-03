<?php


namespace App\Repositories;


use App\Interfaces\Repositories\CarrierRepositoryInterface;
use App\Models\Carrier;
use App\Repositories\Base\BaseRepository;

class CarrierRepository extends BaseRepository implements CarrierRepositoryInterface
{
    /**
     * CarrierRepository constructor.
     * @param Carrier $carrierModel
     */
    public function __construct(Carrier $carrierModel)
    {
        $this->model = $carrierModel;
    }
}
