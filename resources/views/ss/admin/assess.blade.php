@extends('welcome')

@section('title', 'Beri Nilai SS')

@section('content')
<div class="animate-reveal pb-20 max-w-2xl mx-auto">
    <div class="mb-6"><a href="{{ route('ss.admin.show', $submission->id) }}" class="text-gray-500"><i class="fa-regular fa-arrow-left"></i> Kembali</a></div>
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-[#091E6E] mb-4">Beri Nilai untuk SS</h2>
        <form action="{{ route('ss.admin.assess.store', $submission->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Score (0-100)</label>
                <input type="number" name="score" min="0" max="100" required class="w-full border rounded-xl p-3">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Catatan (opsional)</label>
                <textarea name="notes" rows="3" class="w-full border rounded-xl p-3"></textarea>
            </div>
            <button type="submit" class="bg-[#091E6E] text-white px-5 py-2 rounded-xl">Simpan Nilai</button>
        </form>
    </div>
</div>
@endsection