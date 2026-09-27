@extends('backend.layout.app')

@section('title', 'Export PDF')

@include('backend.portfolio._assets')

@section('content')
    @include('backend.portfolio._header', [
        'heading' => 'Export PDF',
        'subheading' => 'Empat versi dokumen, semuanya memakai data yang kamu isi di website.',
    ])

    <div class="row g-6">
        {{-- ---------- Daftar versi ---------- --}}
        <div class="col-xl-5">
            @include('backend.portfolio._guide', [
                'text' => 'Semua versi memakai data yang sama dari website — cukup pilih bentuk dokumennya.',
                'steps' => [
                    'Klik salah satu versi di bawah; pratinjaunya langsung tampil di samping.',
                    'Klik "Buka & Cetak", lalu pilih Save as PDF / Simpan sebagai PDF.',
                    'Di dialog cetak: margin None, dan centang Background graphics agar warnanya ikut.',
                ],
            ])

            <div class="d-grid gap-4 mt-6">
                @foreach ($variants as $key => $v)
                    <div class="card card-flush shadow-sm pfx-card {{ $key === $active ? 'pfx-card--on' : '' }}"
                         data-pfx-card data-variant="{{ $key }}"
                         data-url="{{ route('pf.export.sheet', $key) }}">
                        <div class="card-body p-6">
                            <div class="d-flex align-items-start justify-content-between gap-4">
                                <div>
                                    <h3 class="fs-4 fw-bold mb-1">{{ $v['label'] }}</h3>
                                    <span class="badge badge-light-danger">{{ $v['tagline'] }}</span>
                                </div>
                                <a class="btn btn-sm btn-light-primary flex-shrink-0"
                                   href="{{ route('pf.export.sheet', $key) }}" target="_blank" rel="noopener">
                                    <i class="ki-outline ki-exit-right-corner fs-5"></i> Buka &amp; Cetak
                                </a>
                            </div>
                            <p class="text-muted mt-4 mb-0 fs-7">{{ $v['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ---------- Pratinjau ---------- --}}
        <div class="col-xl-7">
            <div class="pf-preview card card-flush shadow-sm">
                <div class="pf-preview__bar">
                    <span class="pf-preview__dots" aria-hidden="true"><i></i><i></i><i></i></span>
                    <span class="pf-preview__title">
                        <i class="ki-outline ki-eye fs-6"></i> Pratinjau —
                        <b data-pfx-label>{{ $variants[$active]['label'] }}</b>
                    </span>
                    <div class="pf-preview__tools">
                        <button type="button" class="btn btn-icon btn-sm btn-light" data-pfx-reload
                                title="Muat ulang" aria-label="Muat ulang pratinjau">
                            <i class="ki-outline ki-arrows-circle fs-5"></i>
                        </button>
                        <a data-pfx-open href="{{ route('pf.export.sheet', $active) }}" target="_blank" rel="noopener"
                           class="btn btn-icon btn-sm btn-light" title="Buka di tab baru" aria-label="Buka di tab baru">
                            <i class="ki-outline ki-exit-right-corner fs-5"></i>
                        </a>
                    </div>
                </div>
                <div class="pf-preview__stage pfx-stage">
                    <iframe data-pfx-frame src="{{ route('pf.export.sheet', $active) }}"
                            title="Pratinjau dokumen export" loading="lazy"></iframe>
                </div>
                <div class="pf-preview__hint">
                    Yang tampil di sini persis yang akan tercetak. Tombol <b>Download PDF</b> ada di dalam pratinjau.
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .pfx-card { cursor: pointer; transition: border-color .15s, transform .15s; border: 1px solid var(--bs-border-color); }
            .pfx-card:hover { transform: translateY(-2px); }
            .pfx-card--on { border-color: var(--bs-danger); box-shadow: 0 0 0 3px rgb(var(--bs-danger-rgb) / .12) !important; }
            .pfx-stage { height: min(78vh, 900px); }
            .pfx-stage iframe { width: 100%; height: 100%; border: 0; background: #e5e7eb; }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function () {
                'use strict';
                var frame = document.querySelector('[data-pfx-frame]');
                var label = document.querySelector('[data-pfx-label]');
                var open = document.querySelector('[data-pfx-open]');
                if (!frame) { return; }

                document.querySelectorAll('[data-pfx-card]').forEach(function (card) {
                    card.addEventListener('click', function (e) {
                        // Biarkan tombol "Buka & Cetak" bekerja seperti biasa.
                        if (e.target.closest('a')) { return; }
                        document.querySelectorAll('[data-pfx-card]').forEach(function (c) { c.classList.remove('pfx-card--on'); });
                        card.classList.add('pfx-card--on');
                        frame.src = card.dataset.url;
                        if (open) { open.href = card.dataset.url; }
                        if (label) { label.textContent = card.querySelector('h3').textContent.trim(); }
                    });
                });

                var reload = document.querySelector('[data-pfx-reload]');
                if (reload) {
                    reload.addEventListener('click', function () { frame.src = frame.src; });
                }
            })();
        </script>
    @endpush
@endsection
