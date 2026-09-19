<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeRecord;
use App\Services\Customer\HR\HrContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeRecordController extends Controller
{
    public function __construct(
        private readonly HrContextService $ctx
    ) {}

    public function store(Request $request, string $locale, Employee $employee): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        if ((string) $employee->tenant_id !== (string) $tenantId) {
            abort(403);
        }

        $validated = $request->validate([
            'record_type' => [
                'required',
                'string',
                Rule::in([
                    'achievement',   // إنجازات وإبداعات الموظف
                    'qualification', // شهادة علمية
                    'experience',    // خبرة عملية سابقة
                    'training',      // تطوير مستمر ودورات
                    'violation',     // تهرب من العمل ومخالفات
                    'note',          // ملاحظات عامة
                ]),
            ],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date'  => ['required', 'date'],
        ]);

        EmployeeRecord::query()->create([
            'tenant_id'   => $tenantId,
            'employee_id' => $employee->id,
            'record_type' => $validated['record_type'],
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'event_date'  => $validated['event_date'],
            'created_by'  => auth()->id(),
        ]);

        return redirect()->back()->with('success', __('hr.messages.success.record_added') ?? 'تمت إضافة السجل إلى ملف الموظف بنجاح.');
    }

    public function destroy(string $locale, Employee $employee, EmployeeRecord $record): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        if ((string) $employee->tenant_id !== (string) $tenantId || (string) $record->tenant_id !== (string) $tenantId) {
            abort(403);
        }

        $record->delete();

        return redirect()->back()->with('success', __('hr.messages.success.record_deleted') ?? 'تم حذف السجل بنجاح.');
    }
}
