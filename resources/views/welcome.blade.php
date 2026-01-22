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

        /* SIDEBAR COLLAPSED STATE */
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

        /* ACCORDION ANIMATION (NORMAL MODE) */
        #qccSubmenu { 
            transition: all 0.3s ease-in-out; 
            max-height: 0; 
            overflow: hidden; 
            opacity: 0; 
        }
        #qccSubmenu.show { 
            max-height: 500px; 
            opacity: 1; 
            margin-top: 0.5rem; 
            margin-bottom: 0.5rem; 
        }

        /* FLOATING MENU (MINI MODE) */
        .sidebar-collapsed .group:hover #qccSubmenu {
            display: block !important;
            position: absolute;
            left: 100%;
            top: 0;
            width: 240px; /* Sedikit dilebarkan */
            background: #091E6E;
            border-radius: 0 1rem 1rem 0;
            padding: 1rem;
            box-shadow: 10px 5px 20px rgba(0,0,0,0.3);
            opacity: 1;
            max-height: none;
            z-index: 999;
            margin: 0;
        }

        /* === PERBAIKAN DI SINI === */
        /* Memunculkan kembali teks saat di mode mini (Floating) */
        .sidebar-collapsed .group:hover #qccSubmenu .menu-text {
            display: inline-block !important; /* Paksa muncul */
            opacity: 1 !important;
            color: white;
        }

        .sidebar-collapsed .group:hover #qccSubmenu a {
            padding-left: 0.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        /* ========================= */

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
            <button id="sidebarToggle" class="text-gray-500 hover:text-[#091E6E] p-2 rounded-lg hover:bg-gray-100">
                <i class="fa-solid fa-bars-staggered text-xl"></i>
            </button>
        </div>

        <div class="flex items-center gap-6">
            <div class="hidden md:block text-right border-r pr-6 border-gray-200">
                <p class="font-bold text-[#091E6E] leading-tight">{{ $user->nama }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mt-1">
                    {{ $user->job->name ?? 'User' }}
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
                    <!-- Dashboard Overview -->
                    <a href="{{ route('welcome') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('welcome') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Dashboard Overview</span>
                    </a>

                    <!-- Dropdown QCC -->
                    <div class="relative group">
                        <button onclick="toggleQccDropdown()" class="sidebar-link w-full flex items-center justify-between text-white p-4 rounded-xl focus:outline-none {{ request()->is('qcc*') ? 'bg-white/10' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                    <i class="fa-solid fa-crosshairs"></i>
                                </div>
                                <span class="menu-text font-medium whitespace-nowrap">Monitoring QCC</span>
                            </div>
                            <i id="qccArrow" class="fa-solid fa-chevron-down text-[10px] dropdown-arrow"></i>
                        </button>

                        <div id="qccSubmenu" class="pl-12 space-y-1 {{ request()->is('qcc*') ? 'show' : '' }}">
                            <a href="{{ route('qcc.admin.dashboard') }}" class="flex items-center gap-3 text-blue-100/70 hover:text-white text-xs py-2 {{ request()->is('qcc/admin/dashboard') ? 'text-white font-bold' : '' }}">
                                <i class="fa-solid fa-chart-line text-[12px]"></i>
                                <span class="menu-text whitespace-nowrap">Dashboard Admin</span>
                            </a>
                            <!-- Link Master Steps di Sidebar Submenu QCC -->
                            <a href="{{ route('qcc.admin.master_steps') }}" 
                                class="flex items-center gap-3 text-blue-100/70 hover:text-white text-xs py-2 {{ request()->is('qcc/admin/master-steps*') ? 'text-white font-bold' : '' }}">
                                <i class="fa-solid fa-list-ol text-[12px]"></i>
                                <span class="menu-text">Master Steps</span>
                            </a>
                            <!-- Link Master Periods di Sidebar Submenu QCC -->
                            <a href="{{ route('qcc.admin.master_periods') }}" class="flex items-center gap-3 text-blue-100/70 hover:text-white text-xs py-2">
                                <i class="fa-solid fa-calendar-days text-[12px]"></i>
                                <span class="menu-text whitespace-nowrap">Master Periode</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 text-blue-100/70 hover:text-white text-xs py-2">
                                <i class="fa-solid fa-users-rectangle text-[12px]"></i>
                                <span class="menu-text whitespace-nowrap">Data Circle</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 text-blue-100/70 hover:text-white text-xs py-2">
                                <i class="fa-solid fa-spinner text-[12px]"></i>
                                <span class="menu-text whitespace-nowrap">Monitoring Progress</span>
                            </a>
                        </div>
                    </div>

                    <!-- Monitoring SS -->
                    <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-regular fa-lightbulb"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Monitoring SS</span>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const qccSubmenu = document.getElementById('qccSubmenu');
        const qccArrow = document.getElementById('qccArrow');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-collapsed');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                qccSubmenu.classList.remove('show');
                qccArrow.classList.remove('rotate-180');
            }
        });

        function toggleQccDropdown() {
            if (!sidebar.classList.contains('sidebar-collapsed')) {
                qccSubmenu.classList.toggle('show');
                qccArrow.classList.toggle('rotate-180');
            }
        }
    </script>
</body>
</html>