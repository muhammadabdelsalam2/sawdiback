<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livestock\VaccineStoreRequest;
use App\Http\Requests\Livestock\VaccineUpdateRequest;
use App\Models\Vaccine;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class VaccineController extends Controller
{
    public function index(string $locale): View
    {
        $rows = Vaccine::query()
            ->orderBy('name_translations->' . $this->localeKey())
            ->orderBy('name')
            ->paginate(15);
        return view('dashboard.livestock.master.vaccines.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.livestock.master.vaccines.create');
    }

    public function store(VaccineStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['name_translations'] = [$this->localeKey() => $data['name']];
        
        Vaccine::query()->create($data);

        return redirect()
            ->route('customer.livestock.vaccines.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.vaccine_created'));
    }

    public function edit(string $locale, Vaccine $vaccine): View
    {
        return view('dashboard.livestock.master.vaccines.edit', compact('vaccine'));
    }

    public function update(VaccineUpdateRequest $request, string $locale, Vaccine $vaccine): RedirectResponse
    {
        $data = $request->validated();
        $translations = $vaccine->name_translations ?? [];
        $translations[$this->localeKey()] = $data['name'];
        $data['name_translations'] = $translations;

        $vaccine->update($data);

        return redirect()
            ->route('customer.livestock.vaccines.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.vaccine_updated'));
    }

    public function destroy(string $locale, Vaccine $vaccine): RedirectResponse
    {
        try {
            $vaccine->delete();
            return redirect()->back()->with('success', __('livestock.messages.success.vaccine_deleted'));
        } catch (Throwable $e) {
            return redirect()->back()->with('error', __('livestock.messages.error.vaccine_in_use'));
        }
    }

    private function localeKey(): string
    {
        return substr(app()->getLocale(), 0, 2);
    }
}
