@extends('backend.layout.app')

@section('title', $title)

@include('backend.portfolio._assets')

@section('content')
    @include('backend.portfolio._header', [
        'heading' => $title,
        'subheading' => $description,
        'action' => ['url' => route("{$route}.create"), 'label' => 'Tambah ' . $singular],
    ])

    <div class="pf-split">
    <div class="card card-flush shadow-sm">
        <div class="card-header pt-6 border-0 flex-wrap gap-3">
            <form method="GET" class="d-flex align-items-center position-relative w-100 mw-350px" role="search">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                <input type="search" name="q" value="{{ $search }}" class="form-control form-control-solid ps-12"
                    placeholder="Cari {{ strtolower($singular) }}..." aria-label="Cari" />
            </form>
            <div class="card-toolbar text-muted fs-7">{{ $items->total() }} data</div>
        </div>

        <div class="card-body pt-2">
            @include('backend.portfolio._guide', ['steps' => $guide])
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-60px">Urut</th>
                            @foreach ($columns as $heading)
                                <th>{{ $heading }}</th>
                            @endforeach
                            <th class="text-end w-150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-700">
                        @forelse ($items as $item)
                            <tr>
                                <td><span class="badge badge-light">{{ $item->sort_order }}</span></td>
                                @foreach ($columns as $column => $heading)
                                    <td class="mw-300px text-truncate">
                                        @switch(true)
                                            @case($column === 'is_active')
                                                <span class="badge {{ $item->is_active ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ $item->is_active ? 'Tampil' : 'Disembunyikan' }}
                                                </span>
                                                @break
                                            @case($column === 'platform')
                                                <span class="d-inline-flex align-items-center gap-2">
                                                    <span class="symbol symbol-35px"><span class="symbol-label bg-light-primary text-primary fs-4"><i class="{{ $item->icon }}"></i></span></span>
                                                    {{ \App\Support\Portfolio\SocialPlatform::name($item->platform) }}
                                                </span>
                                                @break
                                            @case($column === 'icon')
                                                <span class="symbol symbol-35px"><span class="symbol-label bg-light-primary text-primary fs-4"><i class="{{ $item->icon }}"></i></span></span>
                                                @break
                                            @case(($fields[$column]['type'] ?? null) === 'image')
                                                @if ($item->{$column})
                                                    <img src="{{ \App\Support\Portfolio\Media::url($item->{$column}) }}" alt="" width="40" height="40" class="rounded" style="object-fit:cover" loading="lazy">
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                                @break
                                            @case(($fields[$column]['type'] ?? null) === 'select')
                                                <span class="badge badge-light-info">{{ $fields[$column]['options'][$item->{$column}] ?? $item->{$column} }}</span>
                                                @break
                                            @case($column === 'level')
                                                <div class="d-flex align-items-center gap-2 mw-150px">
                                                    <div class="progress h-6px w-100"><div class="progress-bar bg-primary" style="width: {{ (int) $item->level }}%"></div></div>
                                                    <span class="fs-8">{{ $item->level }}</span>
                                                </div>
                                                @break
                                            @default
                                                {{ \Illuminate\Support\Str::limit((string) $item->{$column}, 60) ?: '—' }}
                                        @endswitch
                                    </td>
                                @endforeach
                                <td class="text-end text-nowrap">
                                    <a href="{{ route("{$route}.edit", $item->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Edit" aria-label="Edit">
                                        <i class="ki-outline ki-pencil fs-5"></i>
                                    </a>
                                    @include('backend.portfolio._delete', ['action' => route("{$route}.destroy", $item->id)])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 2 }}" class="text-center text-muted py-15">
                                    <i class="ki-outline {{ $icon }} fs-3x d-block mb-3 text-gray-400"></i>
                                    Belum ada data. <a href="{{ route("{$route}.create") }}">Tambah sekarang</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $items->links() }}</div>
        </div>
    </div>

    @if ($previewTarget)
        @include('backend.portfolio._preview', ['target' => $previewTarget])
    @endif
    </div>
@endsection
