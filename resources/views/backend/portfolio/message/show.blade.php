@extends('backend.layout.app')

@section('title', 'Pesan dari ' . $message->name)

@section('content')
    @include('backend.portfolio._header', [
        'heading' => $message->subject ?: 'Pesan dari ' . $message->name,
        'subheading' => $message->created_at->format('d M Y H:i') . ' · IP ' . $message->ip,
        'back' => route('pf.messages.index'),
        'backLabel' => 'Pesan Masuk',
    ])

    <div class="row g-6">
        <div class="col-12 col-lg-8">
            <div class="card card-flush shadow-sm">
                <div class="card-body py-8">
                    <div class="fs-6 text-gray-800" style="white-space:pre-line;line-height:1.8">{{ $message->message }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card card-flush shadow-sm">
                <div class="card-body py-8 d-grid gap-4">
                    <div><div class="text-muted fs-8">Nama</div><div class="fw-bold">{{ $message->name }}</div></div>
                    <div><div class="text-muted fs-8">Email</div><div class="fw-bold">{{ $message->email }}</div></div>
                    @if ($message->phone)<div><div class="text-muted fs-8">Telepon</div><div class="fw-bold">{{ $message->phone }}</div></div>@endif
                    @if ($message->budget)<div><div class="text-muted fs-8">Budget</div><div class="fw-bold">{{ $message->budget }}</div></div>@endif
                    <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . ($message->subject ?: 'Project Anda')) }}" class="btn btn-primary">
                        <i class="ki-outline ki-sms fs-4 me-1"></i>Balas via Email
                    </a>
                    @if ($message->phone)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', preg_replace('/^0/', '62', $message->phone)) }}" target="_blank" rel="noopener" class="btn btn-light-success">
                            <i class="ki-outline ki-whatsapp fs-4 me-1"></i>Balas via WhatsApp
                        </a>
                    @endif
                    @include('backend.portfolio._delete', ['action' => route('pf.messages.destroy', $message)])
                </div>
            </div>
        </div>
    </div>
@endsection
