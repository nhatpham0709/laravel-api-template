<?php

namespace App\Services\User;

use App\Exceptions\ApiException;
use App\Models\Demo;
use App\Repositories\User\DemoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DemoService
{
    private DemoRepository $demoRepository;

    public function __construct(DemoRepository $demoRepository)
    {
        $this->demoRepository = $demoRepository;
    }

    /**
     *
     * @return LengthAwarePaginator|null
     */
    public function getAll(): ?LengthAwarePaginator
    {
        return $this->demoRepository->getAll();
    }

    /**
     *
     * @param integer $id
     * @return Demo|null
     */
    public function findById(int $id): ?Demo
    {
        $demoResponse = $this->demoRepository->findById($id);

        if (!$demoResponse) {
            throw ApiException::notFound('No demo found');
        }

        return $demoResponse;
    }
}