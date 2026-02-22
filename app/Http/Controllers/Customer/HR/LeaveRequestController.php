<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\HR\LeaveActionRequest;
use App\Http\Requests\Customer\HR\LeaveStoreRequest;
use App\Models\LeaveRequest;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\LeaveRepositoryInterface;
use App\Services\Customer\HR\HrContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function __construct(
        private readonly LeaveRepositoryInterface $repo,
        private readonly EmployeeRepositoryInterface $employeesRepo,
        private readonly HrContextService $ctx
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $leaves = $this->repo->paginate($tenantId, 20);

        return view('dashboard.customer.hr.leaves.index', compact('leaves'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $employees = $this->employeesRepo->paginate($tenantId, 200);

        return view('dashboard.customer.hr.leaves.create', compact('employees'));
    }

    public function store(LeaveStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());

        $this->repo->create([
            'tenant_id' => $tenantId,
            ...$request->validated(),
        ]);

        return redirect()
            ->route('customer.hr.leaves.index', ['locale' => $locale])
            ->with('success', 'Leave request submitted.');
    }

    public function approve(LeaveActionRequest $request, string $locale, LeaveRequest $leave): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        if ($leave->tenant_id !== $tenantId) abort(403);

        $this->repo->approve($leave, (int) auth()->id());

        return back()->with('success', 'Leave approved.');
    }

    public function reject(LeaveActionRequest $request, string $locale, LeaveRequest $leave): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        if ($leave->tenant_id !== $tenantId) abort(403);

        $this->repo->reject($leave, (int) auth()->id());

        return back()->with('success', 'Leave rejected.');
    }
}
