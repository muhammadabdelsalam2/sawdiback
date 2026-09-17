@csrf
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
    $eggSource = old('egg_source', $batch->egg_source ?? 'farm');
    $breedsList = \App\Models\Poultry\PoultryHatcheryBatch::BREEDS;
@endphp

<div class="row g-3">
    {{-- 1. البيانات الأساسية للدفعة --}}
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.batch_number') }} <span class="text-danger">*</span></label>
        <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number', $batch->batch_number ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.machine') }} <span class="text-danger">*</span></label>
        <select name="hatchery_machine_id" class="form-select" required>
            <option value="">-- {{ $isArabic ? 'اختر الماكينة / الفقاسة' : 'Select Machine' }} --</option>
            @foreach($machines as $machine)
                <option value="{{ $machine->id }}" @selected((int) old('hatchery_machine_id', $batch->hatchery_machine_id ?? 0) === $machine->id)>
                    {{ $machine->machine_number }} ({{ $machine->name ?? '' }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.loaded_at') }} <span class="text-danger">*</span></label>
        <input type="date" name="loaded_at" class="form-control" value="{{ old('loaded_at', isset($batch) && $batch->loaded_at ? $batch->loaded_at->format('Y-m-d') : date('Y-m-d')) }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.expected_hatch_at') }} <span class="text-danger">*</span></label>
        <input type="date" name="expected_hatch_at" class="form-control" value="{{ old('expected_hatch_at', isset($batch) && $batch->expected_hatch_at ? $batch->expected_hatch_at->format('Y-m-d') : '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.actual_hatch_at') }}</label>
        <input type="date" name="actual_hatch_at" class="form-control" value="{{ old('actual_hatch_at', isset($batch) && $batch->actual_hatch_at ? $batch->actual_hatch_at->format('Y-m-d') : '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ $isArabic ? 'مبلغ الشراء / التكلفة' : 'Purchase Amount' }}</label>
        <input type="number" step="0.01" min="0" name="purchase_amount" class="form-control" value="{{ old('purchase_amount', $batch->purchase_amount ?? '0.00') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.eggs_loaded') }} <span class="text-danger">*</span></label>
        <input type="number" min="1" name="eggs_loaded" id="totalEggsLoadedInput" class="form-control font-weight-bold bg-light" value="{{ old('eggs_loaded', $batch->eggs_loaded ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.chicks_produced') }}</label>
        <input type="number" min="0" name="chicks_produced" class="form-control" value="{{ old('chicks_produced', $batch->chicks_produced ?? 0) }}">
    </div>

    {{-- 2. مصدر البيض وبيانات المورد أو الحظيرة --}}
    <div class="col-12 mt-4">
        <h6 class="font-weight-bold text-primary border-bottom pb-2">
            <i class="fas fa-egg me-1"></i> {{ $isArabic ? 'مصدر البيض وبيانات التوريد' : 'Egg Source & Supplier Details' }}
        </h6>
    </div>

    <div class="col-md-4">
        <label class="form-label font-weight-bold">{{ $isArabic ? 'مصدر البيض' : 'Egg Source' }} <span class="text-danger">*</span></label>
        <select name="egg_source" id="eggSourceSelect" class="form-select" required>
            <option value="farm" @selected($eggSource === 'farm')>{{ $isArabic ? 'من المزرعة' : 'Internal Farm' }}</option>
            <option value="purchased" @selected($eggSource === 'purchased')>{{ $isArabic ? 'شراء خارجي' : 'Purchased Externally' }}</option>
        </select>
    </div>

    {{-- إذا كان من المزرعة: اختيار الحظيرة --}}
    <div class="col-md-8" id="farmPenContainer" style="{{ $eggSource === 'farm' ? '' : 'display:none;' }}">
        <label class="form-label font-weight-bold">{{ $isArabic ? 'رقم الحظيرة المصدر' : 'Source Pen' }}</label>
        <select name="pen_id" class="form-select">
            <option value="">-- {{ __('farms.empty.no_pens') }} --</option>
            @foreach($pens ?? [] as $pen)
                <option value="{{ $pen->id }}" @selected(old('pen_id', $batch->pen_id ?? '') == $pen->id)>
                    {{ $pen->farm?->name }} - {{ $pen->pen_number }} ({{ $pen->type }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- إذا كان شراء: بيانات البائع والموقع --}}
    <div class="col-md-4 purchase-field" style="{{ $eggSource === 'purchased' ? '' : 'display:none;' }}">
        <label class="form-label font-weight-bold">{{ $isArabic ? 'اسم البائع' : 'Seller Name' }}</label>
        <input type="text" name="seller_name" class="form-control" value="{{ old('seller_name', $batch->seller_name ?? '') }}">
    </div>

    <div class="col-md-4 purchase-field" style="{{ $eggSource === 'purchased' ? '' : 'display:none;' }}">
        <label class="form-label font-weight-bold">{{ $isArabic ? 'رقم هاتف البائع' : 'Seller Phone' }}</label>
        <input type="text" name="seller_phone" class="form-control" value="{{ old('seller_phone', $batch->seller_phone ?? '') }}">
    </div>

    <div class="col-md-4 purchase-field" style="{{ $eggSource === 'purchased' ? '' : 'display:none;' }}">
        <label class="form-label font-weight-bold">{{ $isArabic ? 'موقع البائع / المصدر' : 'Seller Location' }}</label>
        <input type="text" name="seller_location" class="form-control" value="{{ old('seller_location', $batch->seller_location ?? '') }}">
    </div>

    {{-- 3. توزيع السلالات الـ 10 وعدد البيض لكل سلالة --}}
    <div class="col-12 mt-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
            <h6 class="font-weight-bold text-primary mb-0">
                <i class="fas fa-list-ol me-1"></i> {{ $isArabic ? 'توزيع السلالات الـ 10 وعدد البيض لكل سلالة' : '10 Breeds Distribution & Egg Counts' }}
            </h6>
            <small class="text-muted">{{ $isArabic ? 'أدخل عدد البيض أمام كل سلالة مشاركة في الدفعة' : 'Enter count for participating breeds' }}</small>
        </div>

        <div class="table-responsive border rounded">
            <table class="table table-sm table-hover mb-0 text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th class="text-start">{{ $isArabic ? 'السلالة' : 'Breed' }}</th>
                        <th style="width: 250px;">{{ $isArabic ? 'عدد البيض' : 'Egg Count' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $index = 1; @endphp
                    @foreach($breedsList as $key => $title)
                        <tr>
                            <td>{{ $index++ }}</td>
                            <td class="text-start font-weight-bold">
                                {{ $title }}
                                <input type="hidden" name="batch_breeds[{{ $key }}][key]" value="{{ $key }}">
                            </td>
                            <td>
                                <input type="number" min="0" name="batch_breeds[{{ $key }}][count]" 
                                       class="form-control form-control-sm text-center breed-count-input" 
                                    value="{{ old("batch_breeds.$key.count", isset($batch) && $batch->relationLoaded('breeds') ? ($batch->breeds->firstWhere('slug', $key)?->pivot?->egg_count ?? 0) : 0) }}"
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12 mt-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $batch->notes ?? '') }}" placeholder="{{ $isArabic ? 'أي ملاحظات إضافية...' : 'Any notes...' }}">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sourceSelect = document.getElementById('eggSourceSelect');
    const farmContainer = document.getElementById('farmPenContainer');
    const purchaseFields = document.querySelectorAll('.purchase-field');
    const breedInputs = document.querySelectorAll('.breed-count-input');
    const totalEggsInput = document.getElementById('totalEggsLoadedInput');

    // التبديل بين حقول الشراء وحقول المزرعة
    function toggleSourceFields() {
        if (sourceSelect.value === 'purchased') {
            if (farmContainer) farmContainer.style.display = 'none';
            purchaseFields.forEach(el => el.style.display = 'block');
        } else {
            if (farmContainer) farmContainer.style.display = 'block';
            purchaseFields.forEach(el => el.style.display = 'none');
        }
    }
    sourceSelect.addEventListener('change', toggleSourceFields);

    // جمع عدد البيض من السلالات وتحديث إجمالي البيض تلقائياً
    function calculateTotalEggs() {
        let total = 0;
        let hasValue = false;
        breedInputs.forEach(input => {
            const val = parseInt(input.value) || 0;
            if (val > 0) {
                total += val;
                hasValue = true;
            }
        });
        if (hasValue && totalEggsInput) {
            totalEggsInput.value = total;
        }
    }

    breedInputs.forEach(input => {
        input.addEventListener('input', calculateTotalEggs);
    });
});
</script>