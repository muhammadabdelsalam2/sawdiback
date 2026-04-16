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

    public function helpCenter()
    {
        $items = $this->service->getSupportItemtemsgetBymodule('HELP_CENTER');
            
        return SupportItemResource::collection($items);
    }

    public function fqs()
    {
        $items = $this->service->getSupportItemtemsgetBymodule('FAQS');

        return SupportItemResource::collection($items);
    }

    public function contactUs()
    {
        $items = $this->service->getSupportItemtemsgetBymodule('CONTACT_US');

        return SupportItemResource::collection($items);
    }

    public function termsPolicies()
    {
        $items = $this->service->getSupportItemtemsgetBymodule('TERMS_POLICIES');

        return SupportItemResource::collection($items);
    }

    
}
