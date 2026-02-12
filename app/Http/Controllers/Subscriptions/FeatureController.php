<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriptions\FeatureStoreRequest;
use App\Http\Requests\Subscriptions\FeatureUpdateRequest;
use App\Models\Feature;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureController extends Controller
{
    public function index(string $locale): View
    {
        $features = Feature::query()->orderBy('name')->paginate(15);

        return view('dashboard.subscriptions.features.index', compact('features'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.subscriptions.features.create');
    }

    public function store(FeatureStoreRequest $request, string $locale): RedirectResponse
    {
        Feature::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('superadmin.features.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('subscriptions.messages.feature_created'));
    }

    public function edit(string $locale, Feature $feature): View
    {
        return view('dashboard.subscriptions.features.edit', compact('feature'));
    }

    public function update(FeatureUpdateRequest $request, string $locale, Feature $feature): RedirectResponse
    {
        $feature->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('superadmin.features.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('subscriptions.messages.feature_updated'));
    }

    public function destroy(string $locale, Feature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()
            ->route('superadmin.features.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('subscriptions.messages.feature_deleted'));
    }
}
