{{--
    Upload zone: click, drag & drop, or paste (Ctrl+V) a screenshot.
    Params: name, id, multiple (bool), kind ('image'|'file'), accept, hint, default (bool: receives paste when no zone is focused)
--}}
@php
    $multiple = $multiple ?? false;
    $kind = $kind ?? 'image';
@endphp
<div class="pf-drop" tabindex="0" role="button"
    data-input="#{{ $id }}" data-multiple="{{ $multiple ? '1' : '0' }}" data-kind="{{ $kind }}"
    data-max="{{ \App\Support\Portfolio\Media::MAX_KB * 1024 }}" @if (! empty($default)) data-paste-default @endif
    aria-label="Pilih, seret, atau tempel {{ $kind === 'image' ? 'gambar' : 'file' }}">
    <input type="file" class="d-none" id="{{ $id }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        @if ($multiple) multiple @endif accept="{{ $accept ?? ($kind === 'image' ? 'image/jpeg,image/png,image/webp,image/gif' : '') }}">
    <div class="pf-drop__empty">
        <i class="ki-outline {{ $kind === 'image' ? 'ki-picture' : 'ki-folder-up' }} fs-2x text-primary"></i>
        <div class="fw-semibold text-gray-800 mt-2">Klik, seret file ke sini, atau <kbd>Ctrl</kbd>+<kbd>V</kbd> untuk tempel</div>
        <div class="text-muted fs-8 mt-1">{{ $hint ?? 'Maks 10 MB per file. Otomatis dikompres tanpa mengurangi kualitas.' }}</div>
    </div>
    <div class="pf-drop__previews"></div>
</div>
