<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Satu AISIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        .bg-custom-gradient {
            background: linear-gradient(180deg, #FFFFFF 0%, #130998 100%);
        }
        .btn-gradient {
            background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%);
            transition: all 0.3s ease;
        }

        /* --- ANIMASI --- */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-reveal { opacity: 0; animation: fadeInUp 0.8s ease-out forwards; }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }

        .btn-gradient:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(16, 53, 209, 0.4);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Header / Logo -->
    <header class="bg-white h-20 px-10 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.3)] flex items-center shrink-0 relative z-50">
        <img src="{{ asset('assets/images/logo-aisin.png') }}" alt="Satu AISIN Logo" class="h-12">
    </header>

    <!-- Main Content -->
    <main class="flex-1 bg-custom-gradient flex items-center justify-center p-6">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-around gap-12">
            
            <!-- Left Side: Illustration & Text -->
            <div class="hidden md:flex flex-col items-center text-white max-w-sm">
                <div class="animate-reveal delay-1">
                    <img src="{{ asset('assets/images/In progress-amico.png') }}" alt="Illustration" class="w-full mb-6 animate-float">
                </div>
                <h1 class="text-1xl font-semibold text-center animate-reveal delay-2 leading-relaxed">
                    Satu ide, Satu Perubahan, Satu Kemajuan!
                </h1>
            </div>

            <!-- Right Side: Login Card -->
            <div class="bg-white rounded-3xl shadow-2xl p-10 max-w-sm w-full animate-reveal delay-3">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-[#162299] mb-2 tracking-tight">Selamat Datang</h2>
                    <p class="text-gray-500 text-sm font-medium">Masukkan Username dan Password Anda!</p>
                </div>

                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf
                    
                    <!-- Username (Nama) -->
                    <div class="mb-6 group">
                        <!-- 'value="{{ old('username') }}"' penting agar saat gagal nama tidak hilang -->
                        <input type="text" name="username" placeholder="Nama Lengkap" 
                            value="{{ old('username') }}"
                            class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-gray-50/50 transition-all duration-300">
                    </div>

                    <!-- Password (NPK) -->
                    <div class="mb-8 relative group">
                        <input type="password" id="password" name="password" placeholder="Nomor Pokok Karyawan (NPK)" 
                            class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-gray-50/50 transition-all duration-300 pr-12">
                        
                        <!-- Toggle Password Icon -->
                        <div id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-gray-400 hover:text-blue-600 transition-colors">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        class="w-full btn-gradient text-white font-bold py-4 rounded-xl flex items-center justify-center gap-3 active:scale-95 uppercase tracking-widest shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        LOGIN
                    </button>
                </form>
            </div>

        </div>
    </main>

    <!-- JS SECTION -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Validasi Password Visibility (Toggle Mata)
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('text-blue-600');
        });

        // 2. SweetAlert jika Username/NPK Salah (Kredensial)
        @if(Session::has('error'))
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: "{{ Session::get('error') }}",
                confirmButtonColor: '#091E6E',
                timer: 4000
            });
        @endif

        // 3. SweetAlert jika form tidak diisi (Validation Error dari Controller)
        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                // Kita tampilkan pesan error yang spesifik dari Controller
                text: "{{ $errors->first() }}", 
                confirmButtonColor: '#1035D1',
                confirmButtonText: 'Coba Lagi'
            });
        @endif

        // 4. SweetAlert Sukses Login
        @if(Session::has('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Masuk',
                text: "{{ Session::get('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>
</body>
</html>