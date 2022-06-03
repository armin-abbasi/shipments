<?php

namespace App\Interfaces\Repositories;

interface ShipmentRepositoryInterface
{
    public function create(array $params): array;

    public function getAll(array $params): array;

    public function getOneShipment(int $id): array;

    public function getCarrierShipments(array $params): array;

    public function getCompanyShipments(array $params): array;

    public function getRouteShipments(int $routeID): array;

    public function attachRoute(int $routeID): void;
}
