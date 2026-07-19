<?php

namespace App\Http\Controllers\Customer\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Warehouse\WarehouseAssetStoreRequest;
use App\Http\Requests\Customer\Warehouse\WarehouseAssetUpdateRequest;
use App\Models\Farm;
use App\Models\WarehouseAsset;
use App\Models\WarehouseAssetAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WarehouseAssetController extends Controller
{
    public function index(string $locale): View
    {
        $tenantId = auth()->user()?->tenant_id;
        $assets = WarehouseAsset::query()
            ->with(['farm', 'attachments'])
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->paginate(15);

        return view('dashboard.customer.warehouse.index', compact('assets'));
    }

    public function create(string $locale): View
    {
        $farms = Farm::query()->where('tenant_id', auth()->user()?->tenant_id)->get();

        return view('dashboard.customer.warehouse.create', compact('farms'));
    }

    public function store(WarehouseAssetStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = auth()->user()?->tenant_id;
        $asset = WarehouseAsset::query()->create([
            'tenant_id' => $tenantId,
            'farm_id' => $request->input('farm_id'),
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'storage_location' => $request->input('storage_location'),
            'quantity_or_status' => $request->input('quantity_or_status'),
            'notes' => $request->input('notes'),
        ]);

        $this->storeAttachments($request, $asset, $tenantId);

        return redirect()->route('customer.warehouse-assets.index', ['locale' => $locale])
            ->with('success', __('warehouse.messages.success.asset_created'));
    }

    public function edit(string $locale, WarehouseAsset $warehouseAsset): View
    {
        $farms = Farm::query()->where('tenant_id', auth()->user()?->tenant_id)->get();
        $warehouseAsset->load('attachments');

        return view('dashboard.customer.warehouse.edit', compact('warehouseAsset', 'farms'));
    }

    public function update(WarehouseAssetUpdateRequest $request, string $locale, WarehouseAsset $warehouseAsset): RedirectResponse
    {
        $warehouseAsset->update([
            'farm_id' => $request->input('farm_id'),
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'storage_location' => $request->input('storage_location'),
            'quantity_or_status' => $request->input('quantity_or_status'),
            'notes' => $request->input('notes'),
        ]);

        $this->storeAttachments($request, $warehouseAsset, (string) $warehouseAsset->tenant_id);

        return redirect()->route('customer.warehouse-assets.index', ['locale' => $locale])
            ->with('success', __('warehouse.messages.success.asset_updated'));
    }

    public function destroy(string $locale, WarehouseAsset $warehouseAsset): RedirectResponse
    {
        foreach ($warehouseAsset->attachments as $attachment) {
            @unlink(storage_path('app/public/' . $attachment->path));
            $attachment->delete();
        }

        $warehouseAsset->delete();

        return redirect()->route('customer.warehouse-assets.index', ['locale' => $locale])
            ->with('success', __('warehouse.messages.success.asset_deleted'));
    }

    private function storeAttachments(WarehouseAssetStoreRequest|WarehouseAssetUpdateRequest $request, WarehouseAsset $asset, string $tenantId): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $extension = $file->getClientOriginalExtension() ?: 'bin';
            $filename = Str::uuid() . '.' . $extension;
            $directory = storage_path('app/public/warehouse/assets');
            File::ensureDirectoryExists($directory);
            $file->move($directory, $filename);

            WarehouseAssetAttachment::query()->create([
                'tenant_id' => $tenantId,
                'warehouse_asset_id' => $asset->id,
                'name' => $file->getClientOriginalName(),
                'path' => 'warehouse/assets/' . $filename,
                'uploaded_at' => now(),
            ]);
        }
    }
}
