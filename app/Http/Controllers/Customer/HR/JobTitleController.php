<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\HR\JobTitleStoreRequest;
use App\Http\Requests\Customer\HR\JobTitleUpdateRequest;
use App\Models\JobTitle;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use App\Services\Customer\HR\HrContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobTitleController extends Controller
{
    public function __construct(
        private readonly JobTitleRepositoryInterface $repo,
        private readonly HrContextService $ctx
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $jobTitles = $this->repo->paginate($tenantId, 15);

        return view('dashboard.customer.hr.job_titles.index', compact('jobTitles'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.customer.hr.job_titles.create');
    }

    public function store(JobTitleStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());

        $this->repo->create([
            'tenant_id' => $tenantId,
            ...$request->validated(),
        ]);

        return redirect()->route('customer.hr.job-titles.index', ['locale' => $locale])
            ->with('success', 'Job title created.');
    }

    public function edit(string $locale, JobTitle $job_title): View
    {
        $this->authorizeTenant($job_title);

        return view('dashboard.customer.hr.job_titles.edit', ['jobTitle' => $job_title]);
    }

    public function update(JobTitleUpdateRequest $request, string $locale, JobTitle $job_title): RedirectResponse
    {
        $this->authorizeTenant($job_title);

        $this->repo->update($job_title, $request->validated());

        return redirect()->route('customer.hr.job-titles.index', ['locale' => $locale])
            ->with('success', 'Job title updated.');
    }

    public function destroy(string $locale, JobTitle $job_title): RedirectResponse
    {
        $this->authorizeTenant($job_title);

        $this->repo->delete($job_title);

        return redirect()->route('customer.hr.job-titles.index', ['locale' => $locale])
            ->with('success', 'Job title deleted.');
    }

    private function authorizeTenant(JobTitle $jobTitle): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ($jobTitle->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
