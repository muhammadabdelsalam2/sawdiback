<div class="container ">
    <div class="shadow-sm rounded-4 bg-white p-2">

        <x-form method="POST" action="{{ route('customer.farmers.store', ['locale' => request()->route('locale')]) }}"
            method="POST" :axios="true">

            @csrf

            <div class="row g-3">

                {{-- Name --}}
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            id="name" placeholder="Farmer Name" value="{{ old('name') }}">

                        <label for="name">{{ __('farmer.lables.name') }}</label>

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

                        <label for="phone">{{ __('farmer.lables.phone') }}</label>

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

                        <label for="email">{{ __('farmer.lables.email') }}</label>

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

                        <label for="farm_name">{{ __('farmer.lables.farm_name') }}</label>

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
                            class="form-control @error('opening_balance') is-invalid @enderror" id="opening_balance"
                            placeholder="Opening Balance" value="{{ old('opening_balance', 0) }}">

                        <label for="opening_balance">{{ __('farmer.lables.opening_balance') }}</label>

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
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Address"
                            id="address" style="height: 120px">{{ old('address') }}</textarea>

                        <label for="address">{{ __('farmer.fields.address') }}</label>

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
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}>

                        <label class="form-check-label" for="is_active">
                            {{ __('farmer.lables.status') }}
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-3 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> {{ __('farmer.titles.farmer_create') }}
                    </button>
                    <a href="{{ route('customer.farmers.index', ['locale', $currentLocale]) }}" class="btn btn-light">
                        <i class="fas fa-x-circle me-1"></i> {{ __('farmer.cancel') }}
                    </a>
                </div>
                {{-- Upload Image --}}
                <div class="col-12">
                    <label for="image" class="form-label">{{ __('farmer.lables.image') }}</label>
                    <input class="form-control @error('image') is-invalid @enderror" type="file" id="image"
                        name="image">

                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

        </x-form>
    </div>
</div>
