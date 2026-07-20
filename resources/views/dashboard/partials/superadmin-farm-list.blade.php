<article class="customer-chart-card">
    <div class="customer-chart-head">
        <div>
            <h3>{{ $title }}</h3>
            <p>{{ __('superadmin.farm_dashboard.section_database_note') }}</p>
        </div>
        <i class="bi bi-list-check"></i>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ __("superadmin.farm_dashboard.columns.{$column}") }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($columns as $column)
                            @php($value = data_get($row, $column))
                            <td>
                                @if (is_bool($value))
                                    {{ $value ? __('superadmin.status.active') : __('superadmin.status.inactive') }}
                                @elseif (is_numeric($value) && ! str_contains((string) $value, '-'))
                                    {{ is_float($value + 0) ? number_format((float) $value, 2) : number_format((int) $value) }}
                                @else
                                    {{ $value ?: '-' }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center text-muted py-4">{{ $empty }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</article>
