<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,

            // Display data
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'icon' => $this->icon,

            // Behavior config (important for frontend)
            'type' => $this->type,        // route | link | action
            // 'route' => $this->value,       // route name or URL
            // 'params' => $this->meta ?? [],  // dynamic parameters

            // Final resolved action (ready to use)
            'action' => $this->resolveAction(),

            // UI ترتيب
            'order' => $this->order,
        ];


    }

    private function resolveAction()
    {
        if ($this->type !== 'route') {
            return $this->value;
        }

        $params = json_decode($this->meta, true) ?? [];

        // inject locale dynamically
        $params['locale'] = request('locale');
        $params['supportItem'] = $this->id; // pass the item id for dynamic resolution in the route
        return route($this->value, $params);
    }
}
