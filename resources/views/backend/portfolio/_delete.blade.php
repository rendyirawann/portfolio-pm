<form method="POST" action="{{ $action }}" class="d-inline" data-confirm-delete>
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Hapus" aria-label="Hapus">
        <i class="ki-outline ki-trash fs-5"></i>
    </button>
</form>

@once
    @push('scripts')
        <script>
            document.addEventListener('submit', function (e) {
                var form = e.target.closest('[data-confirm-delete]');
                if (!form || form.dataset.confirmed) return;
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus data ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light' }
                }).then(function (r) {
                    if (r.isConfirmed) { form.dataset.confirmed = '1'; form.submit(); }
                });
            });
        </script>
    @endpush
@endonce
