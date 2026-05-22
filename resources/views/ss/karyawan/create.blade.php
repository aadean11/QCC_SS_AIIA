@extends('welcome')

@section('title', 'Pengajuan SS')

@section('content')
<div class="animate-reveal pb-20">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Suggestion System', 'icon' => 'fa-solid fa-lightbulb'],
        ['label' => 'Daftar SS', 'url' => route('ss.karyawan.index')],
        'Pengajuan & Penilaian',
    ]])

        <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
            <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6">
                <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Pengajuan SS & Penilaian</h2>
                <p class="text-blue-200 text-[10px] md:text-xs mt-1">
                    {{ $user->job->name ?? $user->occupation }}
                    @if($myDept) · Dept: <strong>{{ $myDept->name }}</strong> @endif
                </p>
            </div>

            <div class="p-4 md:p-8">
                @if($user->isLdr() && $oprList->isEmpty())
                    <div class="mb-6 text-center py-4 text-amber-700 bg-amber-50 rounded-xl border border-amber-200 text-sm">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Belum ada OPR di departemen — Anda tetap dapat mengajukan <strong>SS Saya</strong>.
                    </div>
                @endif

                <form action="{{ route('ss.karyawan.store') }}" method="POST" enctype="multipart/form-data" id="formSsSubmit">
                    @csrf
                    <input type="hidden" name="submission_type" id="submission_type" value="{{ old('submission_type', $user->isLdr() && $oprList->isNotEmpty() ? 'opr' : 'self') }}">

                    @if($user->isLdr())
                    <div class="flex flex-wrap gap-2 mb-6 p-1 bg-gray-100 rounded-xl">
                        <button type="button" data-tab="opr" id="tabOpr"
                            class="flex-1 min-w-[140px] py-2.5 px-4 rounded-lg text-xs font-bold uppercase tracking-wide transition-all tab-btn">
                            <i class="fa-solid fa-users mr-1"></i> Untuk Operator (OPR)
                        </button>
                        <button type="button" data-tab="self" id="tabSelf"
                            class="flex-1 min-w-[140px] py-2.5 px-4 rounded-lg text-xs font-bold uppercase tracking-wide transition-all tab-btn">
                            <i class="fa-solid fa-user mr-1"></i> SS Saya
                        </button>
                    </div>
                    @else
                    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-sm font-bold text-[#091E6E]">Pengajuan SS Pribadi</p>
                        <p class="text-xs text-gray-600 mt-1">Atas nama: <strong>{{ $user->nama }}</strong> ({{ $user->npk }})</p>
                    </div>
                    @endif

                    {{-- Panel: Untuk OPR (hanya LDR) --}}
                    <div id="panelOpr" class="{{ $user->isLdr() ? '' : 'hidden' }}">
                        @if($user->isLdr() && $oprList->isNotEmpty())
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Operator (OPR) <span class="text-red-500">*</span></label>
                            <select name="employee_npk" id="employee_npk"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[#091E6E] text-sm focus:ring-2 focus:ring-[#091E6E] outline-none">
                                <option value="">-- Pilih nama OPR --</option>
                                @foreach($oprList as $opr)
                                    <option value="{{ $opr->npk }}" @selected(old('employee_npk') == $opr->npk)>
                                        {{ $opr->nama }} ({{ $opr->npk }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">Hanya Leader yang dapat mengajukan atas nama OPR.</p>
                            @error('employee_npk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif
                    </div>

                    {{-- Panel: SS Saya --}}
                    <div id="panelSelf" class="{{ $user->isLdr() ? 'hidden' : '' }}">
                        <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Pengaju</span>
                            <p class="text-sm font-bold text-[#091E6E] mt-1">{{ $user->nama }} ({{ $user->npk }})</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nilai / Score <span class="text-red-500">*</span></label>
                        <input type="number" name="score" min="0" step="any" required value="{{ old('score') }}"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[#091E6E] text-sm focus:ring-2 focus:ring-[#091E6E] outline-none"
                            placeholder="Masukkan nilai">
                        <p class="text-[10px] text-gray-400 mt-1">Nilai tidak dibatasi maksimum</p>
                        @error('score') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">File PDF Ide SS <span class="text-red-500">*</span></label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#091E6E] transition-all bg-gray-50/30">
                            <input type="file" name="file" accept=".pdf" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#091E6E] file:text-white hover:file:bg-[#130998] file:cursor-pointer">
                            <p class="text-xs text-gray-400 mt-3">Maksimal 5MB, format PDF</p>
                        </div>
                        @error('file') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (opsional)</label>
                        <textarea name="notes" rows="3" class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Catatan penilaian atau deskripsi singkat...">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('ss.karyawan.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium text-sm hover:bg-gray-50">Batal</a>
                        <button type="submit" class="px-5 py-2.5 bg-[#091E6E] text-white rounded-xl font-bold text-sm hover:bg-[#130998] shadow-md flex items-center gap-2">
                            <i class="fa-regular fa-paper-plane"></i> <span id="submitLabel">Ajukan SS</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection

@push('scripts')
<style>
    .tab-btn.active { background: #091E6E; color: #fff; box-shadow: 0 4px 12px rgba(9,30,110,.25); }
    .tab-btn:not(.active) { background: transparent; color: #64748b; }
</style>
@if(Session::has('error'))
<script>Swal.fire({ icon: 'error', title: 'Gagal', text: @json(Session::get('error')), confirmButtonColor: '#091E6E' });</script>
@endif
<script>
(function() {
    const isLdr = @json($user->isLdr());
    const hasOpr = @json($oprList->isNotEmpty());
    const typeInput = document.getElementById('submission_type');
    const panelOpr = document.getElementById('panelOpr');
    const panelSelf = document.getElementById('panelSelf');
    const selectOpr = document.getElementById('employee_npk');
    const submitLabel = document.getElementById('submitLabel');

    function setTab(type) {
        if (!isLdr) return;
        typeInput.value = type;
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === type);
        });
        if (type === 'opr' && hasOpr) {
            panelOpr.classList.remove('hidden');
            panelSelf.classList.add('hidden');
            if (selectOpr) selectOpr.required = true;
            submitLabel.textContent = 'Ajukan untuk OPR';
        } else {
            panelOpr.classList.add('hidden');
            panelSelf.classList.remove('hidden');
            if (selectOpr) { selectOpr.required = false; selectOpr.value = ''; }
            submitLabel.textContent = 'Ajukan SS Saya';
        }
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => setTab(btn.dataset.tab));
    });

    if (isLdr) {
        const initial = typeInput.value || (hasOpr ? 'opr' : 'self');
        if (initial === 'opr' && !hasOpr) setTab('self');
        else setTab(initial);
    }

    document.getElementById('formSsSubmit')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const t = typeInput?.value === 'opr' ? 'operator (OPR)' : 'Anda sendiri';
        Swal.fire({
            title: 'Ajukan SS?',
            text: 'Pengajuan untuk ' + t + ' akan dikirim ke review SPV.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#091E6E',
            confirmButtonText: 'Ya, Ajukan',
            cancelButtonText: 'Batal'
        }).then((r) => { if (r.isConfirmed) this.submit(); });
    });
})();
</script>
@endpush
