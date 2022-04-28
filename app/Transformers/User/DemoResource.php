<?php

namespace App\Transformers\User;

use App\Transformers\SuccessResource;
use Illuminate\Http\Request;

class DemoResource extends SuccessResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
