<?php

namespace App\Providers;

use App\Interfaces\Repositories\CarrierRepositoryInterface;
use App\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Interfaces\Repositories\RouteRepositoryInterface;
use App\Interfaces\Repositories\ShipmentRepositoryInterface;
use App\Repositories\CarrierRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\RouteRepository;
use App\Repositories\ShipmentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ShipmentRepositoryInterface::class, ShipmentRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(CarrierRepositoryInterface::class, CarrierRepository::class);
        $this->app->bind(RouteRepositoryInterface::class, RouteRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
