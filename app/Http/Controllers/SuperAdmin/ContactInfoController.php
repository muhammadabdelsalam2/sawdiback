<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\ContactInfoRequest;
use App\Models\ContactInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactInfoController extends Controller
{
    public function edit(): View
    {
        return view('superadmin.contact_info.edit', [
            'contactInfo' => ContactInfo::singleton(),
        ]);
    }

    public function update(ContactInfoRequest $request, string $locale): RedirectResponse
    {
        $validated = $request->validated();

        ContactInfo::singleton()->update([
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => [
                'ar' => $validated['address_ar'] ?? null,
                'en' => $validated['address_en'] ?? null,
            ],
            'working_hours' => [
                'ar' => $validated['working_hours_ar'] ?? null,
                'en' => $validated['working_hours_en'] ?? null,
            ],
            'description' => [
                'ar' => $validated['description_ar'] ?? null,
                'en' => $validated['description_en'] ?? null,
            ],
            'whatsapp_url' => $validated['whatsapp_url'] ?? null,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'x_url' => $validated['x_url'] ?? null,
        ]);

        return redirect()
            ->route('superadmin.contact-info.edit', ['locale' => $locale])
            ->with('success', __('dashboard.contact_info.updated'));
    }
}
