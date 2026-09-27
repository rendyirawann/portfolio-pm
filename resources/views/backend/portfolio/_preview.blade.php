{{--
    Live website preview shown beside a Portfolio form.
    Params: target (CSS selector of the section, e.g. "#services"),
            prefix (key prefix for list items, e.g. "services.5." — empty for page content),
            url (optional page to preview, default: home)
--}}
@php
    $previewUrl = ($url ?? route('home')) . (str_contains($url ?? '', '?') ? '&' : '?') . 'pf_preview=1' . ($target ?? '');
@endphp
<div class="pf-preview card card-flush shadow-sm" data-pf-preview data-target="{{ $target ?? '' }}" data-prefix="{{ $prefix ?? '' }}">
    <div class="pf-preview__bar">
        <span class="pf-preview__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="pf-preview__title"><i class="ki-outline ki-eye fs-6"></i> Pratinjau langsung</span>
        <div class="pf-preview__tools">
            <button type="button" class="btn btn-icon btn-sm btn-light is-active" data-device="desktop" title="Desktop" aria-label="Pratinjau desktop"><i class="ki-outline ki-screen fs-5"></i></button>
            <button type="button" class="btn btn-icon btn-sm btn-light" data-device="mobile" title="Mobile" aria-label="Pratinjau mobile"><i class="ki-outline ki-phone fs-5"></i></button>
            <button type="button" class="btn btn-icon btn-sm btn-light" data-preview-reload title="Muat ulang" aria-label="Muat ulang pratinjau"><i class="ki-outline ki-arrows-circle fs-5"></i></button>
            <a href="{{ str_replace('pf_preview=1', '', $previewUrl) }}" target="_blank" rel="noopener" class="btn btn-icon btn-sm btn-light" title="Buka di tab baru" aria-label="Buka website"><i class="ki-outline ki-exit-right-corner fs-5"></i></a>
        </div>
    </div>
    <div class="pf-preview__stage" data-stage>
        <iframe src="{{ $previewUrl }}" title="Pratinjau website" loading="lazy" data-frame></iframe>
    </div>
    <div class="pf-preview__hint">Perubahan teks & gambar tampil langsung di sini. Klik <b>Simpan</b> agar tersimpan di website.</div>
</div>

@once
    @push('scripts')
        <script src="{{ asset('assets/js/custom/portfolio/preview.js') }}?v={{ filemtime(public_path('assets/js/custom/portfolio/preview.js')) }}" defer></script>
    @endpush
@endonce
