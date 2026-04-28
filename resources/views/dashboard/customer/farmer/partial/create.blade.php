<div class="container mt-4">
    <div class="shadow-sm rounded-4 bg-white p-4">

        <x-form method="POST" action="{{ route('customer.farmers.store', ['locale' => request()->route('locale')]) }}">

            @csrf

            <div class="row g-3">

                {{-- Name --}}
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            id="name" placeholder="Farmer Name" value="{{ old('name') }}">

                        <label for="name">Farmer Name *</label>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            id="phone" placeholder="Phone" value="{{ old('phone') }}">

                        <label for="phone">Phone</label>

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" placeholder="Email" value="{{ old('email') }}">

                        <label for="email">Email</label>

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Farm Name --}}
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="farm_name"
                            class="form-control @error('farm_name') is-invalid @enderror" id="farm_name"
                            placeholder="Farm Name" value="{{ old('farm_name') }}">

                        <label for="farm_name">Farm Name</label>

                        @error('farm_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Opening Balance (NEW - ERP FIELD) --}}
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="number" step="0.01" name="opening_balance"
                            class="form-control @error('opening_balance') is-invalid @enderror"
                            id="opening_balance"
                            placeholder="Opening Balance"
                            value="{{ old('opening_balance', 0) }}">

                        <label for="opening_balance">Opening Balance</label>

                        @error('opening_balance')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Address --}}
                <div class="col-12">
                    <div class="form-floating">
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                            placeholder="Address" id="address" style="height: 120px">{{ old('address') }}</textarea>

                        <label for="address">Address</label>

                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Active (FIXED ERP SAFE) --}}
                <div class="col-12">
                    <input type="hidden" name="is_active" value="0">

                    <div class="form-check form-switch">
                        <input class="form-check-input"
                               type="checkbox"
                               name="is_active"
                               id="is_active"
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>

                        <label class="form-check-label" for="is_active">
                            Active Farmer
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
              <div class="mt-3 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle me-1"></i> {{ __('farmer.create') }}
                </button>
                <a href="{{ route('customer.farmers.index',['locale', $currentLocale]) }}" class="btn btn-light">
                    <i class="fas fa-x-circle me-1"></i> {{ __('farmer.cancel') }}
                </a>
            </div>

            </div>

        </x-form>
    </div>
</div>

