<?php


namespace App\Repositories;


use App\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Models\Company;
use App\Repositories\Base\BaseRepository;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    /**
     * CompanyRepository constructor.
     * @param Company $companyModel
     */
    public function __construct(Company $companyModel)
    {
        $this->model = $companyModel;
    }
}
