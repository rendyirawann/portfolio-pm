{{-- Text and icons are edited in Portfolio › Konten Halaman › Navbar & Footer Admin;
     the icons come from Portfolio › Sosial Media. --}}
<footer class="app-footer" id="kt_footer">
    <div class="container-fluid">
        <div class="app-footer__inner">
            <div class="text-muted fs-7">
                @if ($chrome['footer_link'])
                    <a href="{{ $chrome['footer_link'] }}" target="_blank" rel="noopener noreferrer"
                        class="text-gray-800 text-hover-primary fw-semibold">© {{ now()->year }} <span data-pf="admin_footer_text">{{ $chrome['footer_name'] }}</span></a> · <span data-pf="admin_brand_tagline">{{ $chrome['tagline'] }}</span>
                @else
                    © {{ now()->year }} <span data-pf="admin_footer_text">{{ $chrome['footer_name'] }}</span> · <span data-pf="admin_brand_tagline">{{ $chrome['tagline'] }}</span>
                @endif
            </div>

            @if ($chrome['socials']->isNotEmpty())
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
                            integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
                            crossorigin="anonymous" referrerpolicy="no-referrer" />
                <nav class="app-footer__social" aria-label="Sosial media">
                    @foreach ($chrome['socials'] as $s)
                        <a href="{{ $s->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $s->display_label }}" title="{{ $s->display_label }}">
                            <i class="{{ $s->icon }}" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </div>
</footer>
