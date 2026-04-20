@extends('welcome')

@section('title', 'Review KDP')

@section('content')
<div class="animate-reveal pb-20 max-w-2xl mx-auto">
    <div class="mb-6"><a href="{{ route('ss.admin.show', $submission->id) }}" class="text-gray-500"><i class="fa-regular fa-arrow-left"></i> Kembali</a></div>
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-[#091E6E] mb-4">Review KDP</h2>
        <form action="{{ route('ss.admin.review_kdp.store', $submission->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Tindakan</label>
                <select name="action" class="w-full border rounded-xl p-3" required>
                    <option value="approved">✅ Approve</option>
                    <option value="rejected">❌ Reject</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Catatan KDP</label>
                <textarea name="kdp_notes" rows="3" class="w-full border rounded-xl p-3"></textarea>
            </div>
            <button type="submit" class="bg-orange-600 text-white px-5 py-2 rounded-xl">Kirim Review</button>
        </form>
    </div>
</div>
@endsection