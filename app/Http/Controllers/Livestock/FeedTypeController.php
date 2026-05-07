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
        $rows = FeedType::query()
            ->orderBy('name_translations->' . $this->localeKey())
            ->orderBy('name')
            ->paginate(15);
        return view('dashboard.livestock.master.feed_types.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.livestock.master.feed_types.create');
    }

    public function store(FeedTypeStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['name_translations'] = [$this->localeKey() => $data['name']];
        
        FeedType::query()->create($data);

        return redirect()
            ->route('customer.livestock.feed-types.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.feed_type_created'));
    }

    public function edit(string $locale, FeedType $feedType): View
    {
        return view('dashboard.livestock.master.feed_types.edit', compact('feedType'));
    }

    public function update(FeedTypeUpdateRequest $request, string $locale, FeedType $feedType): RedirectResponse
    {
        $data = $request->validated();
        $translations = $feedType->name_translations ?? [];
        $translations[$this->localeKey()] = $data['name'];
        $data['name_translations'] = $translations;

        $feedType->update($data);

        return redirect()
            ->route('customer.livestock.feed-types.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.feed_type_updated'));
    }

    public function destroy(string $locale, FeedType $feedType): RedirectResponse
    {
        try {
            $feedType->delete();
            return redirect()->back()->with('success', __('livestock.messages.success.feed_type_deleted'));
        } catch (Throwable $e) {
            return redirect()->back()->with('error', __('livestock.messages.error.feed_type_in_use'));
        }
    }

    private function localeKey(): string
    {
        return substr(app()->getLocale(), 0, 2);
    }
}
