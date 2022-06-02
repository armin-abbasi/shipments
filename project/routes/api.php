<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShipmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('shipments', [ShipmentController::class, 'index']);
    Route::get('shipments/{shipment_id}', [ShipmentController::class, 'getOneShipment']);
    Route::get('companies/{company_id}/shipments', [ShipmentController::class, 'companyShipments']);
    Route::get('carriers/{carrier_id}/shipments', [ShipmentController::class, 'carrierShipments']);
    Route::get('routes/{route_id}/shipments', [ShipmentController::class, 'routeShipments']);
});
