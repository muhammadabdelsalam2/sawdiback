<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateThemeRequest;
use App\Models\TenantSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThemeController extends Controller
{
    private function tenantId(): int
    {
        return 1; // TODO: replace with tenant resolver later
    }

    public function edit(): View
    {
        $activeLocale = session('locale_full', 'en-SA');

        $settings = TenantSetting::query()->firstOrCreate(
            ['tenant_id' => $this->tenantId()],
            [
                'rtl_enabled' => false,
                'app_name' => null,
                'primary_color' => null,
            ]
        );

        return view('settings.theme.edit', compact('settings', 'activeLocale'));
    }

    public function update(UpdateThemeRequest $request): RedirectResponse
    {
        try {
            $settings = TenantSetting::query()->firstOrCreate(
                ['tenant_id' => $this->tenantId()],
                [
                    'rtl_enabled' => false,
                    'app_name' => null,
                    'primary_color' => null,
                ]
            );

            $settings->update([
                'rtl_enabled' => (bool) $request->rtl_enabled,
                'app_name' => $request->app_name,
                'primary_color' => $request->primary_color,
            ]);

            return redirect()
                ->route('superadmin.setting.theme.edit', ['locale' => $request->route('locale')])
                ->with('success', 'Theme settings updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update theme settings.');
        }
    }
}
