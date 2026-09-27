@extends('frontend.layout')

@php
    use App\Support\Portfolio\Media;
    use Illuminate\Support\Str;
    $c = $content;

    // Subtitle with the admin-chosen words tinted (alternating red / blue).
    $subtitle = e($c['hero_subtitle']);
    foreach (array_values(array_filter(array_map('trim', explode(',', (string) $c['hero_highlight'])))) as $i => $word) {
        $subtitle = preg_replace('/' . preg_quote(e($word), '/') . '/iu', '<span class="hl hl--' . ($i % 2 ? 'blue' : 'red') . '">$0</span>', $subtitle, 1);
    }

    $heroImage = Media::url($c['hero_image'], 'assets/front/img/hero-default.svg');
    $photo = Media::url($c['about_photo'], 'assets/front/img/profile-default.svg');
    $wa = preg_replace('/\D/', '', (string) $c['contact_whatsapp']);
    $waLink = $wa ? 'https://wa.me/' . $wa . '?text=' . rawurlencode($c['contact_whatsapp_text']) : null;
    $budgets = array_values(array_filter(array_map('trim', explode(',', (string) $c['contact_budgets']))));
@endphp

@push('head')
    <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
@endpush

@section('content')
    {{-- ================= HERO ================= --}}
    <section class="hero" id="home" aria-labelledby="hero-title">
        <div class="hero__bg" aria-hidden="true"><span class="hero__glow hero__glow--red"></span><span class="hero__glow hero__glow--blue"></span><span class="hero__stripes"></span></div>

        <div class="container hero__grid">
            <div class="hero__copy">
                @if ($stats->isNotEmpty())
                    <ul class="hud-chips" aria-label="Statistik">
                        @foreach ($stats->take(2) as $s)
                            <li class="hud-chip"><b data-pf="stats.{{ $s->id }}.value">{{ $s->value }}</b><span data-pf="stats.{{ $s->id }}.label">{{ $s->label }}</span></li>
                        @endforeach
                    </ul>
                @endif
                <p class="hero__eyebrow" data-pf="hero_eyebrow">{{ $c['hero_eyebrow'] }}</p>
                <h1 class="hero__title" id="hero-title">
                    <span class="hero__line" data-pf="hero_title_1">{{ $c['hero_title_1'] }}</span>
                    <span class="hero__line hero__line--dim" data-pf="hero_title_2">{{ $c['hero_title_2'] }}</span>
                </h1>
                <p class="hero__subtitle" data-pf="hero_subtitle">{!! $subtitle !!}</p>
                <p class="hero__desc" data-pf="hero_description">{{ $c['hero_description'] }}</p>

                <div class="hero__actions">
                    <a href="{{ \App\Support\Portfolio\PortfolioData::href($c['hero_cta_link']) }}" class="btn btn--glow"><span data-pf="hero_cta_label">{{ $c['hero_cta_label'] }}</span> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @if ($c['hero_cta2_label'])
                        <a href="{{ \App\Support\Portfolio\PortfolioData::href($c['hero_cta2_link']) }}" class="btn btn--ghost" data-pf="hero_cta2_label">{{ $c['hero_cta2_label'] }}</a>
                    @endif
                    @if ($c['hero_badge'])
                        <div class="badge-ring">
                            <span class="badge-ring__icon" aria-hidden="true"><i class="fa-solid fa-plus"></i></span>
                            <span class="badge-ring__text" data-pf="hero_badge">{{ $c['hero_badge'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="hero__visual">
                <div class="hero__frame">
                    <img src="{{ $heroImage }}" alt="{{ $c['about_name'] }} — {{ $c['about_role'] }}" class="hero__img" data-pf-img="hero_image" width="720" height="820" fetchpriority="high" decoding="async">
                </div>
            </div>
        </div>

        <div class="container hero__footer">
            @include('frontend.partials.socials', ['class' => 'hero__socials'])

            <div class="hero__cards">
                @foreach ([1, 2] as $n)
                    @if ($c["hero_card{$n}_title"])
                        <div class="hero__card">
                            <h2 data-pf="hero_card{{ $n }}_title">{{ $c["hero_card{$n}_title"] }}</h2>
                            <p data-pf="hero_card{{ $n }}_text">{{ $c["hero_card{$n}_text"] }}</p>
                            <a href="{{ \App\Support\Portfolio\PortfolioData::href($c["hero_card{$n}_link"]) }}" class="btn btn--outline btn--xs" data-pf="hero_card{{ $n }}_label">{{ $c["hero_card{$n}_label"] }}</a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= ABOUT / PROFILE ================= --}}
    <section class="section about" id="about" aria-labelledby="about-title">
        <div class="container">
            <div class="about__top" data-reveal>
                <span class="about__meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $c['about_location'] }} · <time data-clock data-tz="{{ $c['about_timezone'] ?: 'Asia/Jakarta' }}">--:--</time></span>
                <span class="about__brand" data-pf="about_title">{{ $c['about_title'] }}</span>
                @if ($c['about_available'] === '1')
                    <span class="pill"><span class="pill__dot" aria-hidden="true"></span>{{ $c['about_available_text'] }}</span>
                @endif
            </div>

            <div class="about__stage" data-reveal>
                <span class="about__beam" aria-hidden="true"></span>
                <img src="{{ $photo }}" alt="Foto {{ $c['about_name'] }}" class="about__photo" data-pf-img="about_photo" width="520" height="620" loading="lazy" decoding="async">
                <h2 class="about__name" id="about-title" data-pf="about_name">{{ $c['about_name'] }}</h2>
                <p class="about__role" data-pf="about_role">{{ $c['about_role'] }}</p>
            </div>

            <div class="about__grid">
                <div class="hud-panel" data-reveal>
                    <div class="hud-panel__head">
                        <span class="hud-level"><b data-pf="about_level">{{ $c['about_level'] }}</b> <span data-pf="about_level_label">{{ $c['about_level_label'] }}</span></span>
                        <span class="hud-tag">Player Profile</span>
                    </div>
                    <dl class="hud-list">
                        <div><dt>Name</dt><dd>{{ $c['about_name'] }}</dd></div>
                        <div><dt>Occupation</dt><dd>{{ $c['about_role'] }}</dd></div>
                        <div><dt>Location</dt><dd>{{ $c['about_location'] }}</dd></div>
                        <div><dt>Availability</dt><dd class="{{ $c['about_available'] === '1' ? 'is-on' : '' }}">{{ $c['about_available'] === '1' ? $c['about_available_text'] : 'Busy' }}</dd></div>
                    </dl>
                    @if ($c['about_resume'])
                        <a href="{{ Media::url($c['about_resume']) }}" class="btn btn--outline btn--sm" target="_blank" rel="noopener" download>
                            <i class="fa-solid fa-download" aria-hidden="true"></i> {{ $c['about_resume_label'] }}
                        </a>
                    @endif
                </div>

                <div class="about__bio" data-reveal>
                    <p data-pf="about_bio">{{ $c['about_bio'] }}</p>
                    @if ($stats->isNotEmpty())
                        <ul class="stats">
                            @foreach ($stats as $s)
                                <li><b data-pf="stats.{{ $s->id }}.value">{{ $s->value }}</b><span data-pf="stats.{{ $s->id }}.label">{{ $s->label }}</span></li>
                            @endforeach
                        </ul>
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
                <div class="cards">
                    @foreach ($services as $i => $svc)
                        <article class="card hud-corners" data-reveal>
                            <span class="card__index">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="card__icon" aria-hidden="true"><i class="{{ $svc->icon ?: 'fa-solid fa-star' }}" data-pf-icon="services.{{ $svc->id }}.icon"></i></span>
                            <h3 class="card__title" data-pf="services.{{ $svc->id }}.title">{{ $svc->title }}</h3>
                            <p class="card__text" data-pf="services.{{ $svc->id }}.description">{{ $svc->description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= SKILLS ================= --}}
    @if ($skills->isNotEmpty())
        <section class="section section--alt" id="skills" aria-labelledby="skills-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'skills', 'id' => 'skills-title', 'kicker' => 'Skills', 'title' => $c['skills_title'], 'subtitle' => $c['skills_subtitle']])
                <div class="skills">
                    @foreach ($skills as $group => $items)
                        <div class="skills__group hud-corners" data-reveal>
                            <h3 class="skills__title">{{ $group }}</h3>
                            <ul>
                                @foreach ($items as $skill)
                                    <li class="skill" data-pf-level="skills.{{ $skill->id }}.level" style="--lvl: {{ (int) $skill->level }}%">
                                        <span class="skill__name" data-pf="skills.{{ $skill->id }}.name">{{ $skill->name }}</span>
                                        <span class="skill__val" data-pf="skills.{{ $skill->id }}.level">{{ (int) $skill->level }}</span>
                                        <span class="skill__bar" role="progressbar" aria-label="{{ $skill->name }}" aria-valuenow="{{ (int) $skill->level }}" aria-valuemin="0" aria-valuemax="100"><i></i></span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= PROJECTS ================= --}}
    @if ($projects->isNotEmpty())
        <section class="section" id="projects" aria-labelledby="projects-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'projects', 'id' => 'projects-title', 'kicker' => 'Portfolio', 'title' => $c['projects_title'], 'subtitle' => $c['projects_subtitle']])
                @include('frontend.partials.project-grid', ['projects' => $projects, 'categories' => $categories])
                @if ($projectTotal > $projects->count())
                    <div class="center mt-lg">
                        <a href="{{ route('portfolio.projects') }}" class="btn btn--ghost">Lihat semua project ({{ $projectTotal }}) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ================= EXPERIENCE ================= --}}
    @if ($experiences->isNotEmpty())
        <section class="section section--alt" id="experience" aria-labelledby="exp-title">
            <div class="container">
                @include('frontend.partials.heading', ['key' => 'experience', 'id' => 'exp-title', 'kicker' => 'Experience', 'title' => $c['experience_title'], 'subtitle' => $c['experience_subtitle']])
                <ol class="timeline">
                    @foreach ($experiences as $exp)
                        <li class="timeline__item" data-reveal>
                            {{-- Penanda timeline. Tanpa foto = belah ketupat merah seperti semula;
                                 dengan foto = foto yang dibingkai belah ketupat merah yang sama.
                                 Dua-duanya bisa diklik untuk membuka modal detail. --}}
                            <button type="button"
                                    class="timeline__marker{{ $exp->image ? ' timeline__marker--photo' : '' }}"
                                    aria-haspopup="dialog"
                                    data-exp-open="exp-dialog-{{ $exp->id }}"
                                    aria-label="Lihat detail pengalaman: {{ $exp->role }} di {{ $exp->company }}">
                                @if ($exp->image)
                                    <span class="timeline__frame">
                                        <img src="{{ Media::url($exp->image) }}" alt="" loading="lazy" decoding="async">
                                    </span>
                                @endif
                            </button>

                            <span class="timeline__period" data-pf="experiences.{{ $exp->id }}.period">{{ $exp->period }}</span>
                            <div class="timeline__body">
                                <h3 data-pf="experiences.{{ $exp->id }}.role">{{ $exp->role }}</h3>
                                <p class="timeline__company" data-pf="experiences.{{ $exp->id }}.company">{{ $exp->company }}</p>
                                <p data-pf="experiences.{{ $exp->id }}.description">{{ $exp->description }}</p>
                            </div>

                            <dialog class="expdlg" id="exp-dialog-{{ $exp->id }}" aria-labelledby="exp-dialog-{{ $exp->id }}-title">
                                <button type="button" class="expdlg__close" data-exp-close aria-label="Tutup">&times;</button>
                                @if ($exp->image)
                                    <img class="expdlg__img" src="{{ Media::url($exp->image) }}"
                                         alt="Foto {{ $exp->company }}" loading="lazy" decoding="async">
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
                <div class="testimonials" tabindex="0" aria-label="Daftar testimoni, geser untuk melihat lainnya">
                    @foreach ($testimonials as $t)
                        <figure class="testimonial" data-reveal>
                            <i class="fa-solid fa-quote-left testimonial__mark" aria-hidden="true"></i>
                            <blockquote data-pf="testimonials.{{ $t->id }}.quote">{{ $t->quote }}</blockquote>
                            <figcaption>
                                @if ($t->avatar)
                                    <img src="{{ Media::url($t->avatar) }}" alt="" width="44" height="44" loading="lazy">
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
    <section class="section section--alt contact" id="contact" aria-labelledby="contact-title">
        <div class="container contact__grid">
            <div class="contact__info" data-reveal>
                <p class="kicker">Contact</p>
                <h2 class="section-title" id="contact-title" data-pf="contact_title">{{ $c['contact_title'] }}</h2>
                <p class="section-sub" data-pf="contact_subtitle">{{ $c['contact_subtitle'] }}</p>

                <div class="contact__direct">
                    @if ($waLink)
                        <a href="{{ $waLink }}" class="direct direct--wa" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span><b>WhatsApp</b><small>Chat langsung</small></span>
                        </a>
                    @endif
                    @if ($c['contact_linkedin'])
                        <a href="{{ $c['contact_linkedin'] }}" class="direct direct--in" target="_blank" rel="noopener">
                            <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i><span><b>LinkedIn</b><small>Terhubung profesional</small></span>
                        </a>
                    @endif
                    @if ($c['contact_email'])
                        <a href="mailto:{{ $c['contact_email'] }}" class="direct direct--mail">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i><span><b>Email</b><small>{{ $c['contact_email'] }}</small></span>
                        </a>
                    @endif
                </div>
                @include('frontend.partials.socials', ['class' => 'socials'])
            </div>

            @if ($c['contact_form_enabled'] === '1')
                <form method="POST" action="{{ route('portfolio.contact') }}" class="form hud-corners" data-contact-form data-reveal novalidate>
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
                        <input id="f-subject" name="subject" type="text" maxlength="150" value="{{ old('subject') }}" placeholder="Mis. Pembuatan website company profile">
                    </div>
                    <div class="field">
                        <label for="f-message">Ceritakan project Anda *</label>
                        <textarea id="f-message" name="message" rows="5" required minlength="10" maxlength="5000">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn--glow btn--block" data-submit>
                        <span>Kirim Pesan</span> <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                    </button>
                </form>
            @endif
        </div>
    </section>
@endsection
