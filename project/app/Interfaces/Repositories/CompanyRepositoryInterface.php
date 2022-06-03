<?php


namespace App\Interfaces\Repositories;


interface CompanyRepositoryInterface
{
    public function create(array $data): array;
}
