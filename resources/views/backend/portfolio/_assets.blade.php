{{-- Loaded only by Portfolio admin screens that need icons / the upload zone. --}}
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/css/portfolio-admin.css') }}?v=1" />
@endpush
@if (! empty($uploader))
    @push('scripts')
        <script src="{{ asset('assets/js/custom/portfolio/uploader.js') }}?v=1" defer></script>
    @endpush
@endif
