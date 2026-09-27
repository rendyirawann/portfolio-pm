@extends('backend.layout.app')

@section('title', ($item->exists ? 'Edit ' : 'Tambah ') . $singular)

@php
    $hasUpload = collect($fields)->contains(fn ($f) => $f['type'] === 'image');
    $hasSocial = collect($fields)->contains(fn ($f) => $f['type'] === 'social');
@endphp

@include('backend.portfolio._assets', ['uploader' => $hasUpload])

@section('content')
    @include('backend.portfolio._header', [
        'heading' => ($item->exists ? 'Edit ' : 'Tambah ') . $singular,
        'subheading' => $description,
        'back' => route("{$route}.index"),
        'backLabel' => $title,
    ])

    <div class="pf-split">
    <form method="POST" enctype="multipart/form-data"
        action="{{ $item->exists ? route("{$route}.update", $item->id) : route("{$route}.store") }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <div class="card card-flush shadow-sm">
            <div class="card-body pt-8">
                <div class="row g-6">
                    @foreach ($fields as $name => $field)
                        @php $value = old($name, $item->{$name}); @endphp
                        <div class="col-12 {{ ($field['col'] ?? 12) === 6 ? 'col-xxl-6' : '' }}">
                            @switch($field['type'])
                                @case('toggle')
                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                        <input type="hidden" name="{{ $name }}" value="0">
                                        <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1" @checked($value)>
                                        <span class="form-check-label fw-semibold text-gray-800">{{ $field['label'] }}</span>
                                    </label>
                                    @break

                                @case('textarea')
                                    <label class="form-label fw-semibold" for="f-{{ $name }}">{{ $field['label'] }}</label>
                                    <textarea class="form-control form-control-solid" id="f-{{ $name }}" name="{{ $name }}" rows="4">{{ $value }}</textarea>
                                    @break

                                @case('image')
                                    <label class="form-label fw-semibold">{{ $field['label'] }}</label>
                                    @if ($item->{$name})
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <img src="{{ \App\Support\Portfolio\Media::url($item->{$name}) }}" alt="" width="64" height="64" class="rounded" style="object-fit:cover">
                                            <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" name="remove_{{ $name }}" value="1">
                                                <span class="form-check-label text-danger">Hapus gambar</span>
                                            </label>
                                        </div>
                                    @endif
                                    @include('backend.portfolio._dropzone', ['name' => $name, 'id' => 'f-' . $name, 'default' => true])
                                    @break

                                @case('icon')
                                    <label class="form-label fw-semibold" for="f-{{ $name }}">{{ $field['label'] }}</label>
                                    <div class="input-group input-group-solid">
                                        <span class="input-group-text w-50px justify-content-center"><i class="{{ $value ?: 'fa-solid fa-star' }} fs-4 text-primary" data-icon-preview></i></span>
                                        <input type="text" class="form-control form-control-solid" id="f-{{ $name }}" name="{{ $name }}" value="{{ $value }}" data-icon-input placeholder="fa-solid fa-code">
                                    </div>
                                    @break

                                @case('social')
                                    <label class="form-label fw-semibold" for="f-{{ $name }}">{{ $field['label'] }}</label>
                                    <div class="input-group input-group-solid">
                                        <span class="input-group-text w-50px justify-content-center"><i class="{{ $item->exists ? $item->icon : 'fa-solid fa-globe' }} fs-4 text-primary" data-social-preview></i></span>
                                        <input type="text" class="form-control form-control-solid" id="f-{{ $name }}" name="{{ $name }}" value="{{ $value }}" data-social-input placeholder="https://github.com/username" required>
                                    </div>
                                    <div class="fs-8 text-primary mt-1" data-social-name></div>
                                    @break

                                @default
                                    <label class="form-label fw-semibold" for="f-{{ $name }}">{{ $field['label'] }}</label>
                                    <input type="{{ $field['type'] === 'number' ? 'number' : 'text' }}" class="form-control form-control-solid"
                                        id="f-{{ $name }}" name="{{ $name }}" value="{{ $value }}">
                            @endswitch

                            @if (! empty($field['help']))
                                <div class="form-text">{{ $field['help'] }}</div>
                            @endif
                            @error($name)
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="f-sort">Urutan tampil</label>
                        <input type="number" min="0" class="form-control form-control-solid" id="f-sort" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}">
                        <div class="form-text">Angka kecil tampil lebih dulu.</div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route("{$route}.index") }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="ki-outline ki-check fs-4 me-1"></i>Simpan</button>
            </div>
        </div>
    </form>

    @include('backend.portfolio._preview', ['target' => $previewTarget, 'prefix' => $item->exists ? $previewKey . '.' . $item->id . '.' : ''])
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var icon = document.querySelector('[data-icon-input]');
            if (icon) icon.addEventListener('input', function () {
                document.querySelector('[data-icon-preview]').className = (icon.value.trim() || 'fa-solid fa-star') + ' fs-4 text-primary';
            });

            @if ($hasSocial)
                var map = @json(\App\Support\Portfolio\SocialPlatform::hostMap());
                var input = document.querySelector('[data-social-input]');
                var preview = document.querySelector('[data-social-preview]');
                var label = document.querySelector('[data-social-name]');
                var detect = function () {
                    var v = input.value.trim(), found = { name: 'Website', icon: 'fa-solid fa-globe' };
                    if (/^mailto:/i.test(v)) found = { name: 'Email', icon: 'fa-solid fa-envelope' };
                    else if (/^tel:/i.test(v)) found = { name: 'Telepon', icon: 'fa-solid fa-phone' };
                    else {
                        try {
                            var host = new URL(v).hostname.toLowerCase().replace(/^(www|m|mobile|web|api)\./, '');
                            Object.keys(map).some(function (h) {
                                if (host === h || host.endsWith('.' + h)) { found = map[h]; return true; }
                            });
                        } catch (e) { if (!v) { label.textContent = ''; return; } }
                    }
                    preview.className = found.icon + ' fs-4 text-primary';
                    label.textContent = 'Terdeteksi: ' + found.name;
                };
                input.addEventListener('input', detect);
                detect();
            @endif
        })();
    </script>
@endpush
