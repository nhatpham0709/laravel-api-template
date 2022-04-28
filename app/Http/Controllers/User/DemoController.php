<?php

namespace App\Http\Controllers\User;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Repositories\User\DemoRepository;
use App\Transformers\User\DemoPaginationResource;
use App\Transformers\User\DemoResource;

class DemoController extends Controller
{
    protected DemoRepository $demoRepository;

    public function __construct(DemoRepository $demoRepository)
    {
        $this->demoRepository = $demoRepository;
    }

    /**
     * Demo get all function of controller
     *
     * @return DemoPaginationResource
     */
    public function index(): DemoPaginationResource
    {
        $demoResponse = $this->demoRepository->index();
        
        return DemoPaginationResource::make($demoResponse); 
    }

    /**
     * Demo get one function of controller
     *
     * @param integer $id
     * @return DemoResource
     */
    public function show(int $id): DemoResource
    {
        $demoResponse = $this->demoRepository->show($id);

        if (!$demoResponse) {
            throw ApiException::notFound('No demo found');
        }
        
        return DemoResource::make($demoResponse); 
    }
}
