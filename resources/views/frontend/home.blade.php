@extends('frontend.layout')

@php
    use App\Support\Portfolio\Media;
    use App\Support\Portfolio\PortfolioData;
    use Illuminate\Support\Str;
    $c = $content;

    // Subtitle with the admin-chosen words tinted in the accent colour.
    $subtitle = e($c['hero_subtitle']);
    foreach (array_values(array_filter(array_map('trim', explode(',', (string) $c['hero_highlight'])))) as $word) {
        $subtitle = preg_replace('/' . preg_quote(e($word), '/') . '/iu', '<span class="hl">$0</span>', $subtitle, 1);
    }

    $heroImage = Media::url($c['hero_image'], 'assets/front/img/hero-default.svg');
    $photo = Media::url($c['about_photo'], 'assets/front/img/profile-default.svg');
    $wa = preg_replace('/\D/', '', (string) $c['contact_whatsapp']);
    $waLink = $wa ? 'https://wa.me/' . $wa . '?text=' . rawurlencode($c['contact_whatsapp_text']) : null;
    $budgets = array_values(array_filter(array_map('trim', explode(',', (string) $c['contact_budgets']))));

    // Hero deck: featured projects first (the visible() scope orders them so).
    $deck = $projects->take(5);

    // Ticker lines: service titles after the hero, skill names before contact.
    $tickerA = $services->pluck('title')->filter()->values();
    $tickerB = $skills->flatten()->pluck('name')->filter()->values();
@endphp

@push('head')
    <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
@endpush

