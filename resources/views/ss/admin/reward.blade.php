@extends('welcome')

@section('title', 'Beri Reward')

@section('content')
<div class="animate-reveal pb-20 max-w-2xl mx-auto">
    <div class="mb-6"><a href="{{ route('ss.admin.show', $submission->id) }}" class="text-gray-500"><i class="fa-regular fa-arrow-left"></i> Kembali</a></div>
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-[#091E6E] mb-4">Beri Reward</h2>
        <form action="{{ route('ss.admin.reward.store', $submission->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Nominal Reward (Rp)</label>
                <input type="number" name="reward_amount" min="0" step="1000" class="w-full border rounded-xl p-3" required placeholder="Contoh: 50000">
            </div>
            <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded-xl">Bayar Reward</button>
        </form>
    </div>
</div>
@endsection