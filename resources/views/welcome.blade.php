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
        /* ... (semua style tetap sama seperti sebelumnya) ... */
        body { font-family: 'Poppins', sans-serif; background-color: #F3F4F6; }
        html, body { height: 100%; overflow: hidden; }
        .sidebar-gradient { background: linear-gradient(180deg, #091E6E 0%, #130998 100%); transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow-y: auto; overflow-x: hidden; }
        .sidebar-collapsed { width: 5rem !important; overflow: visible !important; }
        .sidebar-collapsed .badge-number { display: none !important; }
        .sidebar-collapsed .sidebar-link .relative { margin-right: 0 !important; }
        .sidebar-collapsed .sidebar-link .relative span.bg-red-500 { top: -2px !important; right: -2px !important; width: 10px !important; height: 10px !important; }
        .sidebar-collapsed .menu-text, .sidebar-collapsed .sidebar-header-text, .sidebar-collapsed .sidebar-footer, .sidebar-collapsed .dropdown-arrow { display: none; }
        .sidebar-collapsed .sidebar-link { justify-content: center; padding-left: 0; padding-right: 0; }
        .submenu { transition: all 0.3s ease-in-out; max-height: 0; overflow: hidden; opacity: 0; }
        .submenu.show { max-height: 500px; opacity: 1; margin-top: 0.5rem; margin-bottom: 0.5rem; }
        .submenu { position: relative; padding-left: 2.25rem !important; margin-top: 0.5rem; }
        .submenu::before { content: ""; position: absolute; left: 20px; top: 6px; bottom: 6px; width: 1px; background-color: rgba(255,255,255,0.15); }
        .submenu a { position: relative; display: block; padding: 0.5rem 0.75rem 0.5rem 1.25rem !important; border-radius: 8px; transition: all 0.2s ease; }
        .submenu a::before { content: ""; position: absolute; left: -14px; top: 50%; width: 14px; height: 1px; background-color: rgba(255,255,255,0.15); transform: translateY(-50%); }
        .submenu a::after { content: ""; position: absolute; left: -18px; top: 50%; width: 4px; height: 4px; background-color: rgba(255,255,255,0.4); border-radius: 50%; transform: translateY(-50%); }
        .submenu a:hover { background-color: rgba(255,255,255,0.07); transform: translateX(4px); color: #ffffff !important; }
        .submenu a.font-bold { background-color: rgba(255,255,255,0.1); color: #ffffff !important; }
        .dropdown-arrow { transition: transform 0.3s ease; }
        .rotate-180 { transform: rotate(180deg); }
        .sidebar-link { transition: all 0.3s ease; border-left: 4px solid transparent; }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.1); border-left: 4px solid #FBBF24; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #707070; border-radius: 10px;}
        .welcome-banner { background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-reveal { animation: fadeInUp 0.6s ease-out forwards; }
        .sidebar-collapsed .group { position: relative; }
        .floating-submenu { position: fixed !important; display: block !important; visibility: hidden; opacity: 0; width: 240px; background: #091E6E; border-radius: 0 1rem 1rem 0; padding: 1rem; box-shadow: 15px 5px 30px rgba(0, 0, 0, 0.4); z-index: 99999; border-left: 3px solid #FBBF24; max-height: 80vh !important; overflow-y: auto !important; transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease; transform: translateX(-10px); }
        .floating-submenu.active { visibility: visible; opacity: 1; transform: translateX(0); }
        .floating-submenu .menu-text { display: inline-block !important; color: #fff !important; white-space: nowrap; }
        .floating-submenu a { padding: 0.75rem 1rem !important; display: flex !important; align-items: center !important; gap: 12px !important; transition: all 0.2s !important; border-radius: 0.5rem !important; margin-bottom: 0.25rem !important; color: #93C5FD !important; }
        .floating-submenu a:hover { background: rgba(255, 255, 255, 0.15) !important; transform: translateX(5px) !important; color: white !important; }
        .floating-submenu i { color: #93C5FD !important; width: 1.25rem !important; text-align: center !important; transition: color 0.2s; }
        .floating-submenu a:hover i { color: white !important; }
        .floating-submenu .text-xs { font-size: 0.8125rem !important; line-height: 1.5 !important; }
        .sidebar-collapsed .sidebar-link:hover { background: rgba(255, 255, 255, 0.15) !important; border-left: 4px solid #FBBF24 !important; }
        .menu-gap { position: absolute; left: 100%; top: 0; width: 20px; height: 100%; z-index: 99998; }
        .swal2-shown { padding-right: 0 !important; }
        body.swal2-height-auto { height: 100vh !important; }
        .swal2-container { position: fixed !important; inset: 0 !important; }
        .sidebar-collapsed ~ main { margin-left: 5rem !important; }
        #sidebar { transition: transform 0.3s ease-in-out; }
        #sidebarOverlay { transition: opacity 0.3s ease-in-out; z-index: 35; }
        @media (min-width: 768px) {
            #sidebar { transform: none !important; }
            #sidebarOverlay { display: none !important; }
        }
    </style>
    <!-- SCRIPT CEK STATUS SIDEBAR SEBELUM HALAMAN DI-RENDER -->
    <script>
        (function() {
            const state = localStorage.getItem('sidebar-state');
            if (state === 'collapsed') {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>
</head>
<body class="h-screen flex flex-col text-sm">
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
                <button type="submit" title="Logout" class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-right-from-bracket text-lg"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Overlay untuk mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden transition-opacity" onclick="toggleMobileSidebar()"></div>

    <div class="flex flex-1 h-full">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-72 sidebar-gradient flex flex-col p-4 shadow-2xl fixed top-20 left-0 h-[calc(100vh-5rem)] z-40 transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0">
            <div class="flex-1 mt-1 overflow-y-auto custom-scrollbar pr-2">
                <nav class="space-y-1">
                    <!-- 1. DASHBOARD (Universal) -->
                    <a href="{{ route('welcome') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('welcome') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-solid fa-chart-pie text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Dashboard Overview</span>
                    </a>

                    <a href="{{ route('qcc.admin.master_schedule') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('*/master-schedule*') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-solid fa-calendar-check text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap text-sm">Master Schedule</span>
                    </a>

                    <!-- MONITORING DASHBOARD (Conditional for GMR, KDP, SPV) -->
                    @if(in_array($user->occupation, ['GMR', 'KDP', 'SPV']))
                        <a href="{{ route('qcc.admin.dashboard') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('*/dashboard*') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-solid fa-chart-line text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Monitoring Progres QCC</span>
                        </a>
                    @endif

                    <!-- 2. APPROVAL CIRCLE (Conditional) -->
                    @if($user->occupation === 'SPV' || $user->occupation === 'KDP')
                        <a href="{{ route('qcc.approval.circle') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('*/approval/circle*') ? 'bg-white/10' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg relative">
                                <i class="fa-solid fa-user-check text-blue-200"></i>
                                @if(isset($countCircle) && $countCircle > 0) 
                                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-[#091E6E]"></span> 
                                @endif
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Approve Circle</span>
                            @if(isset($countCircle) && $countCircle > 0) 
                                <span class="badge-number ml-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $countCircle }}</span> 
                            @endif
                        </a>

                        <a href="{{ route('qcc.approval.progress') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('*/approval/progress*') ? 'bg-white/10' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg relative">
                                <i class="fa-solid fa-file-signature text-blue-200"></i>
                                @if(isset($countProgress) && $countProgress > 0) 
                                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-[#091E6E]"></span> 
                                @endif
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Approve Progres</span>
                            @if(isset($countProgress) && $countProgress > 0) 
                                <span class="badge-number ml-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $countProgress }}</span> 
                            @endif
                        </a>
                    @endif

                    <!-- 3. KARYAWAN QCC ACTIVITY (Conditional) -->
                    @if(session('active_role') === 'employee')
                        <div class="relative group" data-submenu="karyawanSubmenu">
                            <button onclick="toggleKaryawanDropdown()" class="sidebar-link w-full flex items-center justify-between text-white p-4 rounded-xl focus:outline-none {{ request()->is('qcc/karyawan*') ? 'bg-white/10' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                        <i class="fa-solid fa-users-gear text-blue-200"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap text-sm">Circle QCC Saya</span>
                                </div>
                                <i id="karyawanArrow" class="fa-solid fa-chevron-down text-[10px] dropdown-arrow"></i>
                            </button>
                            <div class="menu-gap"></div>

                            <div id="karyawanSubmenu" class="submenu pl-12 space-y-1 {{ request()->is('qcc/karyawan*') ? 'show' : '' }}">
                                <a href="{{ route('qcc.karyawan.dashboard') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/dashboard') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-chart-line w-4"></i> <span class="menu-text">Dashboard Progres</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.roadmap') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/roadmap') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-map-location-dot w-4"></i> <span class="menu-text">Monitoring Roadmap</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.my_circle') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/my-circle') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-users w-4"></i> <span class="menu-text">Master Circle & Member</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.themes') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/themes') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-lightbulb w-4"></i> <span class="menu-text">Master Tema</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.progress') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/progress') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-cloud-arrow-up w-4"></i> <span class="menu-text">Upload Progress</span>
                                </a>
                            </div>
                        </div>

                        <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-solid fa-lightbulb text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Input SS Baru</span>
                        </a>
                    @endif

                    <!-- 4. ADMIN SYSTEM MANAGEMENT (Conditional) -->
                    @if(session('active_role') === 'admin')
                        <div class="relative group" data-submenu="qccSubmenu">
                            <button onclick="toggleQccDropdown()" class="sidebar-link w-full flex items-center justify-between text-white p-4 rounded-xl focus:outline-none {{ request()->is('qcc/admin*') ? 'bg-white/10' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                        <i class="fa-solid fa-people-group text-blue-200"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap text-sm">Monitoring QCC</span>
                                </div>
                                <i id="qccArrow" class="fa-solid fa-chevron-down text-[10px] dropdown-arrow"></i>
                            </button>
                            <div class="menu-gap"></div>

                            <div id="qccSubmenu" class="submenu pl-12 space-y-1 {{ request()->is('qcc/admin*') ? 'show' : '' }}">
                                <a href="{{ route('qcc.admin.dashboard') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/dashboard') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-chart-line w-4"></i> <span class="menu-text">Dashboard QCC</span>
                                </a>
                                <a href="{{ route('qcc.admin.master_steps') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/master-steps') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-list-ol w-4"></i> <span class="menu-text">Master Steps</span>
                                </a>
                                <a href="{{ route('qcc.admin.master_periods') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/master-periods') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-calendar-days w-4"></i> <span class="menu-text">Master Periode</span>
                                </a>
                                <a href="{{ route('qcc.admin.master_targets') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/master-targets') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-crosshairs w-4"></i> <span class="menu-text">Master Target</span>
                                </a>
                                <a href="{{ route('qcc.admin.all_progress') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/all-progress') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-chart-pie w-4"></i> <span class="menu-text">Progress Circle</span>
                                </a>
                            </div>
                        </div>

                        <!-- DROPDOWN MONITORING SS - BARU (Coming Soon) -->
                        <div class="relative group" data-submenu="ssSubmenu">
                            <button onclick="toggleSsDropdown()" class="sidebar-link w-full flex items-center justify-between text-white p-4 rounded-xl focus:outline-none">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                        <i class="fa-regular fa-lightbulb text-blue-200"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap text-sm">Monitoring SS</span>
                                </div>
                                <i id="ssArrow" class="fa-solid fa-chevron-down text-[10px] dropdown-arrow"></i>
                            </button>
                            <div class="menu-gap"></div>

                            <div id="ssSubmenu" class="submenu pl-12 space-y-1">
                                <a href="#" class="text-blue-100/70 hover:text-white text-xs py-2 block">
                                    <i class="fa-solid fa-chart-line w-4"></i> <span class="menu-text">Dashboard SS</span>
                                </a>
                                <a href="#" class="text-blue-100/70 hover:text-white text-xs py-2 block">
                                    <i class="fa-solid fa-list w-4"></i> <span class="menu-text">Daftar Ide</span>
                                </a>
                                <a href="#" class="text-blue-100/70 hover:text-white text-xs py-2 block">
                                    <i class="fa-solid fa-check-double w-4"></i> <span class="menu-text">Penilaian</span>
                                </a>
                                <a href="#" class="text-blue-100/70 hover:text-white text-xs py-2 block">
                                    <i class="fa-solid fa-trophy w-4"></i> <span class="menu-text">Hasil & Reward</span>
                                </a>
                                <a href="#" class="text-blue-100/70 hover:text-white text-xs py-2 block">
                                    <i class="fa-solid fa-calendar-alt w-4"></i> <span class="menu-text">Rekap Bulanan</span>
                                </a>
                                <a href="#" class="text-blue-100/70 hover:text-white text-xs py-2 block">
                                    <i class="fa-solid fa-gear w-4"></i> <span class="menu-text">Master SS</span>
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('admin.master_employee.index') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('admin/master-employee*') ? 'bg-white/10' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-solid fa-users-gear text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Master Karyawan</span>
                        </a>
                    @endif

                    <!-- <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('admin/master-employee*') ? 'bg-white/10' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-solid fa-graduation-cap text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Materi Training</span>
                        </a>

                        <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('admin/master-employee*') ? 'bg-white/10' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-solid fa-history text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Histori Training</span>
                        </a> -->
                </nav>
            </div>

            <div class="sidebar-footer bg-white/5 rounded-2xl p-4 mt-auto">
                <p class="text-blue-200 text-[10px] text-center whitespace-nowrap font-medium tracking-widest uppercase">© {{ date('Y') }} Satu AISIN</p>
            </div>
        </aside>

        <main class="flex-1 md:ml-72 p-8 overflow-y-auto custom-scrollbar h-[calc(100vh-5rem)]">
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
        const overlay = document.getElementById('sidebarOverlay');
        const qccSubmenu = document.getElementById('qccSubmenu');
        const qccArrow = document.getElementById('qccArrow');
        const karyawanSubmenu = document.getElementById('karyawanSubmenu');
        const karyawanArrow = document.getElementById('karyawanArrow');
        const ssSubmenu = document.getElementById('ssSubmenu');
        const ssArrow = document.getElementById('ssArrow');

        // Variabel global
        let hoverTimeout = null;
        let activeFloatingMenu = null;
        let isSidebarCollapsed = false;
        let isMouseInFloatingMenu = false;

        function isMobile() {
            return window.innerWidth < 768;
        }

        // Toggle mobile sidebar (off-canvas)
        function toggleMobileSidebar(open) {
            if (open === undefined) {
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                if (isOpen) {
                    // Tutup
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                } else {
                    // Buka – pastikan collapsed class dihapus agar lebar penuh
                    sidebar.classList.remove('sidebar-collapsed');
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                }
            } else {
                if (open) {
                    sidebar.classList.remove('sidebar-collapsed');
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                }
            }
        }

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', () => {
            const state = localStorage.getItem('sidebar-state');
            if (state === 'collapsed') {
                sidebar.classList.add('sidebar-collapsed');
                isSidebarCollapsed = true;
                closeAllSubmenus();
                setupFloatingMenuListeners();
            }
            if (isMobile()) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
            }
        });

        // Floating menu (sama seperti sebelumnya)
        function createFloatingSubmenu(parentButton, submenuId) {
            const existingFloating = document.querySelector('.floating-submenu');
            if (existingFloating) {
                existingFloating.remove();
                activeFloatingMenu = null;
            }
            const originalSubmenu = document.getElementById(submenuId);
            if (!originalSubmenu) return null;
            const floatingMenu = originalSubmenu.cloneNode(true);
            floatingMenu.id = 'floating-' + submenuId;
            floatingMenu.classList.add('floating-submenu');
            floatingMenu.classList.remove('submenu', 'show');
            
            floatingMenu.addEventListener('mouseenter', () => {
                isMouseInFloatingMenu = true;
                if (hoverTimeout) clearTimeout(hoverTimeout);
            });
            floatingMenu.addEventListener('mouseleave', (e) => {
                isMouseInFloatingMenu = false;
                if (!floatingMenu.contains(e.relatedTarget)) {
                    hoverTimeout = setTimeout(() => {
                        if (!isMouseInFloatingMenu) hideFloatingSubmenu();
                    }, 300);
                }
            });
            document.body.appendChild(floatingMenu);
            
            const parentRect = parentButton.getBoundingClientRect();
            const sidebarWidth = 80;
            let topPosition = parentRect.top - 10;
            const maxTop = window.innerHeight - 300;
            if (topPosition > maxTop) topPosition = maxTop;
            floatingMenu.style.left = (sidebarWidth + 5) + 'px';
            floatingMenu.style.top = topPosition + 'px';
            return floatingMenu;
        }

        function showFloatingSubmenu(button, submenuId) {
            if (!isSidebarCollapsed || isMobile()) return;
            if (hoverTimeout) clearTimeout(hoverTimeout);
            if (activeFloatingMenu) hideFloatingSubmenu();
            const floatingMenu = createFloatingSubmenu(button, submenuId);
            if (floatingMenu) {
                setTimeout(() => {
                    floatingMenu.classList.add('active');
                    activeFloatingMenu = floatingMenu;
                }, 50);
            }
        }

        function hideFloatingSubmenu() {
            if (activeFloatingMenu) {
                activeFloatingMenu.classList.remove('active');
                setTimeout(() => {
                    if (activeFloatingMenu && !activeFloatingMenu.classList.contains('active')) {
                        activeFloatingMenu.remove();
                        activeFloatingMenu = null;
                        isMouseInFloatingMenu = false;
                    }
                }, 300);
            }
        }

        function setupFloatingMenuListeners() {
            const groups = document.querySelectorAll('.group[data-submenu]');
            groups.forEach(group => {
                const button = group.querySelector('button');
                const menuGap = group.querySelector('.menu-gap');
                const submenuId = group.getAttribute('data-submenu');
                if (!button || !submenuId) return;

                button.addEventListener('mouseenter', () => {
                    if (!isSidebarCollapsed || isMobile()) return;
                    hoverTimeout = setTimeout(() => showFloatingSubmenu(button, submenuId), 200);
                });
                button.addEventListener('mouseleave', (e) => {
                    if (!isSidebarCollapsed || isMobile()) return;
                    if (hoverTimeout) clearTimeout(hoverTimeout);
                    const floatingMenu = document.querySelector('.floating-submenu.active');
                    const isMovingToGap = menuGap && menuGap.contains(e.relatedTarget);
                    const isMovingToFloatingMenu = floatingMenu && floatingMenu.contains(e.relatedTarget);
                    if (!isMovingToGap && !isMovingToFloatingMenu) {
                        hoverTimeout = setTimeout(() => {
                            if (!isMouseInFloatingMenu) hideFloatingSubmenu();
                        }, 200);
                    }
                });
                if (menuGap) {
                    menuGap.addEventListener('mouseenter', () => {
                        if (!isSidebarCollapsed || isMobile()) return;
                        if (hoverTimeout) clearTimeout(hoverTimeout);
                        if (!activeFloatingMenu) showFloatingSubmenu(button, submenuId);
                    });
                    menuGap.addEventListener('mouseleave', (e) => {
                        if (!isSidebarCollapsed || isMobile()) return;
                        const floatingMenu = document.querySelector('.floating-submenu.active');
                        const isMovingToFloatingMenu = floatingMenu && floatingMenu.contains(e.relatedTarget);
                        if (!isMovingToFloatingMenu) {
                            hoverTimeout = setTimeout(() => {
                                if (!isMouseInFloatingMenu) hideFloatingSubmenu();
                            }, 200);
                        }
                    });
                }
                group.addEventListener('mouseleave', (e) => {
                    if (!isSidebarCollapsed || isMobile()) return;
                    const floatingMenu = document.querySelector('.floating-submenu.active');
                    const isMovingToGap = menuGap && menuGap.contains(e.relatedTarget);
                    const isMovingToFloatingMenu = floatingMenu && floatingMenu.contains(e.relatedTarget);
                    if (!isMovingToGap && !isMovingToFloatingMenu) {
                        hoverTimeout = setTimeout(() => {
                            if (!isMouseInFloatingMenu) hideFloatingSubmenu();
                        }, 200);
                    }
                });
            });
            document.addEventListener('click', (e) => {
                const floatingMenu = document.querySelector('.floating-submenu.active');
                if (floatingMenu && !floatingMenu.contains(e.target) && !e.target.closest('.group')) {
                    hideFloatingSubmenu();
                }
            });
            window.addEventListener('scroll', () => {
                if (activeFloatingMenu && isSidebarCollapsed && !isMobile()) {
                    const button = document.querySelector('.group:hover button');
                    if (button) {
                        const parentRect = button.getBoundingClientRect();
                        let topPosition = parentRect.top - 10;
                        const maxTop = window.innerHeight - 300;
                        if (topPosition > maxTop) topPosition = maxTop;
                        activeFloatingMenu.style.top = topPosition + 'px';
                    }
                }
            });
        }

        // Toggle sidebar utama
        sidebarToggle.addEventListener('click', () => {
            if (isMobile()) {
                toggleMobileSidebar();
            } else {
                sidebar.classList.toggle('sidebar-collapsed');
                isSidebarCollapsed = sidebar.classList.contains('sidebar-collapsed');
                if (isSidebarCollapsed) {
                    localStorage.setItem('sidebar-state', 'collapsed');
                    closeAllSubmenus();
                    hideFloatingSubmenu();
                    setTimeout(setupFloatingMenuListeners, 100);
                } else {
                    localStorage.setItem('sidebar-state', 'expanded');
                    hideFloatingSubmenu();
                }
            }
        });

        overlay.addEventListener('click', () => toggleMobileSidebar(false));

        // Resize handler: menangani perpindahan mode mobile/desktop
        window.addEventListener('resize', function() {
            if (isMobile()) {
                // Masuk mode mobile: hapus collapsed class agar sidebar penuh saat dibuka
                if (sidebar.classList.contains('sidebar-collapsed')) {
                    sidebar.classList.remove('sidebar-collapsed');
                    // Jangan ubah localStorage, tetap ingat untuk desktop
                }
                // Pastikan sidebar tersembunyi
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                isSidebarCollapsed = false; // di mobile tidak ada collapsed
            } else {
                // Masuk mode desktop: pulihkan status collapsed dari localStorage
                const state = localStorage.getItem('sidebar-state');
                if (state === 'collapsed') {
                    sidebar.classList.add('sidebar-collapsed');
                    isSidebarCollapsed = true;
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                    isSidebarCollapsed = false;
                }
                // Pastikan sidebar terlihat
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                // Setup ulang floating menu jika perlu
                if (isSidebarCollapsed) {
                    setupFloatingMenuListeners();
                }
            }
        });

        function closeAllSubmenus() {
            document.querySelectorAll('.submenu').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown-arrow').forEach(el => el.classList.remove('rotate-180'));
        }

        // Fungsi dropdown yang sudah diperbaiki untuk mobile
        function toggleQccDropdown() {
            // Di desktop collapsed, dropdown diganti floating menu → jangan toggle
            if (isSidebarCollapsed && !isMobile()) return;
            // Di mobile, hanya boleh toggle jika sidebar terbuka
            if (isMobile() && sidebar.classList.contains('-translate-x-full')) return;
            // Selain itu, toggle biasa
            if (qccSubmenu) qccSubmenu.classList.toggle('show');
            if (qccArrow) qccArrow.classList.toggle('rotate-180');
        }

        function toggleKaryawanDropdown() {
            if (isSidebarCollapsed && !isMobile()) return;
            if (isMobile() && sidebar.classList.contains('-translate-x-full')) return;
            if (karyawanSubmenu) karyawanSubmenu.classList.toggle('show');
            if (karyawanArrow) karyawanArrow.classList.toggle('rotate-180');
        }

        // Fungsi baru untuk dropdown SS
        function toggleSsDropdown() {
            if (isSidebarCollapsed && !isMobile()) return;
            if (isMobile() && sidebar.classList.contains('-translate-x-full')) return;
            if (ssSubmenu) ssSubmenu.classList.toggle('show');
            if (ssArrow) ssArrow.classList.toggle('rotate-180');
        }

        // Flash message (sama)
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#091E6E', scrollbarPadding: false });
            @endif
            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#091E6E', scrollbarPadding: false });
            @endif
            @if(session('warning'))
                Swal.fire({ icon: 'warning', title: 'Perhatian!', text: "{{ session('warning') }}", confirmButtonColor: '#091E6E', scrollbarPadding: false });
            @endif
            @if(session('info'))
                Swal.fire({ icon: 'info', title: 'Informasi', text: "{{ session('info') }}", confirmButtonColor: '#091E6E', scrollbarPadding: false });
            @endif
        });
    </script>
</body>
</html>