{{-- "Panduan" box at the top of a Portfolio form. Params: text (string), steps (optional list) --}}
@if (! empty($text) || ! empty($steps))
    <div class="pf-guide mb-7">
        <div class="pf-guide__icon"><i class="ki-outline ki-information-2 fs-2"></i></div>
        <div class="min-w-0">
            <div class="pf-guide__title">Panduan</div>
            @if (! empty($text))<p class="pf-guide__text">{{ $text }}</p>@endif
            @if (! empty($steps))
                <ol class="pf-guide__steps">
                    @foreach ($steps as $step)<li>{{ $step }}</li>@endforeach
                </ol>
            @endif
        </div>
    </div>
@endif
