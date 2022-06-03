<?php


namespace App\Repositories;


use App\Interfaces\Repositories\RouteRepositoryInterface;
use App\Models\Route;
use App\Repositories\Base\BaseRepository;

class RouteRepository extends BaseRepository implements RouteRepositoryInterface
{
    public function __construct(Route $routeModel)
    {
        $this->model = $routeModel;
    }
}
