<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SIGITA</title>
    <link rel="icon" type="image/png" href="{{ asset('SIGITA_LOGO.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-custom-gradient { background: linear-gradient(180deg, #FFFFFF 0%, #130998 100%); }
        .btn-gradient { background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%); }
    </style>
</head>
<body class="min-h-screen bg-custom-gradient flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-[#091E6E]">Reset Password</h1>
            <p class="text-gray-400 text-sm mt-2">Buat password baru untuk akun SIGITA Anda.</p>
        </div>

        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 text-red-600 px-4 py-3 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 text-red-600 px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-2 mb-1 block">Email</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required
                    class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] text-sm">
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-2 mb-1 block">Password Baru</label>
                <input type="password" name="password" required
                    class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] text-sm">
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-2 mb-1 block">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] text-sm">
            </div>

            <button type="submit" class="w-full btn-gradient py-4 text-white rounded-xl font-black shadow-lg uppercase tracking-widest text-xs">
                <i class="fa-solid fa-key mr-2"></i> Simpan Password Baru
            </button>
        </form>

        <a href="{{ route('login') }}" class="block text-center mt-6 text-xs font-bold text-blue-600 hover:text-[#091E6E]">
            Kembali ke Login
        </a>
    </div>
</body>
</html>
