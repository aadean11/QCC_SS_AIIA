<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Satu AISIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F3F4F6; }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #091E6E 0%, #130998 100%);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* State Sidebar Mengecil */
        .sidebar-collapsed {
            width: 5rem !important; /* Kurang lebih 80px */
        }

        .sidebar-collapsed .menu-text, 
        .sidebar-collapsed .sidebar-header-text,
        .sidebar-collapsed .sidebar-footer {
            display: none;
        }

        .sidebar-collapsed .sidebar-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-collapsed .sidebar-link i {
            margin: 0;
            font-size: 1.25rem;
        }

        .card-gradient-qcc { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
        .card-gradient-ss { background: linear-gradient(135deg, #FBBF24 0%, #D97706 100%); }
        .welcome-banner { background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-reveal { animation: fadeInUp 0.6s ease-out forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }

        .sidebar-link {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #FBBF24;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen flex flex-col overflow-hidden">

    <!-- TOPBAR -->
    <header class="bg-white h-20 px-8 flex justify-between items-center shadow-sm sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <img src="{{ asset('assets/images/logo-aisin.png') }}" alt="Logo" class="h-10">
            <!-- Button Drawer/Toggle -->
            <button id="sidebarToggle" class="text-gray-500 hover:text-[#091E6E] transition-colors p-2 rounded-lg hover:bg-gray-100">
                <i class="fa-solid fa-bars-staggered text-xl"></i>
            </button>
        </div>

        <div class="flex items-center gap-6">
            <div class="hidden md:block text-right border-r pr-6 border-gray-200">
                <p class="font-bold text-[#091E6E] leading-tight">{{ $user->nama }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mt-1">
                    {{ $user->job->name ?? 'Jabatan Tidak Terdaftar' }}
                </p>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" title="Keluar dari sistem"
                    class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-right-from-bracket text-lg"></i>
                </button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-72 sidebar-gradient hidden md:flex flex-col p-4 shadow-2xl relative">
            <div class="flex-1 space-y-4 mt-4 overflow-hidden">
                <p class="sidebar-header-text text-blue-300 text-[10px] font-bold uppercase tracking-[0.2em] mb-4 px-4 whitespace-nowrap">Menu Utama</p>
                
                <nav class="space-y-2">
                    <a href="{{ route('welcome') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl" title="Dashboard">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-solid fa-chart-pie text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Dashboard Overview</span>
                    </a>

                    <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl" title="Monitoring QCC">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-solid fa-crosshairs text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Monitoring QCC</span>
                    </a>

                    <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl" title="Monitoring SS">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-regular fa-lightbulb text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Monitoring SS</span>
                    </a>
                </nav>
            </div>

            <!-- DINAMIS DI SIDEBAR -->
            <div class="sidebar-footer bg-white/5 rounded-2xl p-4 mt-auto">
                <p class="text-blue-200 text-[10px] text-center whitespace-nowrap">© {{ date('Y') }} Satu AISIN</p>
            </div>
        </aside>

        <!-- DASHBOARD CONTENT -->
        <main class="flex-1 p-8 overflow-y-auto custom-scrollbar">
            
            <!-- Breadcrumb -->
            <nav class="flex mb-6 text-sm animate-reveal" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center text-gray-400">
                        <i class="fa-solid fa-house mr-2 text-xs"></i>
                        Home
                    </li>
                    <li>
                        <div class="flex items-center text-[#091E6E] font-semibold">
                            <i class="fa-solid fa-chevron-right text-[10px] mx-2 text-gray-400"></i>
                            Dashboard
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Welcome Banner -->
            <div class="welcome-banner rounded-[2rem] p-8 md:p-12 mb-10 shadow-xl relative overflow-hidden animate-reveal">
                <div class="relative z-10 max-w-2xl">
                    <h4 class="text-blue-200 font-medium mb-2 uppercase tracking-widest text-sm">Overview System</h4>
                    <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight mb-4">
                        Halo, <span class="text-yellow-400">{{ $user->nama }}!</span>
                    </h1>
                    <p class="text-blue-100 text-lg opacity-90">
                        Pantau kemajuan ide dan perubahan melalui sistem Monitoring QCC dan SS secara real-time di sini.
                    </p>
                </div>
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute right-10 -bottom-20 w-60 h-60 bg-blue-400/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 animate-reveal delay-1">
                
                <!-- Card QCC -->
                <div class="glass-card rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500 group relative overflow-hidden border border-white">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-gray-400 font-medium uppercase tracking-wider text-[10px] mb-1">Total Submission</p>
                            <h3 class="text-xl font-bold text-[#091E6E] mb-6">Quality Control Circle</h3>
                            <div class="text-7xl font-extrabold text-[#091E6E] group-hover:scale-105 transition-transform duration-500">
                                {{ $jumlahQcc }}
                            </div>
                        </div>
                        <div class="w-14 h-14 card-gradient-qcc rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-users-gear text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-8 flex items-center text-emerald-600 text-[11px] font-bold">
                        <i class="fa-solid fa-arrow-up mr-2"></i>
                        <span>Update: {{ date('H:i') }} WIB</span>
                    </div>
                    <i class="fa-solid fa-users-gear absolute -right-4 -bottom-4 text-9xl text-gray-50 opacity-50 -z-0"></i>
                </div>

                <!-- Card SS -->
                <div class="glass-card rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500 group relative overflow-hidden border border-white">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-gray-400 font-medium uppercase tracking-wider text-[10px] mb-1">Total Submission</p>
                            <h3 class="text-xl font-bold text-[#091E6E] mb-6">Suggestion System</h3>
                            <div class="text-7xl font-extrabold text-[#091E6E] group-hover:scale-105 transition-transform duration-500">
                                {{ $jumlahSs }}
                            </div>
                        </div>
                        <div class="w-14 h-14 card-gradient-ss rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="fa-regular fa-lightbulb text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-8 flex items-center text-amber-600 text-[11px] font-bold">
                        <i class="fa-solid fa-clock mr-2"></i>
                        <span>Update: {{ date('d M Y') }}</span>
                    </div>
                    <i class="fa-regular fa-lightbulb absolute -right-4 -bottom-4 text-9xl text-gray-50 opacity-50 -z-0"></i>
                </div>

            </div>

            <!-- DINAMIS DI FOOTER -->
            <div class="mt-12 text-center text-gray-400 text-[10px] animate-reveal delay-2">
                Aisin Indonesia Automotive &bull; Quality Management System &bull; {{ date('Y') }}
            </div>

        </main>
    </div>

    <script src="https://cdn.tailwindcss.com"></script> <!-- Jika belum ada -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        // Tampilkan SweetAlert Sukses jika ada session 'success'
        @if(Session::has('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Masuk',
                text: "{{ Session::get('success') }}",
                showConfirmButton: false,
                timer: 2500,
                background: '#ffffff',
                iconColor: '#10B981', // Warna hijau senada dengan desain Anda
                customClass: {
                    title: 'text-[#091E6E] font-bold'
                }
            });
        @endif

        // Toogle Sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-collapsed');
        });
    </script>

</body>
</html>