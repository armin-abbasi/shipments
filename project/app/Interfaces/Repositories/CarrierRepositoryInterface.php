<?php


namespace App\Interfaces\Repositories;


interface CarrierRepositoryInterface
{
    public function create(array $data): array;
}
