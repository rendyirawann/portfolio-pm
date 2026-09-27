@extends('backend.layout.app')

@section('title', 'Project & Produk')

@section('content')
    @include('backend.portfolio._header', [
        'heading' => 'Project & Produk',
        'subheading' => 'Kelola project/produk beserta galeri gambar, file, dan link-nya.',
        'action' => ['url' => route('pf.projects.create'), 'label' => 'Tambah Project'],
    ])

    @include('backend.portfolio._assets')
    <div class="pf-split">
    <div class="card card-flush shadow-sm">
        <div class="card-header pt-6 border-0">
            <form method="GET" class="d-flex flex-wrap gap-3 w-100" role="search">
                <div class="position-relative flex-grow-1 mw-350px">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4 top-50 translate-middle-y"></i>
                    <input type="search" name="q" value="{{ $search }}" class="form-control form-control-solid ps-12" placeholder="Cari judul...">
                </div>
                <select name="category" class="form-select form-select-solid w-auto" onchange="this.form.submit()" aria-label="Kategori">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="type" class="form-select form-select-solid w-auto" onchange="this.form.submit()" aria-label="Tipe">
                    <option value="">Project & Produk</option>
                    @foreach (\App\Models\Portfolio\Project::TYPES as $k => $v)
                        <option value="{{ $k }}" @selected(request('type') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Project</th>
                            <th>Kategori</th>
                            <th class="text-center">Isi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-700">
                        @forelse ($projects as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-4">
                                        <img src="{{ $p->cover_url }}" alt="" width="72" height="54" class="rounded flex-shrink-0" style="object-fit:cover" loading="lazy">
                                        <div class="min-w-0">
                                            <a href="{{ route('pf.projects.edit', $p) }}" class="text-gray-900 text-hover-primary fw-bold d-block text-truncate mw-300px">{{ $p->title }}</a>
                                            <span class="badge badge-light-info fs-8">{{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? $p->type }}</span>
                                            @if ($p->is_featured)<span class="badge badge-light-warning fs-8">Unggulan</span>@endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $p->category?->name ?? '—' }}</td>
                                <td class="text-center text-nowrap fs-7">
                                    <span title="Gambar"><i class="ki-outline ki-picture"></i> {{ $p->images_count }}</span>
                                    <span class="ms-2" title="File"><i class="ki-outline ki-document"></i> {{ $p->files_count }}</span>
                                    <span class="ms-2" title="Link"><i class="ki-outline ki-fasten"></i> {{ $p->links_count }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $p->is_published ? 'badge-light-success' : 'badge-light-secondary' }}">
                                        {{ $p->is_published ? 'Publik' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($p->is_published)
                                        <a href="{{ route('portfolio.project', $p->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-icon btn-light" title="Lihat"><i class="ki-outline ki-eye fs-5"></i></a>
                                    @endif
                                    <a href="{{ route('pf.projects.edit', $p) }}" class="btn btn-sm btn-icon btn-light-primary" title="Edit"><i class="ki-outline ki-pencil fs-5"></i></a>
                                    @include('backend.portfolio._delete', ['action' => route('pf.projects.destroy', $p)])
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-15">Belum ada project. <a href="{{ route('pf.projects.create') }}">Tambah sekarang</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $projects->links() }}</div>
        </div>
    </div>

    @include('backend.portfolio._preview', ['target' => '#projects'])
    </div>
@endsection
