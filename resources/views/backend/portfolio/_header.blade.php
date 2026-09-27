{{-- Page heading shared by every Portfolio admin screen. --}}
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-6">
    <div class="min-w-0">
        <div class="text-muted fs-8 fw-semibold text-uppercase mb-1">
            <a href="{{ route('pf.content.index') }}" class="text-muted text-hover-primary">Portfolio</a>
            @isset($back) › <a href="{{ $back }}" class="text-muted text-hover-primary">{{ $backLabel ?? 'Kembali' }}</a> @endisset
        </div>
        <h1 class="fs-2 fw-bold text-gray-900 mb-1">{{ $heading }}</h1>
        @if (! empty($subheading))
            <p class="text-muted fs-7 mb-0">{{ $subheading }}</p>
        @endif
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-sm btn-light">
            <i class="ki-outline ki-eye fs-5 me-1"></i>Lihat Website
        </a>
        @isset($action)
            <a href="{{ $action['url'] }}" class="btn btn-sm btn-primary">
                <i class="ki-outline ki-plus fs-5 me-1"></i>{{ $action['label'] }}
            </a>
        @endisset
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-6" role="alert">
        <i class="ki-outline ki-shield-cross fs-2hx text-danger me-4"></i>
        <div>
            <h4 class="mb-2 text-danger">Periksa kembali isian Anda</h4>
            <ul class="mb-0 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
