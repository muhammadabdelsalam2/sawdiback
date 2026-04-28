@props([
    'title' => '',
    'subtitle' => '',
    'class' => ''
])

<section class="bg-primary py-5 {{ $class }}" style="margin-top: 80px;">
    <div class="container py-4">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="text-white mb-3">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-white-50 lead">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

