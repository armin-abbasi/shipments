<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    /**
     * @var Model|Builder $model
     */
    public $model;

    protected int $page = 1;
    protected int $perPage = 10;

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        return $this->model
            ->create($data)
            ->toArray();
    }

    /**
     * @param array $params
     * @return array
     */
    protected function paginate(array $params): array
    {
        $page = $params['page'] ?? $this->page;
        $perPage = $params['per_page'] ?? $this->perPage;

        return $this->model
            ->paginate((int)$perPage, ['*'], 'page', (int)$page)
            ->toArray();
    }
}
