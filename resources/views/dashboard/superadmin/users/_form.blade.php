@php
    $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
@endphp

<div class="chart-card mb-4">
    <div class="row g-4">
        <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $userModel->name ?? '') }}"
                class="form-control @error('name') is-invalid @enderror"
                required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $userModel->email ?? '') }}"
                class="form-control @error('email') is-invalid @enderror"
                required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="password" class="form-label fw-semibold">
                Password
                @if (!isset($userModel))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                @if (!isset($userModel)) required @endif>
            @if (isset($userModel))
                <small class="text-muted">Leave blank to keep current password.</small>
            @endif
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                @if (!isset($userModel)) required @endif>
        </div>
    </div>
</div>

<div class="chart-card mb-4">
    <h3 class="chart-title mb-3">Roles</h3>
    <div class="row g-2">
        @foreach ($roles as $role)
            @php
                $checkedRoles = old('roles', isset($userModel) ? $userModel->roles->pluck('name')->all() : []);
            @endphp
            <div class="col-md-4 col-sm-6">
                <label class="custom-checkbox-label">
                    <input
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->name }}"
                        class="custom-checkbox"
                        {{ in_array($role->name, $checkedRoles, true) ? 'checked' : '' }}>
                    <span class="checkbox-text">{{ $role->name }}</span>
                </label>
            </div>
        @endforeach
    </div>
    @error('roles')
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror
    @error('roles.*')
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror
</div>

<div class="chart-card mb-4">
    <h3 class="chart-title mb-3">Direct Permissions</h3>
    <p class="chart-desc mb-3">Optional permissions assigned directly to this user.</p>
    <div class="row g-2">
        @foreach ($permissions as $permission)
            @php
                $checkedPermissions = old('permissions', isset($userModel) ? $userModel->permissions->pluck('name')->all() : []);
            @endphp
            <div class="col-md-4 col-sm-6">
                <label class="custom-checkbox-label">
                    <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->name }}"
                        class="custom-checkbox"
                        {{ in_array($permission->name, $checkedPermissions, true) ? 'checked' : '' }}>
                    <span class="checkbox-text">{{ $permission->name }}</span>
                </label>
            </div>
        @endforeach
    </div>
    @error('permissions.*')
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary-green">{{ $submitLabel }}</button>
    <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">Cancel</a>
</div>
