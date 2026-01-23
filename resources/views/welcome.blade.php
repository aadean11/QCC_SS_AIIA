<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Satu AISIN')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F3F4F6; }
        
        /* SIDEBAR BASE */
        .sidebar-gradient { 
            background: linear-gradient(180deg, #091E6E 0%, #130998 100%); 
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* SIDEBAR COLLAPSED STATE (MINI) */
        .sidebar-collapsed { 
            width: 5rem !important; 
            overflow: visible !important; 
        }

        .sidebar-collapsed .menu-text, 
        .sidebar-collapsed .sidebar-header-text, 
        .sidebar-collapsed .sidebar-footer, 
        .sidebar-collapsed .dropdown-arrow { 
            display: none; 
        }

        .sidebar-collapsed .sidebar-link { 
            justify-content: center; 
            padding-left: 0; 
            padding-right: 0; 
        }

        /* SUBMENU ACCORDION (Mode Normal) */
        .submenu { 
            transition: all 0.3s ease-in-out; 
            max-height: 0; 
            overflow: hidden; 
            opacity: 0; 
        }
        .submenu.show { 
            max-height: 500px; 
            opacity: 1; 
            margin-top: 0.5rem; 
            margin-bottom: 0.5rem; 
        }

        /* FLOATING MENU (Mode Mini - Hover) */
        .sidebar-collapsed .group:hover .submenu {
            display: block !important;
            position: absolute;
            left: 100%;
            top: 0;
            width: 240px;
            background: #091E6E;
            border-radius: 0 1rem 1rem 0;
            padding: 1rem;
            box-shadow: 10px 5px 20px rgba(0,0,0,0.3);
            opacity: 1;
            max-height: none;
            z-index: 999;
            margin: 0;
        }

        /* Munculkan kembali teks menu saat melayang (floating) */
        .sidebar-collapsed .group:hover .submenu .menu-text {
            display: inline-block !important;
            opacity: 1 !important;
            color: white;
        }

        .sidebar-collapsed .group:hover .submenu a {
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }
        
        .sidebar-collapsed .group:hover .submenu a:hover {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
        }

        /* UTILS */
        .dropdown-arrow { transition: transform 0.3s ease; }
        .rotate-180 { transform: rotate(180deg); }
        .sidebar-link { transition: all 0.3s ease; border-left: 4px solid transparent; }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.1); border-left: 4px solid #FBBF24; }
        
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .welcome-banner { background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-reveal { animation: fadeInUp 0.6s ease-out forwards; }
    </style>
</head>
<body class="min-h-screen flex flex-col overflow-hidden">

    <!-- TOPBAR -->
    <header class="bg-white h-20 px-8 flex justify-between items-center shadow-sm sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <img src="{{ asset('assets/images/logo-aisin.png') }}" alt="Logo" class="h-10">
            <button id="sidebarToggle" class="text-gray-500 hover:text-[#091E6E] p-2 rounded-lg hover:bg-gray-100 transition-all">
                <i class="fa-solid fa-bars-staggered text-xl"></i>
            </button>
        </div>

        <div class="flex items-center gap-6">
            <div class="hidden md:block text-right border-r pr-6 border-gray-200">
                <p class="font-bold text-[#091E6E] leading-tight">{{ $user->nama }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mt-1">
                    {{ session('active_role') === 'admin' ? 'Administrator' : ($user->job->name ?? 'Karyawan') }}
                </p>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-right-from-bracket text-lg"></i>
                </button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-72 sidebar-gradient hidden md:flex flex-col p-4 shadow-2xl relative">
            <div class="flex-1 space-y-4 mt-4">
                <p class="sidebar-header-text text-blue-300 text-[10px] font-bold uppercase tracking-[0.2em] mb-4 px-4 whitespace-nowrap">Menu Utama</p>
                
                <nav class="space-y-2">
                    <!-- 1. Menu Beranda -->
                    <a href="{{ route('welcome') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('welcome') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-solid fa-chart-pie text-blue-200 text-sm"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap text-sm">Dashboard Overview</span>
                    </a>

                    <!-- 2. MENU KHUSUS ADMIN -->
                    @if(session('active_role') === 'admin')
                        <div class="relative group">
                            <button onclick="toggleQccDropdown()" class="sidebar-link w-full flex items-center justify-between text-white p-4 rounded-xl focus:outline-none {{ request()->is('qcc/admin*') ? 'bg-white/10' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                        <i class="fa-solid fa-crosshairs text-blue-200 text-sm"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap text-sm">Monitoring QCC</span>
                                </div>
                                <i id="qccArrow" class="fa-solid fa-chevron-down text-[10px] dropdown-arrow"></i>
                            </button>

                            <div id="qccSubmenu" class="submenu pl-12 space-y-1 {{ request()->is('qcc/admin*') ? 'show' : '' }}">
                                <a href="{{ route('qcc.admin.dashboard') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/dashboard') ? 'text-white font-bold' : '' }}">
                                    <span class="menu-text">Dashboard Admin</span>
                                </a>
                                <a href="{{ route('qcc.admin.master_steps') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/master-steps') ? 'text-white font-bold' : '' }}">
                                    <span class="menu-text">Master Steps</span>
                                </a>
                                <a href="{{ route('qcc.admin.master_periods') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/master-periods') ? 'text-white font-bold' : '' }}">
                                    <span class="menu-text">Master Periode</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- 3. MENU KHUSUS KARYAWAN -->
                    @if(session('active_role') === 'employee')
                        <div class="relative group">
                            <button onclick="toggleKaryawanDropdown()" class="sidebar-link w-full flex items-center justify-between text-white p-4 rounded-xl focus:outline-none {{ request()->is('qcc/karyawan*') ? 'bg-white/10' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                        <i class="fa-solid fa-users-gear text-blue-200 text-sm"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap text-sm">Circle QCC Saya</span>
                                </div>
                                <i id="karyawanArrow" class="fa-solid fa-chevron-down text-[10px] dropdown-arrow"></i>
                            </button>

                            <div id="karyawanSubmenu" class="submenu pl-12 space-y-1 {{ request()->is('qcc/karyawan*') ? 'show' : '' }}">
                                <a href="{{ route('qcc.karyawan.my_circle') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/my-circle') ? 'text-white font-bold' : '' }}">
                                    <span class="menu-text">Manajemen Circle</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.progress') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/progress') ? 'text-white font-bold' : '' }}">
                                    <span class="menu-text">Update Progress</span>
                                </a>
                            </div>
                        </div>

                        <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-solid fa-lightbulb text-blue-200 text-sm"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Input SS Baru</span>
                        </a>
                    @endif

                    <!-- 4. Menu Monitoring SS -->
                    <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-regular fa-lightbulb text-blue-200 text-sm"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap text-sm">Monitoring SS</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-footer bg-white/5 rounded-2xl p-4 mt-auto">
                <p class="text-blue-200 text-[10px] text-center whitespace-nowrap">© {{ date('Y') }} Satu AISIN</p>
            </div>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto custom-scrollbar">
            @yield('content')
        </main>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        // Logic Sidebar Toggle (Mini / Full)
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-collapsed');
            
            // Tutup semua accordion saat sidebar mengecil agar tampilan tetap bersih
            if (sidebar.classList.contains('sidebar-collapsed')) {
                document.querySelectorAll('.submenu').forEach(el => el.classList.remove('show'));
                document.querySelectorAll('.dropdown-arrow').forEach(el => el.classList.remove('rotate-180'));
            }
        });

        // Toggle Dropdown Admin QCC
        function toggleQccDropdown() {
            if (!sidebar.classList.contains('sidebar-collapsed')) {
                const submenu = document.getElementById('qccSubmenu');
                const arrow = document.getElementById('qccArrow');
                if (submenu) submenu.classList.toggle('show');
                if (arrow) arrow.classList.toggle('rotate-180');
            }
        }

        // Toggle Dropdown Karyawan QCC
        function toggleKaryawanDropdown() {
            if (!sidebar.classList.contains('sidebar-collapsed')) {
                const submenu = document.getElementById('karyawanSubmenu');
                const arrow = document.getElementById('karyawanArrow');
                if (submenu) submenu.classList.toggle('show');
                if (arrow) arrow.classList.toggle('rotate-180');
            }
        }

        // Alert sukses login
        @if(Session::has('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ Session::get('success') }}",
                showConfirmButton: false,
                timer: 2000,
                background: '#ffffff',
                iconColor: '#10B981',
                customClass: { title: 'text-[#091E6E] font-bold' }
            });
        @endif
    </script>
</body>
</html>