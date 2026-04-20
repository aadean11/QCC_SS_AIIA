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
        .submenu a {
            position: relative;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.5rem 0.75rem 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.75rem;
            line-height: 1.25rem;
            color: #bfd9ff;
        }
        .submenu a::before {
            content: "";
            position: absolute;
            left: -14px;
            top: 50%;
            width: 14px;
            height: 1px;
            background-color: rgba(255,255,255,0.15);
            transform: translateY(-50%);
        }
        .submenu a::after {
            content: "";
            position: absolute;
            left: -18px;
            top: 50%;
            width: 4px;
            height: 4px;
            background-color: rgba(255,255,255,0.4);
            border-radius: 50%;
            transform: translateY(-50%);
        }
        .submenu a:hover { background-color: rgba(255,255,255,0.07); transform: translateX(4px); color: #ffffff !important; }
        .submenu a.font-bold { background-color: rgba(255,255,255,0.1); color: #ffffff !important; }
        .submenu i { width: 1rem; text-align: center; font-size: 0.75rem; }
        .dropdown-arrow { transition: transform 0.3s ease; }
        .rotate-180 { transform: rotate(180deg); }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            color: white;
        }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.1); border-left: 4px solid #FBBF24; }
        .sidebar-link .icon-box {
            width: 2rem;
            height: 2rem;
            min-width: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 0.5rem;
        }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #707070; border-radius: 10px;}
        .welcome-banner { background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-reveal { animation: fadeInUp 0.6s ease-out forwards; }
        .sidebar-collapsed .group { position: relative; }
        .floating-submenu { position: fixed !important; display: block !important; visibility: hidden; opacity: 0; width: 240px; background: #091E6E; border-radius: 0 1rem 1rem 0; padding: 0.75rem; box-shadow: 15px 5px 30px rgba(0, 0, 0, 0.4); z-index: 99999; border-left: 3px solid #FBBF24; max-height: 80vh !important; overflow-y: auto !important; transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease; transform: translateX(-10px); }
        .floating-submenu.active { visibility: visible; opacity: 1; transform: translateX(0); }
        .floating-submenu .menu-text { display: inline-block !important; color: #fff !important; white-space: nowrap; }
        .floating-submenu a { padding: 0.5rem 0.75rem !important; display: flex !important; align-items: center !important; gap: 0.75rem !important; transition: all 0.2s !important; border-radius: 0.5rem !important; margin-bottom: 0.25rem !important; color: #93C5FD !important; font-size: 0.75rem; }
        .floating-submenu a:hover { background: rgba(255, 255, 255, 0.15) !important; transform: translateX(5px) !important; color: white !important; }
        .floating-submenu i { color: #93C5FD !important; width: 1rem !important; text-align: center !important; transition: color 0.2s; }
        .floating-submenu a:hover i { color: white !important; }
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
        .submenu .badge-number {
            margin-left: auto;
            background-color: #EF4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            line-height: 1.25;
            min-width: 1.5rem;
            text-align: center;
        }
    </style>
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

    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden transition-opacity" onclick="toggleMobileSidebar()"></div>

    <div class="flex flex-1 h-full">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-72 sidebar-gradient flex flex-col p-4 shadow-2xl fixed top-20 left-0 h-[calc(100vh-5rem)] z-40 transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0">
            <div class="flex-1 mt-1 overflow-y-auto custom-scrollbar pr-2">
                <nav class="space-y-1">
                    <!-- DASHBOARD OVERVIEW -->
                    <a href="{{ route('welcome') }}" class="sidebar-link {{ request()->is('welcome') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                        <div class="icon-box">
                            <i class="fa-solid fa-chart-pie text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Dashboard Overview</span>
                    </a>

                    <a href="{{ route('qcc.admin.master_schedule') }}" class="sidebar-link {{ request()->is('*/master-schedule*') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                        <div class="icon-box">
                            <i class="fa-solid fa-calendar-check text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Master Schedule</span>
                    </a>

                    <!-- ==================== MONITORING QCC DROPDOWN (UNIFIED) ==================== -->
                    @if(session('active_role') === 'admin' || in_array($user->occupation, ['GMR', 'KDP', 'SPV']))
                        <div class="relative group" data-submenu="qccSubmenu">
                            <button type="button" class="sidebar-link w-full justify-between dropdown-toggle {{ request()->is('qcc/admin*') || request()->is('qcc/approval*') || request()->is('qcc/dashboard*') ? 'bg-white/10' : '' }}" data-dropdown="qcc">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box">
                                        <i class="fa-solid fa-people-group text-blue-200"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap">Monitoring QCC</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[10px] dropdown-arrow {{ request()->is('qcc/admin*') || request()->is('qcc/approval*') || request()->is('qcc/dashboard*') ? 'rotate-180' : '' }}" data-dropdown="qcc"></i>
                            </button>
                            <div class="menu-gap"></div>

                            <div id="qccSubmenu" class="submenu space-y-1 {{ request()->is('qcc/admin*') || request()->is('qcc/approval*') || request()->is('qcc/dashboard*') ? 'show' : '' }}">
                                @if(session('active_role') === 'admin')
                                    <a href="{{ route('qcc.admin.dashboard') }}" class="{{ request()->is('qcc/admin/dashboard') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-chart-line"></i>
                                        <span class="menu-text">Dashboard QCC</span>
                                    </a>
                                    <a href="{{ route('qcc.admin.master_steps') }}" class="{{ request()->is('qcc/admin/master-steps') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-list-ol"></i>
                                        <span class="menu-text">Master Steps</span>
                                    </a>
                                    <a href="{{ route('qcc.admin.master_seven_tools') }}" class="{{ request()->is('qcc/admin/master-seven-tools*') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                        <span class="menu-text">Master Seven Tools</span>
                                    </a>
                                    <a href="{{ route('qcc.admin.master_periods') }}" class="{{ request()->is('qcc/admin/master-periods') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <span class="menu-text">Master Periode</span>
                                    </a>
                                    <a href="{{ route('qcc.admin.master_targets') }}" class="{{ request()->is('qcc/admin/master-targets') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-crosshairs"></i>
                                        <span class="menu-text">Master Target</span>
                                    </a>
                                    <a href="{{ route('qcc.admin.all_progress') }}" class="{{ request()->is('qcc/admin/all-progress') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-chart-pie"></i>
                                        <span class="menu-text">Progress Circle</span>
                                    </a>
                                @endif

                                @if(in_array($user->occupation, ['GMR', 'KDP', 'SPV']))
                                    <a href="{{ route('qcc.admin.dashboard') }}" class="{{ request()->is('qcc/admin/dashboard') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-chart-line"></i>
                                        <span class="menu-text">Dashboard Progres QCC</span>
                                    </a>
                                @endif

                                @if(in_array($user->occupation, ['KDP', 'SPV']))
                                    <a href="{{ route('qcc.approval.circle') }}" class="{{ request()->is('*/approval/circle*') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-user-check"></i>
                                        <span class="menu-text">Approve Circle</span>
                                        @if(isset($countCircle) && $countCircle > 0)
                                            <span class="badge-number">{{ $countCircle }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('qcc.approval.progress') }}" class="{{ request()->is('*/approval/progress*') ? 'font-bold' : '' }}">
                                        <i class="fa-solid fa-file-signature"></i>
                                        <span class="menu-text">Approve Progres</span>
                                        @if(isset($countProgress) && $countProgress > 0)
                                            <span class="badge-number">{{ $countProgress }}</span>
                                        @endif
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- KARYAWAN QCC ACTIVITY -->
                    @if(session('active_role') === 'employee')
                        <div class="relative group" data-submenu="karyawanSubmenu">
                            <button type="button" class="sidebar-link w-full justify-between dropdown-toggle {{ request()->is('qcc/karyawan*') ? 'bg-white/10' : '' }}" data-dropdown="karyawan">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box">
                                        <i class="fa-solid fa-users-gear text-blue-200"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap">Circle QCC Saya</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[10px] dropdown-arrow {{ request()->is('qcc/karyawan*') ? 'rotate-180' : '' }}" data-dropdown="karyawan"></i>
                            </button>
                            <div class="menu-gap"></div>

                            <div id="karyawanSubmenu" class="submenu space-y-1 {{ request()->is('qcc/karyawan*') ? 'show' : '' }}">
                                <a href="{{ route('qcc.karyawan.dashboard') }}" class="{{ request()->is('qcc/karyawan/dashboard') ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <span class="menu-text">Dashboard Progres</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.roadmap') }}" class="{{ request()->is('qcc/karyawan/roadmap') ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    <span class="menu-text">Monitoring Roadmap</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.my_circle') }}" class="{{ request()->is('qcc/karyawan/my-circle') ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-users"></i>
                                    <span class="menu-text">Master Circle & Member</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.themes') }}" class="{{ request()->is('qcc/karyawan/themes') ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-lightbulb"></i>
                                    <span class="menu-text">Master Tema</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.progress') }}" class="{{ request()->is('qcc/karyawan/progress') ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <span class="menu-text">Upload Progress</span>
                                </a>
                            </div>
                        </div>

                        <!-- Menu SS untuk karyawan -->
                        <a href="{{ route('ss.karyawan.index') }}" class="sidebar-link {{ request()->is('ss/karyawan') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                            <div class="icon-box">
                                <i class="fa-solid fa-list-ul text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap">Daftar SS Saya</span>
                        </a>
                        <a href="{{ route('ss.karyawan.create') }}" class="sidebar-link {{ request()->is('ss/karyawan/create') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                            <div class="icon-box">
                                <i class="fa-solid fa-lightbulb text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap">Input SS Baru</span>
                        </a>
                    @endif

                    <!-- ADMIN SYSTEM MANAGEMENT -->
                    @if(session('active_role') === 'admin')
                        <div class="relative group" data-submenu="ssSubmenu">
                            <button type="button" class="sidebar-link w-full justify-between dropdown-toggle {{ request()->is('ss/admin*') ? 'bg-white/10' : '' }}" data-dropdown="ss">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box">
                                        <i class="fa-regular fa-lightbulb text-blue-200"></i>
                                    </div>
                                    <span class="menu-text font-medium whitespace-nowrap">Monitoring SS</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[10px] dropdown-arrow {{ request()->is('ss/admin*') ? 'rotate-180' : '' }}" data-dropdown="ss"></i>
                            </button>
                            <div class="menu-gap"></div>

                            <div id="ssSubmenu" class="submenu space-y-1 {{ request()->is('ss/admin*') ? 'show' : '' }}">
                                <a href="{{ route('ss.admin.dashboard') }}" class="{{ request()->is('ss/admin/dashboard') ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <span class="menu-text">Dashboard SS</span>
                                </a>
                                <a href="{{ route('ss.admin.submissions') }}" class="{{ request()->is('ss/admin/submissions') ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-list"></i>
                                    <span class="menu-text">Daftar Ide</span>
                                </a>
                                <a href="{{ route('ss.admin.submissions', ['status' => 'assessed']) }}" class="{{ request()->is('ss/admin/submissions*') && request()->get('status') == 'assessed' ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-check-double"></i>
                                    <span class="menu-text">Penilaian (Need SPV)</span>
                                </a>
                                <a href="{{ route('ss.admin.submissions', ['status' => 'approved']) }}" class="{{ request()->is('ss/admin/submissions*') && request()->get('status') == 'approved' ? 'font-bold' : '' }}">
                                    <i class="fa-solid fa-trophy"></i>
                                    <span class="menu-text">Hasil & Reward</span>
                                </a>
                                <a href="#" class="opacity-50 cursor-not-allowed">
                                    <i class="fa-solid fa-calendar-alt"></i>
                                    <span class="menu-text">Rekap Bulanan (Coming Soon)</span>
                                </a>
                                <a href="#" class="opacity-50 cursor-not-allowed">
                                    <i class="fa-solid fa-gear"></i>
                                    <span class="menu-text">Master SS (Coming Soon)</span>
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('admin.master_employee.index') }}" class="sidebar-link {{ request()->is('admin/master-employee*') ? 'bg-white/10' : '' }}">
                            <div class="icon-box">
                                <i class="fa-solid fa-users-gear text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap">Master Karyawan</span>
                        </a>

                        <a href="{{ route('admin.master_user.index') }}" class="sidebar-link {{ request()->is('admin/master-user*') ? 'bg-white/10' : '' }}">
                            <div class="icon-box">
                                <i class="fa-solid fa-user-lock text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap">Master User</span>
                        </a>
                    @endif
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        let hoverTimeout = null;
        let activeFloatingMenu = null;
        let isSidebarCollapsed = false;
        let isMouseInFloatingMenu = false;

        function isMobile() {
            return window.innerWidth < 768;
        }

        function toggleMobileSidebar(open) {
            if (open === undefined) {
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                if (isOpen) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                } else {
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

        function toggleDropdown(dropdownId, event) {
            if (event) event.stopPropagation();
            if (isSidebarCollapsed && !isMobile()) return;
            if (isMobile() && sidebar.classList.contains('-translate-x-full')) return;
            const submenu = document.getElementById(dropdownId);
            const arrow = submenu?.parentElement?.querySelector('.dropdown-arrow');
            if (submenu) submenu.classList.toggle('show');
            if (arrow) arrow.classList.toggle('rotate-180');
        }

        document.querySelectorAll('.dropdown-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const dropdown = btn.getAttribute('data-dropdown');
                if (dropdown === 'qcc') toggleDropdown('qccSubmenu', e);
                else if (dropdown === 'karyawan') toggleDropdown('karyawanSubmenu', e);
                else if (dropdown === 'ss') toggleDropdown('ssSubmenu', e);
            });
        });

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
                const button = group.querySelector('.dropdown-toggle');
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
                    const button = document.querySelector('.group:hover .dropdown-toggle');
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

        window.addEventListener('resize', function() {
            if (isMobile()) {
                if (sidebar.classList.contains('sidebar-collapsed')) sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                isSidebarCollapsed = false;
            } else {
                const state = localStorage.getItem('sidebar-state');
                if (state === 'collapsed') {
                    sidebar.classList.add('sidebar-collapsed');
                    isSidebarCollapsed = true;
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                    isSidebarCollapsed = false;
                }
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                if (isSidebarCollapsed) setupFloatingMenuListeners();
            }
        });

        function closeAllSubmenus() {
            document.querySelectorAll('.submenu').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown-arrow').forEach(el => el.classList.remove('rotate-180'));
        }

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