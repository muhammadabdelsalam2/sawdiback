<?php


namespace App\Services\API\Setting;

use App\DTOs\ٍSetting\SupportItemDTO;
use App\Models\SupportItem;
use App\Repositories\Api\Setting\SupportRepository;

class SupportService
{
    public function __construct(
        protected SupportRepository $repository
    ) {
    }

    public function getSupportItems()
    {
        return $this->repository->getAll();
    }

    public function getSupportItemtemsgetBymodule($module)
    {

        return $this->repository->getBymodule($module);
    }
    public function resolve(int $id)
    {
        $item = $this->repository->find($id);

        if (!$item) {
            abort(404);
        }
        
        $meta = $item->meta ?? [];

        // 🧠 1. SCREEN (highest priority)
        if (!empty($item->screen_config)) {
            return [
                'kind' => 'screen',
                'module' => $meta['module'] ?? null,
                'action' => $item->value,
                'config' => $item->screen_config,
            ];
        }

        // 🧠 2. ACTION (logout etc)
        if ($item->value === 'logout') {
            return [
                'kind' => 'action',
                'action' => 'logout',
                'module' => $meta['module'] ?? 'GENERAL',
            ];
        }

        // 🧠 3. ROUTE
        return [
            'kind' => 'route',
            'action' => route($item->value, [
                'locale' => request('locale'),
                'supportItem' => $item->id,
            ]),
            'module' => $meta['module'] ?? null,
        ];
    }

}
