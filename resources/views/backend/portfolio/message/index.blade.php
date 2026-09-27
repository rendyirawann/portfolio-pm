@extends('backend.layout.app')

@section('title', 'Pesan Masuk')

@section('content')
    @include('backend.portfolio._header', [
        'heading' => 'Pesan Masuk',
        'subheading' => 'Pesan dari form kontak website. ' . $unread . ' belum dibaca.',
    ])

    <div class="card card-flush shadow-sm">
        <div class="card-header pt-6 border-0">
            <form method="GET" class="d-flex flex-wrap gap-3 w-100" role="search">
                <input type="search" name="q" value="{{ $search }}" class="form-control form-control-solid mw-350px" placeholder="Cari nama, email, subjek...">
                <select name="status" class="form-select form-select-solid w-auto" onchange="this.form.submit()" aria-label="Status">
                    <option value="">Semua</option>
                    <option value="unread" @selected(request('status') === 'unread')>Belum dibaca</option>
                </select>
            </form>
        </div>
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                    <thead>
                        <tr class="text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Pengirim</th><th>Subjek</th><th>Budget</th><th>Email</th><th>Tanggal</th><th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($messages as $m)
                            <tr class="{{ $m->read_at ? '' : 'fw-bold' }}">
                                <td>
                                    @unless ($m->read_at)<span class="bullet bullet-dot bg-primary me-2"></span>@endunless
                                    <a href="{{ route('pf.messages.show', $m) }}" class="text-gray-900 text-hover-primary">{{ $m->name }}</a>
                                    <div class="text-muted fs-8">{{ $m->email }}</div>
                                </td>
                                <td class="mw-250px text-truncate">{{ $m->subject ?: '—' }}</td>
                                <td>{{ $m->budget ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $m->mail_sent ? 'badge-light-success' : 'badge-light-warning' }}" title="Status pengiriman SMTP">
                                        {{ $m->mail_sent ? 'Terkirim' : 'Gagal/Belum' }}
                                    </span>
                                </td>
                                <td class="text-nowrap fs-7">{{ $m->created_at->format('d M Y H:i') }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('pf.messages.show', $m) }}" class="btn btn-sm btn-icon btn-light-primary" title="Buka"><i class="ki-outline ki-eye fs-5"></i></a>
                                    @include('backend.portfolio._delete', ['action' => route('pf.messages.destroy', $m)])
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-15">Belum ada pesan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $messages->links() }}</div>
        </div>
    </div>
@endsection
