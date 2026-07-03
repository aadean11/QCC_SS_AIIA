<style>
    .custom-pagination nav { display: flex; gap: 4px; align-items: center; justify-content: center; }
    .custom-pagination span[aria-current="page"] span { background-color: #091E6E !important; color: white !important; border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 800; }
    .custom-pagination a, .custom-pagination span { border-radius: 8px; padding: 6px 12px; font-size: 11px; border: 1px solid #edf2f7; color: #64748b; transition: all 0.2s; }
    .custom-pagination a:hover { background-color: #f8fafc; border-color: #091E6E; color: #091E6E; }
</style>
<script>
    function openModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id)?.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmDeleteMaster(id, title, html) {
        Swal.fire({
            title: title,
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        });
    }

    function bindMasterFormConfirm(formId, title, text, confirmColor = '#091E6E', confirmText = 'Ya, Simpan!') {
        document.getElementById(formId)?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    }

    @if(Session::has('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: @json(Session::get('success')), timer: 2500, showConfirmButton: false, background: '#ffffff', iconColor: '#10B981', customClass: { title: 'text-[#091E6E] font-bold' } });
    @endif
    @if(Session::has('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: @json(Session::get('error')), confirmButtonColor: '#091E6E', background: '#ffffff', iconColor: '#EF4444', customClass: { title: 'text-[#091E6E] font-bold' } });
    @endif
    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Validasi Gagal!', text: @json($errors->first()), confirmButtonColor: '#091E6E', background: '#ffffff', iconColor: '#EF4444', customClass: { title: 'text-[#091E6E] font-bold' } });
    @endif
</script>
