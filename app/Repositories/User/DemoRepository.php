<?php

namespace App\Repositories\User;

use App\Models\Demo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DemoRepository
{
    public const PER_PAGE = 10;

    /**
     * Demo repository
     * 
     * @return LengthAwarePaginator|null
     */
    public function getAll(): ?LengthAwarePaginator
    {
        return Demo::paginate(self::PER_PAGE);
    }

    public function findById(int $id): ?Demo
    {
        return Demo::find($id);
    }
}