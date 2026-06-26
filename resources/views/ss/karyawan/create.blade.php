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

                    {{--
                    Form detail SS lama dinonaktifkan sementara.
                    Untuk mengaktifkan lagi, buka komentar blok ini dan aktifkan kembali validasi detail di KaryawanSsController::store().
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Ide <span class="text-red-500">*</span></label>
                            <input type="text" name="idea_title" required value="{{ old('idea_title') }}"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[#091E6E] text-sm focus:ring-2 focus:ring-[#091E6E] outline-none"
                                placeholder="Masukkan nama ide SS">
                            @error('idea_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Diterapkan Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="implemented_date" required value="{{ old('implemented_date', now()->format('Y-m-d')) }}"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[#091E6E] text-sm focus:ring-2 focus:ring-[#091E6E] outline-none">
                            @error('implemented_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Ide <span class="text-red-500">*</span></label>
                            <input type="text" name="idea_location" required value="{{ old('idea_location') }}"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[#091E6E] text-sm focus:ring-2 focus:ring-[#091E6E] outline-none"
                                placeholder="Contoh: Line A / Area Produksi">
                            @error('idea_location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status SS / Kenyataan Ide <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 bg-gray-50 border border-gray-200 rounded-xl p-3">
                                @foreach($implementationStatuses as $key => $label)
                                    <label class="flex items-center gap-2 text-xs md:text-sm font-bold text-[#091E6E]">
                                        <input type="radio" name="implementation_status" value="{{ $key }}" @checked(old('implementation_status', 'sudah_dilaksanakan') === $key) class="border-gray-300">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            @error('implementation_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Ide <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 bg-gray-50 border border-gray-200 rounded-xl p-3">
                                @foreach($ideaTypes as $key => $label)
                                    <label class="flex items-center gap-2 text-xs md:text-sm font-bold text-[#091E6E]">
                                        <input type="checkbox" name="idea_types[]" value="{{ $key }}" @checked(in_array($key, old('idea_types', []))) class="rounded border-gray-300">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            @error('idea_types') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Keadaan Sebelumnya <span class="text-red-500">*</span></label>
                            <textarea name="before_condition" rows="6" required class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Uraikan kondisi sebelum perbaikan...">{{ old('before_condition') }}</textarea>
                            @error('before_condition') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Penyebab & Action <span class="text-red-500">*</span></label>
                            <textarea name="cause" rows="3" required class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm mb-3" placeholder="Penyebab...">{{ old('cause') }}</textarea>
                            <textarea name="action" rows="3" required class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Action/perbaikan...">{{ old('action') }}</textarea>
                            @error('cause') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            @error('action') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Hasil <span class="text-red-500">*</span></label>
                            <textarea name="result" rows="6" required class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Uraikan hasil sesudah perbaikan...">{{ old('result') }}</textarea>
                            @error('result') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Standardisasi</label>
                            <textarea name="standardization" rows="4" class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Standardisasi setelah perbaikan...">{{ old('standardization') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Manfaat Ide <span class="text-red-500">*</span></label>
                            <textarea name="benefit" rows="3" required class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm mb-3" placeholder="Uraikan manfaat secara singkat dan jelas...">{{ old('benefit') }}</textarea>
                            <input type="number" name="benefit_amount" min="0" step="1000" value="{{ old('benefit_amount') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[#091E6E] text-sm focus:ring-2 focus:ring-[#091E6E] outline-none" placeholder="Estimasi manfaat Rp (opsional)">
                            @error('benefit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    --}}

                    @if($user->isLdr())
                    <div class="mb-6">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <label class="block text-gray-700 text-sm font-bold">Penilaian Leader <span class="text-red-500">*</span></label>
                            <span class="text-xs font-bold text-[#091E6E]">Total: <span id="ldrScoreTotal">0</span></span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($criteria as $key => $label)
                                <div class="flex items-center justify-between gap-3 bg-gray-50/50 border border-gray-200 rounded-xl px-3 py-2">
                                    <label class="text-xs md:text-sm font-semibold text-gray-700">
                                        {{ $loop->iteration }}. {{ $label }}
                                        <span class="block text-[9px] text-gray-400">Max {{ $criteriaMaxScores[$key] ?? 0 }}</span>
                                    </label>
                                    <input type="number" name="scores[{{ $key }}]" min="0" max="{{ $criteriaMaxScores[$key] ?? 0 }}" value="{{ old('scores.' . $key, 0) }}" class="score-input w-20 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-[#091E6E] text-center outline-none focus:ring-2 focus:ring-[#091E6E]">
                                </div>
                                @error('scores.' . $key) <p class="text-red-500 text-xs -mt-2">{{ $message }}</p> @enderror
                            @endforeach
                        </div>
                        @error('scores') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">File PDF Ide SS <span class="text-red-500">*</span></label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#091E6E] transition-all bg-gray-50/30">
                            <input type="file" name="file" id="ssFileInput" accept=".pdf" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#091E6E] file:text-white hover:file:bg-[#130998] file:cursor-pointer">
                            <p class="text-xs text-gray-400 mt-3">Maksimal 5MB, format PDF</p>
                        </div>
                        @error('file') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (opsional)</label>
                        <textarea name="notes" rows="3" maxlength="20" class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Catatan penilaian atau deskripsi singkat...">{{ old('notes') }}</textarea>
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
@error('file')
<script>Swal.fire({ icon: 'error', title: 'File tidak valid', text: @json($message), confirmButtonColor: '#091E6E' });</script>
@enderror
<script>
(function() {
    const isLdr = @json($user->isLdr());
    const hasOpr = @json($oprList->isNotEmpty());
    const typeInput = document.getElementById('submission_type');
    const panelOpr = document.getElementById('panelOpr');
    const panelSelf = document.getElementById('panelSelf');
    const selectOpr = document.getElementById('employee_npk');
    const submitLabel = document.getElementById('submitLabel');
    const fileInput = document.getElementById('ssFileInput');
    const maxFileSize = 5 * 1024 * 1024;

    function updateLdrScoreTotal() {
        const totalEl = document.getElementById('ldrScoreTotal');
        if (!totalEl) return;

        const total = [...document.querySelectorAll('.score-input')].reduce((sum, input) => {
            const max = Number(input.max || 0);
            const value = Math.max(0, Math.min(Number(input.value || 0), max));
            input.value = value;
            return sum + value;
        }, 0);

        totalEl.textContent = total;
    }

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

    document.querySelectorAll('.score-input').forEach(input => input.addEventListener('input', updateLdrScoreTotal));
    updateLdrScoreTotal();

    document.getElementById('formSsSubmit')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const file = fileInput?.files?.[0];

        if (file && file.size > maxFileSize) {
            Swal.fire({
                icon: 'error',
                title: 'File terlalu besar',
                text: 'Ukuran file PDF maksimal 5MB. Silakan pilih file yang lebih kecil.',
                confirmButtonColor: '#091E6E'
            });
            fileInput.value = '';
            return;
        }

        const t = typeInput?.value === 'opr' ? 'operator (OPR)' : 'Anda sendiri';
        Swal.fire({
            title: 'Ajukan SS?',
            text: 'Pengajuan untuk ' + t + ' beserta nilai Leader akan dikirim ke review SPV.',
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
