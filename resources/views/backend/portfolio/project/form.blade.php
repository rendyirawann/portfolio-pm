@extends('backend.layout.app')

@section('title', $project->exists ? 'Edit Project' : 'Tambah Project')

@include('backend.portfolio._assets', ['uploader' => true])

@php
    use App\Support\Portfolio\Media;
    $links = old('links', $project->exists ? $project->links->map->only(['label', 'url'])->all() : []);
    if ($links === []) $links = [['label' => '', 'url' => '']];
@endphp

@section('content')
    @include('backend.portfolio._header', [
        'heading' => $project->exists ? 'Edit: ' . $project->title : 'Tambah Project / Produk',
        'subheading' => 'Tip: salin (copy) atau screenshot gambar, klik kotak upload, lalu tekan Ctrl+V untuk menempel.',
        'back' => route('pf.projects.index'),
        'backLabel' => 'Project',
    ])

    <form method="POST" enctype="multipart/form-data"
        action="{{ $project->exists ? route('pf.projects.update', $project) : route('pf.projects.store') }}">
        @csrf
        @if ($project->exists) @method('PUT') @endif

        <div class="row g-6">
            {{-- ================= Main column ================= --}}
            <div class="col-12 col-xl-7">
                <div class="card card-flush shadow-sm mb-6">
                    <div class="card-header pt-6 border-0"><h3 class="card-title fw-bold fs-5">Informasi Utama</h3></div>
                    <div class="card-body pt-2">
                        <div class="row g-5">
                            <div class="col-12">
                                <label class="form-label fw-semibold required" for="title">Judul</label>
                                <input type="text" class="form-control form-control-solid" id="title" name="title" value="{{ old('title', $project->title) }}" required maxlength="150">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="slug">Slug URL</label>
                                <input type="text" class="form-control form-control-solid" id="slug" name="slug" value="{{ old('slug', $project->slug) }}" placeholder="otomatis dari judul">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="type">Tipe</label>
                                <select class="form-select form-select-solid" id="type" name="type">
                                    @foreach (\App\Models\Portfolio\Project::TYPES as $k => $v)
                                        <option value="{{ $k }}" @selected(old('type', $project->type) === $k)>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="cat">Kategori</label>
                                <select class="form-select form-select-solid" id="cat" name="project_category_id">
                                    <option value="">—</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}" @selected(old('project_category_id', $project->project_category_id) == $c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('pf.categories.create') }}" class="fs-8">+ kategori baru</a>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="summary">Ringkasan (tampil di kartu)</label>
                                <input type="text" class="form-control form-control-solid" id="summary" name="summary" value="{{ old('summary', $project->summary) }}" maxlength="300">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="description">Deskripsi lengkap</label>
                                <textarea class="form-control form-control-solid" id="description" name="description" rows="9">{{ old('description', $project->description) }}</textarea>
                                <div class="form-text">Baris kosong = paragraf baru. Baris pendek tanpa titik di akhir dianggap sub-judul.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="client">Klien</label>
                                <input type="text" class="form-control form-control-solid" id="client" name="client" value="{{ old('client', $project->client) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" for="year">Tahun</label>
                                <input type="text" class="form-control form-control-solid" id="year" name="year" value="{{ old('year', $project->year) }}" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="tech">Tech stack</label>
                                <input type="text" class="form-control form-control-solid" id="tech" name="tech_stack" value="{{ old('tech_stack', $project->tech_stack) }}" placeholder="Laravel, Vue, PostgreSQL">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ---------- Gallery (child: project_images) ---------- --}}
                <div class="card card-flush shadow-sm mb-6">
                    <div class="card-header pt-6 border-0">
                        <h3 class="card-title fw-bold fs-5">Galeri Gambar</h3>
                        <div class="card-toolbar text-muted fs-7">{{ $project->images->count() }} tersimpan</div>
                    </div>
                    <div class="card-body pt-2">
                        @if ($project->images->isNotEmpty())
                            <div class="pf-gallery mb-6">
                                @foreach ($project->images as $img)
                                    <div class="pf-gallery__item" data-gallery-item>
                                        <img src="{{ Media::url($img->thumb_path ?: $img->path) }}" alt="" loading="lazy">
                                        <div class="pf-gallery__body">
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="existing_images[{{ $img->id }}][caption]" value="{{ $img->caption }}" placeholder="Caption">
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" min="0" class="form-control form-control-sm form-control-solid w-70px" name="existing_images[{{ $img->id }}][sort_order]" value="{{ $img->sort_order }}" title="Urutan">
                                                <label class="form-check form-check-sm form-check-custom form-check-solid ms-auto" title="Hapus">
                                                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $img->id }}" data-delete-toggle>
                                                    <span class="form-check-label text-danger fs-8">Hapus</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @include('backend.portfolio._dropzone', ['name' => 'images', 'id' => 'images', 'multiple' => true, 'default' => true,
                            'hint' => 'Bisa banyak sekaligus (maks 20 per simpan). Maks 10 MB per gambar, otomatis dikompres ke WebP.'])
                    </div>
                </div>

                {{-- ---------- Files (child: project_files) ---------- --}}
                <div class="card card-flush shadow-sm mb-6">
                    <div class="card-header pt-6 border-0"><h3 class="card-title fw-bold fs-5">File Lampiran</h3></div>
                    <div class="card-body pt-2">
                        @foreach ($project->files as $file)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom border-dashed">
                                <i class="ki-outline ki-document fs-2 text-primary"></i>
                                <a href="{{ Media::url($file->path) }}" target="_blank" rel="noopener" class="text-gray-800 text-hover-primary text-truncate">{{ $file->original_name }}</a>
                                <span class="text-muted fs-8 text-nowrap">{{ number_format($file->size / 1024, 0) }} KB</span>
                                <label class="form-check form-check-sm form-check-custom form-check-solid ms-auto">
                                    <input class="form-check-input" type="checkbox" name="delete_files[]" value="{{ $file->id }}">
                                    <span class="form-check-label text-danger fs-8">Hapus</span>
                                </label>
                            </div>
                        @endforeach
                        <div class="mt-4">
                            @include('backend.portfolio._dropzone', ['name' => 'files', 'id' => 'files', 'multiple' => true, 'kind' => 'file',
                                'accept' => '.pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.fig,.psd,.ai,.jpg,.jpeg,.png,.webp,.gif,.mp4',
                                'hint' => 'PDF, ZIP, DOCX, XLSX, PPTX, FIG, PSD, MP4… Maks 10 file per simpan, 10 MB per file.'])
                        </div>
                    </div>
                </div>

                {{-- ---------- Links (child: project_links) ---------- --}}
                <div class="card card-flush shadow-sm">
                    <div class="card-header pt-6 border-0">
                        <h3 class="card-title fw-bold fs-5">Link</h3>
                        <div class="card-toolbar"><button type="button" class="btn btn-sm btn-light-primary" data-add-link><i class="ki-outline ki-plus fs-5"></i>Tambah link</button></div>
                    </div>
                    <div class="card-body pt-2" data-links>
                        @foreach ($links as $i => $link)
                            <div class="row g-3 pf-link-row" data-link-row>
                                <div class="col-sm-4"><input type="text" class="form-control form-control-solid" name="links[{{ $i }}][label]" value="{{ $link['label'] ?? '' }}" placeholder="Live Demo / GitHub / Play Store"></div>
                                <div class="col-sm-7"><input type="url" class="form-control form-control-solid" name="links[{{ $i }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="https://"></div>
                                <div class="col-sm-1"><button type="button" class="btn btn-icon btn-light-danger w-100" data-remove-link aria-label="Hapus link"><i class="ki-outline ki-trash fs-5"></i></button></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ================= Side column ================= --}}
            <div class="col-12 col-xl-5">
                <div class="mb-6 pf-sticky">
                    @include('backend.portfolio._preview', $project->exists && $project->is_published
                        ? ['url' => route('portfolio.project', $project->slug), 'target' => '', 'prefix' => 'projects.' . $project->id . '.']
                        : ['target' => '#projects', 'prefix' => $project->exists ? 'projects.' . $project->id . '.' : ''])
                </div>
                <div class="card card-flush shadow-sm mb-6">
                    <div class="card-header pt-6 border-0"><h3 class="card-title fw-bold fs-5">Publikasi</h3></div>
                    <div class="card-body pt-2 d-grid gap-4">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1" @checked(old('is_published', $project->is_published))>
                            <span class="form-check-label fw-semibold">Tampilkan di website</span>
                        </label>
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project->is_featured))>
                            <span class="form-check-label fw-semibold">Project unggulan (tampil paling depan)</span>
                        </label>
                        <div>
                            <label class="form-label fw-semibold" for="sort">Urutan</label>
                            <input type="number" min="0" class="form-control form-control-solid" id="sort" name="sort_order" value="{{ old('sort_order', $project->sort_order) }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="ki-outline ki-check fs-4 me-1"></i>Simpan Project</button>
                    </div>
                </div>

                <div class="card card-flush shadow-sm mb-6">
                    <div class="card-header pt-6 border-0"><h3 class="card-title fw-bold fs-5">Gambar Utama (Cover)</h3></div>
                    <div class="card-body pt-2">
                        @if ($project->cover_image)
                            <img src="{{ Media::url($project->cover_image) }}" alt="" class="w-100 rounded mb-3" style="aspect-ratio:16/10;object-fit:cover">
                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" name="remove_cover" value="1">
                                <span class="form-check-label text-danger">Hapus cover</span>
                            </label>
                        @endif
                        @include('backend.portfolio._dropzone', ['name' => 'cover', 'id' => 'cover',
                            'hint' => 'Kosongkan → pakai gambar galeri pertama.'])
                    </div>
                </div>

                <div class="card card-flush shadow-sm">
                    <div class="card-header pt-6 border-0"><h3 class="card-title fw-bold fs-5">SEO</h3></div>
                    <div class="card-body pt-2 d-grid gap-4">
                        <div>
                            <label class="form-label fw-semibold" for="mt">Meta title</label>
                            <input type="text" class="form-control form-control-solid" id="mt" name="meta_title" value="{{ old('meta_title', $project->meta_title) }}" maxlength="150" placeholder="default: judul project">
                        </div>
                        <div>
                            <label class="form-label fw-semibold" for="md">Meta description</label>
                            <textarea class="form-control form-control-solid" id="md" name="meta_description" rows="3" maxlength="300" placeholder="default: ringkasan">{{ old('meta_description', $project->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            var box = document.querySelector('[data-links]');
            var next = box.querySelectorAll('[data-link-row]').length;

            document.querySelector('[data-add-link]').addEventListener('click', function () {
                var row = box.querySelector('[data-link-row]').cloneNode(true);
                row.querySelectorAll('input').forEach(function (input) {
                    input.value = '';
                    input.name = input.name.replace(/links\[\d+\]/, 'links[' + next + ']');
                });
                next++;
                box.appendChild(row);
            });

            box.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-remove-link]');
                if (!btn) return;
                var rows = box.querySelectorAll('[data-link-row]');
                var row = btn.closest('[data-link-row]');
                rows.length > 1 ? row.remove() : row.querySelectorAll('input').forEach(function (i) { i.value = ''; });
            });

            document.querySelectorAll('[data-delete-toggle]').forEach(function (cb) {
                cb.addEventListener('change', function () {
                    cb.closest('[data-gallery-item]').classList.toggle('is-deleted', cb.checked);
                });
            });
        })();
    </script>
@endpush
