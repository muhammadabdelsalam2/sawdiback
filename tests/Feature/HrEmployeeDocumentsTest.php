<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class HrEmployeeDocumentsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Tenant $tenant;
    private array $createdAttachmentPaths = [];

    protected function tearDown(): void
    {
        foreach ($this->createdAttachmentPaths as $path) {
            @unlink(storage_path('app/public/' . $path));
        }

        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.view', 'guard_name' => 'web']);
        $role->givePermissionTo('hr.view');

        $this->tenant = Tenant::query()->create([
            'name' => 'HR Test Farm',
            'slug' => 'hr-test-farm',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user->assignRole('SuperAdmin');
    }

    public function test_employee_can_be_created_with_required_hr_fields_and_three_attachments(): void
    {
        $response = $this->actingAs($this->user)->post('/en-SA/hr/employees', [
            'full_name' => 'Ahmed Worker',
            'worker_number' => 'WRK-1001',
            'profession' => 'Poultry Technician',
            'hire_date' => '2026-01-15',
            'salary' => '3500.50',
            'employment_status' => 'active',
            'operational_department' => 'poultry',
            'passport_expiry_date' => now()->addDays(10)->toDateString(),
            'iqama_expiry_date' => now()->addDays(20)->toDateString(),
            'is_active' => 1,
            'attachment_passport' => UploadedFile::fake()->create('passport.pdf', 120, 'application/pdf'),
            'attachment_iqama' => UploadedFile::fake()->create('iqama.pdf', 120, 'application/pdf'),
            'attachment_identity' => UploadedFile::fake()->create('identity.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect('/en-SA/hr/employees');

        $this->assertDatabaseHas('employees', [
            'tenant_id' => $this->tenant->id,
            'full_name' => 'Ahmed Worker',
            'worker_number' => 'WRK-1001',
            'profession' => 'Poultry Technician',
            'employment_status' => 'active',
            'operational_department' => 'poultry',
        ]);

        $employee = Employee::query()->where('worker_number', 'WRK-1001')->firstOrFail();
        $this->assertSame('2026-01-15', $employee->hire_date->format('Y-m-d'));
        $this->assertSame('3500.50', $employee->salary);

        foreach (['passport', 'iqama', 'identity'] as $type) {
            $this->assertDatabaseHas('employee_attachments', [
                'tenant_id' => $this->tenant->id,
                'employee_id' => $employee->id,
                'type' => $type,
            ]);

            $path = $employee->attachments()->where('type', $type)->value('path');
            $this->createdAttachmentPaths[] = $path;
            $this->assertFileExists(storage_path('app/public/' . $path));
        }

        $this->actingAs($this->user)
            ->get('/en-SA/hr/employees/' . $employee->id)
            ->assertOk()
            ->assertSee('WRK-1001')
            ->assertSee('Poultry Technician')
            ->assertSee('Poultry')
            ->assertSee('Passport')
            ->assertSee('Iqama')
            ->assertSee('Identity');
    }

    public function test_document_expiry_command_and_dashboard_alert_show_expiring_documents(): void
    {
        $employee = Employee::query()->create([
            'tenant_id' => $this->tenant->id,
            'full_name' => 'Expired Soon Worker',
            'worker_number' => 'WRK-2002',
            'profession' => 'Livestock Handler',
            'hire_date' => '2026-02-01',
            'salary' => 2800,
            'employment_status' => 'active',
            'operational_department' => 'livestock',
            'passport_expiry_date' => now()->addDays(5)->toDateString(),
            'iqama_expiry_date' => now()->addDays(7)->toDateString(),
            'is_active' => true,
        ]);

        $this->artisan('hr:check-document-expiries', ['--days' => 30])
            ->expectsOutputToContain('Expired Soon Worker')
            ->assertExitCode(0);

        $this->actingAs($this->user)
            ->get('/en-SA/hr/employees/document-alerts')
            ->assertOk()
            ->assertSee('Expired Soon Worker')
            ->assertSee('WRK-2002')
            ->assertSee('Livestock')
            ->assertSee($employee->passport_expiry_date->format('Y-m-d'))
            ->assertSee($employee->iqama_expiry_date->format('Y-m-d'));
    }
}
