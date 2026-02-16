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
        $rows = Vaccine::query()->orderBy('name')->paginate(15);
        return view('dashboard.livestock.master.vaccines.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.livestock.master.vaccines.create');
    }

    public function store(VaccineStoreRequest $request, string $locale): RedirectResponse
    {
        Vaccine::query()->create($request->validated());

        return redirect()
            ->route('superadmin.livestock.vaccines.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Vaccine created successfully.');
    }

    public function edit(string $locale, Vaccine $vaccine): View
    {
        return view('dashboard.livestock.master.vaccines.edit', compact('vaccine'));
    }

    public function update(VaccineUpdateRequest $request, string $locale, Vaccine $vaccine): RedirectResponse
    {
        $vaccine->update($request->validated());

        return redirect()
            ->route('superadmin.livestock.vaccines.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Vaccine updated successfully.');
    }

    public function destroy(string $locale, Vaccine $vaccine): RedirectResponse
    {
        try {
            $vaccine->delete();
            return redirect()->back()->with('success', 'Vaccine deleted successfully.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Vaccine cannot be deleted because it is in use.');
        }
    }
}
