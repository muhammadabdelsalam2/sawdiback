<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livestock\FeedTypeStoreRequest;
use App\Http\Requests\Livestock\FeedTypeUpdateRequest;
use App\Models\FeedType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class FeedTypeController extends Controller
{
    public function index(string $locale): View
    {
        $rows = FeedType::query()->orderBy('name')->paginate(15);
        return view('dashboard.livestock.master.feed_types.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.livestock.master.feed_types.create');
    }

    public function store(FeedTypeStoreRequest $request, string $locale): RedirectResponse
    {
        FeedType::query()->create($request->validated());

        return redirect()
            ->route('customer.livestock.feed-types.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Feed type created successfully.');
    }

    public function edit(string $locale, FeedType $feedType): View
    {
        return view('dashboard.livestock.master.feed_types.edit', compact('feedType'));
    }

    public function update(FeedTypeUpdateRequest $request, string $locale, FeedType $feedType): RedirectResponse
    {
        $feedType->update($request->validated());

        return redirect()
            ->route('customer.livestock.feed-types.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Feed type updated successfully.');
    }

    public function destroy(string $locale, FeedType $feedType): RedirectResponse
    {
        try {
            $feedType->delete();
            return redirect()->back()->with('success', 'Feed type deleted successfully.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Feed type cannot be deleted because it is in use.');
        }
    }
}
