<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Satu AISIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        .bg-custom-gradient { background: linear-gradient(180deg, #FFFFFF 0%, #130998 100%); }
        .btn-gradient { background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%); transition: all 0.3s ease; }
        .sidebar-gradient { background: linear-gradient(180deg, #091E6E 0%, #130998 100%); }
        
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
        .animate-float { animation: float 4s ease-in-out infinite; }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate-reveal { animation: fadeInUp 0.8s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <header class="bg-white h-20 px-10 shadow-md flex items-center shrink-0 relative z-50">
        <img src="{{ asset('assets/images/logo-aisin.png') }}" alt="Satu AISIN Logo" class="h-12">
    </header>

    <main class="flex-1 bg-custom-gradient flex items-center justify-center p-6">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-around gap-12">
            
            <div class="hidden md:flex flex-col items-center text-white max-w-sm">
                <div class="animate-reveal"><img src="{{ asset('assets/images/In progress-amico.png') }}" class="w-full mb-6 animate-float"></div>
                <h1 class="text-1xl font-semibold text-center leading-relaxed">Satu ide, Satu Perubahan, Satu Kemajuan!</h1>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl p-10 max-w-md w-full animate-reveal relative">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-[#162299] mb-2">Selamat Datang</h2>
                    <p class="text-gray-400 text-sm font-medium">Masukkan kredensial Anda</p>
                </div>

                <form id="tempLoginForm">
                    @csrf
                    <div class="mb-5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-2 mb-1 block">Username (Nama)</label>
                        <input type="text" id="username" placeholder="Nama Lengkap" required
                            class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] text-gray-700 bg-gray-50/50">
                    </div>

                    <div class="mb-8 relative">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-2 mb-1 block">Password (NPK)</label>
                        <input type="password" id="password" placeholder="NPK Anda" required
                            class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] text-gray-700 bg-gray-50/50 pr-12">
                        <div id="togglePassword" class="absolute bottom-4 right-4 cursor-pointer text-gray-300"><i id="eyeIcon" class="fa-solid fa-eye"></i></div>
                    </div>

                    <button type="submit" id="btnCheck" class="w-full btn-gradient flex items-center justify-center gap-3
                                text-white font-black py-4 rounded-2xl shadow-lg
                                active:scale-95 uppercase tracking-widest text-sm">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                        </button>
                </form>
            </div>
        </div>
    </main>

    <!-- MODAL PILIH AKSES (SESUAI DESAIN MASTER ANDA) -->
    <div id="modalRole" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
                <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold">Pilih Akses Masuk</h3>
                    <button onclick="closeModal()" class="text-white/70 hover:text-white text-2xl">&times;</button>
                </div>
                <div class="p-8 space-y-6">
                    <p class="text-center text-gray-500 text-sm">Akun Anda terdeteksi memiliki akses Administrator. Silakan pilih mode masuk:</p>
                    
                    <div class="grid gap-4">
                        <button onclick="finalSubmit('admin')" class="flex items-center gap-5 p-5 border-2 border-gray-100 rounded-3xl hover:border-[#130998] hover:bg-indigo-50 transition-all text-left group">
                            <div class="w-12 h-12 bg-indigo-100 text-[#130998] rounded-xl flex items-center justify-center text-xl group-hover:bg-[#130998] group-hover:text-white">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#130998]">Masuk sebagai Admin</h4>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Monitoring & Master Data</p>
                            </div>
                        </button>

                        <button onclick="finalSubmit('employee')" class="flex items-center gap-5 p-5 border-2 border-gray-100 rounded-3xl hover:border-[#091E6E] hover:bg-blue-50 transition-all text-left group">
                            <div class="w-12 h-12 bg-blue-100 text-[#091E6E] rounded-xl flex items-center justify-center text-xl group-hover:bg-[#091E6E] group-hover:text-white">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#091E6E]">Masuk sebagai Karyawan</h4>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">QCC & Suggestion System</p>
                            </div>
                        </button>
                    </div>

                    <button onclick="closeModal()" class="w-full py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-gray-200 transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Real Form -->
    <form id="realLoginForm" action="{{ route('login') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="username" id="real_user">
        <input type="hidden" name="password" id="real_pass">
        <input type="hidden" name="login_type" id="real_type">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function closeModal() { document.getElementById('modalRole').classList.add('hidden'); }

        document.getElementById('tempLoginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnCheck');
            const user = document.getElementById('username').value;
            const pass = document.getElementById('password').value;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Checking...';

            try {
                const response = await fetch("{{ route('check.role') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ username: user, password: pass })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    if (data.is_admin) {
                        // Munculkan Modal jika dia Admin
                        document.getElementById('modalRole').classList.remove('hidden');
                    } else {
                        // Langsung masuk sebagai Karyawan jika bukan Admin
                        finalSubmit('employee');
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Login Gagal', text: data.message, confirmButtonColor: '#091E6E' });
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.disabled = false;
                btn.innerText = 'Masuk Ke Sistem';
            }
        });

        function finalSubmit(type) {
            document.getElementById('real_user').value = document.getElementById('username').value;
            document.getElementById('real_pass').value = document.getElementById('password').value;
            document.getElementById('real_type').value = type;
            document.getElementById('realLoginForm').submit();
        }

        // Toggle Password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        @if(Session::has('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ Session::get('error') }}", confirmButtonColor: '#091E6E' });
        @endif
    </script>
</body>
</html>