<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shipments\GetAllRequest;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Repositories\ShipmentRepository;
use Illuminate\Http\Response;

class ShipmentController extends Controller
{
    protected $repo;

    /**
     * ShipmentController constructor.
     * @param ShipmentRepository $shipmentRepository
     */
    public function __construct(ShipmentRepository $shipmentRepository)
    {
        $this->repo = $shipmentRepository;
    }

    /**
     * @param GetAllRequest $request
     * @return ResponseFactory|Response
     */
    public function index(GetAllRequest $request)
    {
        $data = $this->repo->getAll($request->all());

        return response($data, Response::HTTP_OK);
    }

    /**
     * @param int $shipmentID
     * @return ResponseFactory|Response
     */
    public function getOneShipment(int $shipmentID)
    {
        $data = $this->repo->getOneShipment($shipmentID);

        return response($data, Response::HTTP_OK);
    }

    /**
     * @param int $carrierID
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function carrierShipments(int $carrierID, Request $request)
    {
        $data = $this->repo->getCarrierShipments(array_merge([
            'carrier_id' => $carrierID,
            $request->all(),
        ]));

        return response($data, Response::HTTP_OK);
    }

    /**
     * @param int $companyID
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function companyShipments(int $companyID, Request $request)
    {
        $data = $this->repo->getCompanyShipments(array_merge([
            'company_id' => $companyID,
            $request->all(),
        ]));

        return response($data, Response::HTTP_OK);
    }

    /**
     * @param int $routeID
     * @return ResponseFactory|Response
     */
    public function routeShipments(int $routeID)
    {
        $data = $this->repo->getRouteShipments($routeID);

        return response($data, Response::HTTP_OK);
    }
}
