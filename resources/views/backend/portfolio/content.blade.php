@extends('backend.layout.app')

@section('title', 'Konten Halaman')

@include('backend.portfolio._assets', ['uploader' => true])

@php
    use App\Support\Portfolio\Media;
    $tab = request('tab', array_key_first($groups));
    if (! isset($groups[$tab])) $tab = array_key_first($groups);
@endphp

@section('content')
    @include('backend.portfolio._header', [
        'heading' => 'Konten Halaman',
        'subheading' => 'Semua teks & gambar di halaman depan. Pilih tab, ubah, lalu simpan per tab.',
    ])

    @php $targets = ['brand' => '#home', 'hero' => '#home', 'about' => '#about', 'sections' => '#services', 'contact' => '#contact', 'footer' => '#footer', 'seo' => '#home', 'admin' => 'admin', 'login' => 'login']; @endphp
    <div class="d-grid gap-6">
        <div>
            <div class="card card-flush shadow-sm">
                <div class="card-body p-3">
                    <nav class="nav nav-pills gap-1 flex-nowrap overflow-auto" aria-label="Grup konten">
                        @foreach ($groups as $key => $group)
                            <a href="{{ route('pf.content.index', ['tab' => $key]) }}"
                                class="nav-link text-nowrap d-flex align-items-center gap-2 {{ $tab === $key ? 'active' : 'text-gray-700' }}">
                                <i class="ki-outline {{ $group['icon'] }} fs-4"></i>{{ $group['label'] }}
                            </a>
                        @endforeach
                    </nav>
                    <div class="px-3 pt-2 fs-8 text-muted">
                        Daftar berulang (layanan, skill, project, sosmed, dll) ada di menu <b>Portfolio</b> di sidebar.
                    </div>
                </div>
            </div>
        </div>

        <div class="pf-split">
            @php $group = $groups[$tab]; @endphp
            <form method="POST" action="{{ route('pf.content.update') }}" enctype="multipart/form-data" class="card card-flush shadow-sm">
                @csrf
                <input type="hidden" name="_group" value="{{ $tab }}">
                <div class="card-header pt-6 border-0">
                    <h3 class="card-title fw-bold fs-4"><i class="ki-outline {{ $group['icon'] }} fs-2 me-2 text-primary"></i>{{ $group['label'] }}</h3>
                </div>
                <div class="card-body pt-2">
                    <div class="row g-6">
                        @foreach ($group['fields'] as $key => $field)
                            @php
                                [$label, $type] = $field;
                                $value = old($key, $values[$key] ?? '');
                                $wide = in_array($type, ['textarea', 'image', 'file'], true);
                            @endphp
                            <div class="col-12 {{ $wide ? '' : 'col-xxl-6' }}">
                                @if ($type === 'toggle')
                                    <label class="form-check form-switch form-check-custom form-check-solid mt-md-8">
                                        <input type="hidden" name="{{ $key }}" value="0">
                                        <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" @checked($value === '1')>
                                        <span class="form-check-label fw-semibold text-gray-800">{{ $label }}</span>
                                    </label>
                                @else
                                    <label class="form-label fw-semibold" for="c-{{ $key }}">{{ $label }}</label>
                                    @switch($type)
                                        @case('textarea')
                                            <textarea class="form-control form-control-solid" id="c-{{ $key }}" name="{{ $key }}" rows="3">{{ $value }}</textarea>
                                            @break
                                        @case('select')
                                            <select class="form-select form-select-solid" id="c-{{ $key }}" name="{{ $key }}">
                                                @foreach ($field[3] as $opt)
                                                    <option value="{{ $opt }}" @selected($value === $opt)>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                            @break
                                        @case('color')
                                            <input type="color" class="form-control form-control-solid form-control-color w-100px" id="c-{{ $key }}" name="{{ $key }}" value="{{ $value ?: '#07070d' }}">
                                            @break
                                        @case('image')
                                        @case('file')
                                            @if ($values[$key] ?? null)
                                                <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded">
                                                    @if ($type === 'image')
                                                        <img src="{{ Media::url($values[$key]) }}" alt="" height="64" class="rounded" style="max-width:160px;object-fit:contain">
                                                    @else
                                                        <a href="{{ Media::url($values[$key]) }}" target="_blank" rel="noopener"><i class="ki-outline ki-document fs-2"></i> Lihat file</a>
                                                    @endif
                                                    <label class="form-check form-check-sm form-check-custom form-check-solid ms-auto">
                                                        <input class="form-check-input" type="checkbox" name="remove_{{ $key }}" value="1">
                                                        <span class="form-check-label text-danger">Hapus</span>
                                                    </label>
                                                </div>
                                            @endif
                                            @include('backend.portfolio._dropzone', ['name' => $key, 'id' => 'c-' . $key, 'kind' => $type,
                                                'accept' => $type === 'file' ? '.pdf,.doc,.docx' : null,
                                                'hint' => $type === 'file' ? 'PDF/DOC, maks 10 MB.' : null])
                                            @break
                                        @default
                                            <input type="{{ in_array($type, ['email', 'url'], true) ? $type : 'text' }}" class="form-control form-control-solid"
                                                id="c-{{ $key }}" name="{{ $key }}" value="{{ $value }}">
                                    @endswitch
                                @endif
                                @error($key)<div class="text-danger fs-7 mt-1">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary"><i class="ki-outline ki-check fs-4 me-1"></i>Simpan {{ $group['label'] }}</button>
                </div>
            </form>

            @if ($targets[$tab] ?? false)
                @include('backend.portfolio._preview', $tab === 'login'
                    ? ['url' => route('pf.login-preview'), 'target' => '']
                    : ($tab === 'admin' ? ['url' => route('dashboard'), 'target' => '']
                    : ['target' => $targets[$tab]]))
            @else
                <div class="card card-flush shadow-sm"><div class="card-body py-10 text-center text-muted"><i class="ki-outline ki-information-5 fs-2x d-block mb-3"></i>Pengaturan ini tampil di navbar &amp; footer <b>panel admin</b>. Simpan lalu lihat perubahan di atas dan bawah halaman ini.</div></div>
            @endif
        </div>
    </div>
@endsection
