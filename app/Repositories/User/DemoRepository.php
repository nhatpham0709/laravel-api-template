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
    public function index(): ?LengthAwarePaginator
    {
        return Demo::paginate(self::PER_PAGE);
    }

    public function show(int $id): ?Demo
    {
        return Demo::find($id);
    }
}