@section('content')
    {{-- ================= HERO ================= --}}
    <section class="hero" id="home" aria-labelledby="hero-title">
        {{-- Giant word behind everything; textLength stretches it to the full width. --}}
        <div class="hero__word" aria-hidden="true">
            <svg viewBox="0 0 1000 250"><text x="500" y="236" text-anchor="middle" textLength="1000" lengthAdjust="spacingAndGlyphs" data-pf="hero_word">{{ $c['hero_word'] }}</text></svg>
        </div>

        <div class="container hero__stage">
            <div class="hero__copy">
                <p class="hero__eyebrow" data-pf="hero_eyebrow">{{ $c['hero_eyebrow'] }}</p>
                <h1 class="hero__title" id="hero-title">
                    <span class="hero__line" data-pf="hero_title_1">{{ $c['hero_title_1'] }}</span>
                    <span class="hero__line" data-pf="hero_title_2">{{ $c['hero_title_2'] }}</span>
                </h1>
                <p class="hero__role" data-pf="about_role">{{ $c['about_role'] }}</p>
                @if ($c['hero_description'])
                    <p class="hero__desc" data-pf="hero_description">{{ $c['hero_description'] }}</p>
                @endif
                <div class="hero__actions">
                    <a href="{{ PortfolioData::href($c['hero_cta_link']) }}" class="btn btn--glow"><span data-pf="hero_cta_label">{{ $c['hero_cta_label'] }}</span> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @if ($c['hero_cta2_label'])
                        <a href="{{ PortfolioData::href($c['hero_cta2_link']) }}" class="btn btn--ghost"><span data-pf="hero_cta2_label">{{ $c['hero_cta2_label'] }}</span> <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>

            <div class="hero__visual">
                <div class="hero__frame">
                    <img src="{{ $heroImage }}" alt="{{ $c['about_name'] }} — {{ $c['about_role'] }}" class="hero__img" data-pf-img="hero_image" width="720" height="820" fetchpriority="high" decoding="async">
                </div>
            </div>

            <aside class="hero__side">
                <p class="hero__tagline"><span data-pf="hero_subtitle">{!! $subtitle !!}</span></p>
                @if ($stats->isNotEmpty())
                    <ul class="hero__stats" aria-label="Statistik">
                        @foreach ($stats->take(4) as $s)
                            <li><b data-pf="stats.{{ $s->id }}.value">{{ $s->value }}</b><span data-pf="stats.{{ $s->id }}.label">{{ $s->label }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </aside>
        </div>

        @if ($deck->isNotEmpty())
            <div class="container hero__deck" data-cursor="drag">
                <div class="deck__head">
                    <span class="deck__label" data-pf="hero_deck_label">{{ $c['hero_deck_label'] }}</span>
                    <a href="{{ route('portfolio.projects') }}">Semua project ({{ $projectTotal }}) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="deck" aria-label="Project pilihan — geser untuk memutar" data-deck tabindex="0">
                    @php $ringText = Str::upper(trim((string) $c['hero_deck_label']) ?: 'Selected work'); @endphp
                    <svg class="deck__ring" viewBox="0 0 300 300" aria-hidden="true">
                        <defs><path id="deck-ring-path" d="M150,150 m-128,0 a128,128 0 1,1 256,0 a128,128 0 1,1 -256,0"/></defs>
                        <circle cx="150" cy="150" r="112"/>
                        <text><textPath href="#deck-ring-path">{{ $ringText }} &#8226; {{ $ringText }} &#8226; {{ $ringText }} &#8226; {{ $ringText }} &#8226;</textPath></text>
                    </svg>
                    @foreach ($deck as $p)
                        <a href="{{ route('portfolio.project', $p->slug) }}" class="deck__card" aria-label="{{ $p->title }}" draggable="false">
                            <span class="deck__card-inner">
                                <img src="{{ $p->cover_url }}" alt="" width="300" height="400" loading="eager" decoding="async" draggable="false">
                                <span class="deck__caption"><b>{{ Str::limit($p->title, 32) }}</b><span>{{ $p->category?->name ?: (\App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project') }}</span></span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
@if ($tickerA->isNotEmpty())
        <div class="marquee" aria-hidden="true">
            <div class="marquee__track">
                @foreach ([0, 1] as $rep)@foreach ($tickerA as $w)<span>{{ $w }}</span><i>&#10022;</i>@endforeach @endforeach
            </div>
        </div>
    @endif
    </section>

    {{-- ================= ABOUT ================= --}}
    <section class="section about" id="about" aria-labelledby="about-title">
        <div class="container about__grid">
            <div data-reveal>
                <img src="{{ $photo }}" alt="Foto {{ $c['about_name'] }}" class="about__photo" data-pf-img="about_photo" width="520" height="650" loading="lazy" decoding="async">
                <ul class="about__meta">
                    <li><small>Lokasi</small><span data-pf="about_location">{{ $c['about_location'] }}</span></li>
                    <li><small>Waktu lokal</small><time data-clock data-tz="{{ $c['about_timezone'] ?: 'Asia/Jakarta' }}">--:--</time></li>
                    <li><small>Status</small>
                        @if ($c['about_available'] === '1')
                            <span class="pill"><span class="pill__dot" aria-hidden="true"></span><span data-pf="about_available_text">{{ $c['about_available_text'] }}</span></span>
                        @else
                            <span>Busy</span>
                        @endif
                    </li>
                </ul>
            </div>

            <div data-reveal>
                <p class="kicker" data-pf="about_title">{{ $c['about_title'] }}</p>
                <h2 class="about__name" id="about-title" data-pf="about_name">{{ $c['about_name'] }}</h2>
                <p class="about__role" data-pf="about_role">{{ $c['about_role'] }}</p>
                <p class="about__bio" data-pf="about_bio">{{ $c['about_bio'] }}</p>
                @if ($c['about_level'])
                    <p class="about__level"><b data-pf="about_level">{{ $c['about_level'] }}</b><span data-pf="about_level_label">{{ $c['about_level_label'] }}</span></p>
                @endif
                <div class="about__actions">
                    @if ($c['about_resume'])
                        <a href="{{ Media::url($c['about_resume']) }}" class="btn btn--glow btn--sm" target="_blank" rel="noopener" download>
                            <i class="fa-solid fa-download" aria-hidden="true"></i> <span data-pf="about_resume_label">{{ $c['about_resume_label'] }}</span>
                        </a>
                    @endif
                    @if ($c['contact_linkedin'])
                        <a href="{{ $c['contact_linkedin'] }}" class="btn btn--ghost btn--sm" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i> LinkedIn</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ================= SERVICES ================= --}}
    @if ($services->isNotEmpty())
        <section class="section" id="services" aria-labelledby="services-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'services', 'id' => 'services-title', 'kicker' => 'Services', 'title' => $c['services_title'], 'subtitle' => $c['services_subtitle']])
                <ol class="rows">
                    @foreach ($services as $i => $svc)
                        <li class="rows__item" data-reveal>
                            <span class="rows__index">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3 class="rows__title" data-pf="services.{{ $svc->id }}.title">{{ $svc->title }}</h3>
                                <p class="rows__text" data-pf="services.{{ $svc->id }}.description">{{ $svc->description }}</p>
                            </div>
                            <span class="rows__icon" aria-hidden="true"><i class="{{ $svc->icon ?: 'fa-solid fa-circle' }}" data-pf-icon="services.{{ $svc->id }}.icon"></i></span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- ================= PROJECTS ================= --}}
    @if ($projects->isNotEmpty())
        <section class="section" id="projects" aria-labelledby="projects-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'projects', 'id' => 'projects-title', 'kicker' => 'Selected work', 'title' => $c['projects_title'], 'subtitle' => $c['projects_subtitle']])
                @include('frontend.partials.project-grid', ['projects' => $projects, 'categories' => $categories])
                @if ($projectTotal > $projects->count())
                    <div class="mt-lg">
                        <a href="{{ route('portfolio.projects') }}" class="btn btn--ghost">Lihat semua project ({{ $projectTotal }}) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ================= SKILLS ================= --}}
    @if ($skills->isNotEmpty())
        <section class="section" id="skills" aria-labelledby="skills-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'skills', 'id' => 'skills-title', 'kicker' => 'Skills', 'title' => $c['skills_title'], 'subtitle' => $c['skills_subtitle']])
                <div class="skills">
                    @foreach ($skills as $group => $items)
                        <div class="skills__group" data-reveal>
                            <h3 class="skills__title">{{ $group }}</h3>
                            <ul>
                                @foreach ($items as $skill)
                                    <li class="skill" data-pf-level="skills.{{ $skill->id }}.level" style="--lvl: {{ (int) $skill->level }}%" title="{{ (int) $skill->level }}%">
                                        <span class="skill__name" data-pf="skills.{{ $skill->id }}.name">{{ $skill->name }}</span>
                                        <span class="skill__val" data-pf="skills.{{ $skill->id }}.level">{{ (int) $skill->level }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= EXPERIENCE ================= --}}
    @if ($experiences->isNotEmpty())
        <section class="section" id="experience" aria-labelledby="exp-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'experience', 'id' => 'exp-title', 'kicker' => 'Experience', 'title' => $c['experience_title'], 'subtitle' => $c['experience_subtitle']])
                <ol class="timeline" data-reveal="line">
                    @foreach ($experiences as $exp)
                        <li class="timeline__item" data-reveal>
                            <span class="timeline__period" data-pf="experiences.{{ $exp->id }}.period">{{ $exp->period }}</span>

                            {{-- Marker: a dot, or the uploaded photo as a thumb. Both open the detail dialog. --}}
                            <button type="button"
                                    class="timeline__marker{{ $exp->image ? ' timeline__marker--photo' : '' }}"
                                    aria-haspopup="dialog"
                                    data-exp-open="exp-dialog-{{ $exp->id }}"
                                    aria-label="Lihat detail pengalaman: {{ $exp->role }} di {{ $exp->company }}">
                                @if ($exp->image)
                                    <span class="timeline__frame"><img src="{{ Media::url($exp->image) }}" alt="" loading="lazy" decoding="async"></span>
                                @endif
                            </button>

                            <div class="timeline__body">
                                <h3 data-pf="experiences.{{ $exp->id }}.role">{{ $exp->role }}</h3>
                                <p class="timeline__company" data-pf="experiences.{{ $exp->id }}.company">{{ $exp->company }}</p>
                                <p data-pf="experiences.{{ $exp->id }}.description">{{ $exp->description }}</p>
                            </div>

                            <dialog class="expdlg" id="exp-dialog-{{ $exp->id }}" aria-labelledby="exp-dialog-{{ $exp->id }}-title">
                                <button type="button" class="expdlg__close" data-exp-close aria-label="Tutup">&times;</button>
                                @if ($exp->image)
                                    <img class="expdlg__img" src="{{ Media::url($exp->image) }}" alt="Foto {{ $exp->company }}" loading="lazy" decoding="async">
                                @endif
                                <div class="expdlg__body">
                                    <span class="expdlg__period">{{ $exp->period }}</span>
                                    <h3 id="exp-dialog-{{ $exp->id }}-title">{{ $exp->role }}</h3>
                                    <p class="expdlg__company">{{ $exp->company }}</p>
                                    @if ($exp->description)
                                        <p class="expdlg__desc">{{ $exp->description }}</p>
                                    @endif
                                </div>
                            </dialog>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- ================= TESTIMONIALS ================= --}}
    @if ($testimonials->isNotEmpty())
        <section class="section" id="testimonials" aria-labelledby="testi-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'testimonials', 'id' => 'testi-title', 'kicker' => 'Reviews', 'title' => $c['testimonials_title'], 'subtitle' => $c['testimonials_subtitle']])
                <div class="testimonials">
                    @foreach ($testimonials as $t)
                        <figure class="testimonial" data-reveal>
                            <i class="fa-solid fa-quote-left testimonial__mark" aria-hidden="true"></i>
                            <blockquote data-pf="testimonials.{{ $t->id }}.quote">{{ $t->quote }}</blockquote>
                            <figcaption>
                                @if ($t->avatar)
                                    <img src="{{ Media::url($t->avatar) }}" alt="" width="40" height="40" loading="lazy">
                                @else
                                    <span class="testimonial__avatar" aria-hidden="true">{{ Str::upper(Str::substr($t->name, 0, 1)) }}</span>
                                @endif
                                <span><b data-pf="testimonials.{{ $t->id }}.name">{{ $t->name }}</b><small data-pf="testimonials.{{ $t->id }}.position">{{ $t->position }}</small></span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= CONTACT ================= --}}
    @if ($tickerB->isNotEmpty())
        <div class="marquee marquee--line" aria-hidden="true">
            <div class="marquee__track marquee__track--reverse">
                @foreach ([0, 1] as $rep)@foreach ($tickerB as $w)<span>{{ $w }}</span><i>&#10022;</i>@endforeach @endforeach
            </div>
        </div>
    @endif
    <section class="section contact" id="contact" aria-labelledby="contact-title">
        <div class="container contact__grid">
            <div class="contact__info" data-reveal>
                <p class="kicker">Contact</p>
                <h2 class="section-title" id="contact-title" data-pf="contact_title">{{ $c['contact_title'] }}</h2>
                <p class="section-sub" data-pf="contact_subtitle" style="margin-top:14px">{{ $c['contact_subtitle'] }}</p>

                <div class="contact__direct">
                    @if ($waLink)
                        <a href="{{ $waLink }}" class="direct" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span><b>WhatsApp</b><small>Chat langsung</small></span>
                        </a>
                    @endif
                    @if ($c['contact_linkedin'])
                        <a href="{{ $c['contact_linkedin'] }}" class="direct" target="_blank" rel="noopener">
                            <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i><span><b>LinkedIn</b><small>Terhubung profesional</small></span>
                        </a>
                    @endif
                    @if ($c['contact_email'])
                        <a href="mailto:{{ $c['contact_email'] }}" class="direct">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i><span><b>Email</b><small data-pf="contact_email">{{ $c['contact_email'] }}</small></span>
                        </a>
                    @endif
                    @if ($c['contact_address'])
                        <span class="direct">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><b>Lokasi</b><small data-pf="contact_address">{{ $c['contact_address'] }}</small></span>
                        </span>
                    @endif
                </div>
                @include('frontend.partials.socials', ['class' => 'socials'])
            </div>

            @if ($c['contact_form_enabled'] === '1')
                <form method="POST" action="{{ route('portfolio.contact') }}" class="form" data-contact-form data-reveal novalidate>
                    @csrf
                    <input type="hidden" name="_ts" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) time()) }}">
                    <div class="hp" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form__status" role="status" aria-live="polite" data-form-status>
                        @if (session('contact_success'))<p class="ok">{{ session('contact_success') }}</p>@endif
                        @if ($errors->any())<p class="err">{{ $errors->first() }}</p>@endif
                    </div>

                    <div class="form__row">
                        <div class="field">
                            <label for="f-name">Nama *</label>
                            <input id="f-name" name="name" type="text" required minlength="2" maxlength="100" autocomplete="name" value="{{ old('name') }}">
                        </div>
                        <div class="field">
                            <label for="f-email">Email *</label>
                            <input id="f-email" name="email" type="email" required maxlength="150" autocomplete="email" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="form__row">
                        <div class="field">
                            <label for="f-phone">WhatsApp / Telepon</label>
                            <input id="f-phone" name="phone" type="tel" maxlength="30" autocomplete="tel" value="{{ old('phone') }}">
                        </div>
                        <div class="field">
                            <label for="f-budget">Budget</label>
                            <select id="f-budget" name="budget">
                                <option value="">Pilih budget</option>
                                @foreach ($budgets as $b)
                                    <option value="{{ $b }}" @selected(old('budget') === $b)>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="f-subject">Subjek</label>
                        <input id="f-subject" name="subject" type="text" maxlength="150" value="{{ old('subject') }}" placeholder="Mis. Pengelolaan project aplikasi internal">
                    </div>
                    <div class="field">
                        <label for="f-message">Ceritakan project Anda *</label>
                        <textarea id="f-message" name="message" rows="5" required minlength="10" maxlength="5000">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn--glow" data-submit>
                        <span>Kirim Pesan</span> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
            @endif
        </div>
    </section>
@endsection
