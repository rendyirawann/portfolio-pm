{{-- Portfolio admin menu entries, shared by the sidebar drawer and the desktop dropdown. --}}
@php
    $pfUnread = \Illuminate\Support\Facades\Cache::remember('pf.unread', 60, fn () => \App\Models\Portfolio\ContactMessage::whereNull('read_at')->count());
    $pfLinks = [
        ['pf.content.index', 'admin/portfolio/content*', 'Konten Halaman', 'ki-document'],
        ['pf.projects.index', 'admin/portfolio/projects*', 'Project & Produk', 'ki-abstract-41'],
        ['pf.categories.index', 'admin/portfolio/categories*', 'Kategori Project', 'ki-category'],
        ['pf.services.index', 'admin/portfolio/services*', 'Layanan', 'ki-briefcase'],
        ['pf.skills.index', 'admin/portfolio/skills*', 'Keahlian', 'ki-technology-2'],
        ['pf.experiences.index', 'admin/portfolio/experiences*', 'Pengalaman', 'ki-medal-star'],
        ['pf.stats.index', 'admin/portfolio/stats*', 'Statistik', 'ki-chart-simple'],
        ['pf.testimonials.index', 'admin/portfolio/testimonials*', 'Testimoni', 'ki-message-text-2'],
        ['pf.nav.index', 'admin/portfolio/nav*', 'Menu Navbar', 'ki-burger-menu-2'],
        ['pf.socials.index', 'admin/portfolio/socials*', 'Sosial Media', 'ki-share'],
        ['pf.messages.index', 'admin/portfolio/messages*', 'Pesan Masuk', 'ki-sms'],
    ];
@endphp
@foreach ($pfLinks as [$routeName, $pattern, $label, $icon])
    <div class="menu-item">
        <a class="menu-link {{ request()->is($pattern) ? 'active' : '' }}" href="{{ route($routeName) }}">
            <span class="menu-icon"><i class="ki-outline {{ $icon }} fs-4"></i></span>
            <span class="menu-title">{{ $label }}</span>
            @if ($routeName === 'pf.messages.index' && $pfUnread > 0)
                <span class="menu-badge"><span class="badge badge-danger badge-circle fs-8">{{ $pfUnread }}</span></span>
            @endif
        </a>
    </div>
@endforeach
