<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Content\StoreContentRequest;
use App\Http\Requests\Dashboard\Content\UpdateContentRequest;
use App\Models\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $contents = Content::query()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.content.index', [
            'contents' => $contents,
        ]);
    }

    public function create(): View
    {
        return view('superadmin.content.create');
    }

    public function store(StoreContentRequest $request, string $locale): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'title' => [
                'ar' => $validated['title_ar'],
                'en' => $validated['title_en'],
            ],
            'description' => [
                'ar' => $validated['description_ar'],
                'en' => $validated['description_en'],
            ],
        ];

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('content/videos', 'public');
        }

        if ($validated['video_url']) {
            $data['video_url'] = $validated['video_url'];
        }

        Content::create($data);

        return redirect()
            ->route('superadmin.content.index', ['locale' => $locale])
            ->with('success', __('dashboard.messages.success.content_created'));
    }

    public function edit(string $locale, Content $content): View
    {
        return view('superadmin.content.edit', [
            'content' => $content,
        ]);
    }

    public function update(UpdateContentRequest $request, string $locale, Content $content): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'title' => [
                'ar' => $validated['title_ar'],
                'en' => $validated['title_en'],
            ],
            'description' => [
                'ar' => $validated['description_ar'],
                'en' => $validated['description_en'],
            ],
        ];

        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($content->video && \Storage::disk('public')->exists($content->video)) {
                \Storage::disk('public')->delete($content->video);
            }
            $data['video'] = $request->file('video')->store('content/videos', 'public');
        }

        $data['video_url'] = $validated['video_url'] ?: null;

        $content->update($data);

        return redirect()
            ->route('superadmin.content.index', ['locale' => $locale])
            ->with('success', __('dashboard.messages.success.content_updated'));
    }

    public function destroy(string $locale, Content $content): RedirectResponse
    {
        // Delete video file if exists
        if ($content->video && Storage::disk('public')->exists($content->video)) {
            Storage::disk('public')->delete($content->video);
        }

        $content->delete();

        return redirect()
            ->route('superadmin.content.index', ['locale' => $locale])
            ->with('success', __('dashboard.messages.success.content_deleted'));
    }
}
