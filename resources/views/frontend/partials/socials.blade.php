@if ($socials->isNotEmpty())
    <ul class="{{ $class ?? 'socials' }}" aria-label="Sosial media">
        @foreach ($socials as $s)
            <li>
                <a href="{{ $s->url }}" @unless (str_starts_with($s->url, 'mailto:') || str_starts_with($s->url, 'tel:')) target="_blank" rel="noopener me" @endunless
                    aria-label="{{ $s->display_label }}" title="{{ $s->display_label }}">
                    <i class="{{ $s->icon }}" aria-hidden="true"></i>
                    @if (! empty($withLabel))<span>{{ $s->display_label }}</span>@endif
                </a>
            </li>
        @endforeach
    </ul>
@endif
