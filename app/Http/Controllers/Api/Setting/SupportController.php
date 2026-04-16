<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportItemResource;
use App\Services\API\Setting\SupportService;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    //

    public function __construct(
        protected SupportService $service
    ) {
    }

    public function index()
    {
        
        $items = $this->service->getSupportItems();

        return SupportItemResource::collection($items);
    }
}
