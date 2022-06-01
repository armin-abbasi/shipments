<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shipments\GetAllRequest;
use Illuminate\Http\Request;
use App\Repositories\ShipmentRepository;
use Illuminate\Http\Response;

class ShipmentController extends Controller
{
    public function index(GetAllRequest $request, ShipmentRepository $shipmentRepository)
    {
        $data = $shipmentRepository->getAll($request->all());

        return response($data, Response::HTTP_OK);
    }
}
