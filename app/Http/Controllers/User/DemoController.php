<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\DemoService;
use App\Transformers\User\DemoPaginationResource;
use App\Transformers\User\DemoResource;

class DemoController extends Controller
{
    private DemoService $demoService;

    public function __construct(DemoService $demoService)
    {
        $this->demoService = $demoService;
    }

    /**
     * Demo get all function of controller
     *
     * @return DemoPaginationResource
     */
    public function index(): DemoPaginationResource
    {
        $demoResponse = $this->demoService->getAll();
        
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
        $demoResponse = $this->demoService->findById($id);
        
        return DemoResource::make($demoResponse); 
    }
}
