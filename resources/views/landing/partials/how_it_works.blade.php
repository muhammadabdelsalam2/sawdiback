<section id="how-it-works" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">{{ __('app.how_it_works_title') }}</h2>
            <p class="text-muted">{{ __('app.how_it_works_subtitle') }}</p>
        </div>

        <div class="row g-4">
            @foreach ([
                ['icon' => 'bi-search', 'title' => __('app.how_step_1_title'), 'text' => __('app.how_step_1_text')],
                ['icon' => 'bi-chat-dots', 'title' => __('app.how_step_2_title'), 'text' => __('app.how_step_2_text')],
                ['icon' => 'bi-clipboard-check', 'title' => __('app.how_step_3_title'), 'text' => __('app.how_step_3_text')],
                ['icon' => 'bi-truck', 'title' => __('app.how_step_4_title'), 'text' => __('app.how_step_4_text')],
            ] as $index => $step)
                <div class="col-md-6 col-lg-3">
                    <div class="bg-white rounded-4 shadow-sm p-4 h-100 text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:64px;height:64px;background:#eef6ea;color:#2D5A27;">
                            <i class="bi {{ $step['icon'] }} fs-3"></i>
                        </div>
                        <div class="small text-muted mb-2">{{ __('app.step_label', ['number' => $index + 1]) }}</div>
                        <h5 class="fw-bold">{{ $step['title'] }}</h5>
                        <p class="text-muted mb-0">{{ $step['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
