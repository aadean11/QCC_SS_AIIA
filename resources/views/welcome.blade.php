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

        /* HIDE NUMBER BADGE WHEN COLLAPSED */
        .sidebar-collapsed .badge-number {
            display: none !important;
        }

        /* ADJUST ICON CONTAINER WHEN COLLAPSED */
        .sidebar-collapsed .sidebar-link .relative {
            margin-right: 0 !important;
        }

        /* FIX POSITION OF RED DOT WHEN COLLAPSED */
        .sidebar-collapsed .sidebar-link .relative span.bg-red-500 {
            top: -2px !important;
            right: -2px !important;
            width: 10px !important; /* Ukuran titik merah sedikit dipertegas */
            height: 10px !important;
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

        /* UTILS */
        .dropdown-arrow { transition: transform 0.3s ease; }
        .rotate-180 { transform: rotate(180deg); }
        .sidebar-link { transition: all 0.3s ease; border-left: 4px solid transparent; }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.1); border-left: 4px solid #FBBF24; }
        
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #707070; border-radius: 10px;}
        
        .welcome-banner { background: linear-gradient(90deg, #091E6E 0%, #1035D1 100%); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-reveal { animation: fadeInUp 0.6s ease-out forwards; }

        /* ========================= */
        /* FIXED FLOATING SUBMENU - REVISI BUG */
        /* ========================= */

        /* Parent group untuk floating menu */
        .sidebar-collapsed .group {
            position: relative;
        }

        /* Floating submenu yang stabil */
        .floating-submenu {
            position: fixed !important;
            display: block !important;
            visibility: hidden;
            opacity: 0;
            width: 240px;
            background: #091E6E;
            border-radius: 0 1rem 1rem 0;
            padding: 1rem;
            box-shadow: 15px 5px 30px rgba(0, 0, 0, 0.4);
            z-index: 99999;
            border-left: 3px solid #FBBF24;
            max-height: 80vh !important;
            overflow-y: auto !important;
            transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
            transform: translateX(-10px);
        }

        .floating-submenu.active {
            visibility: visible;
            opacity: 1;
            transform: translateX(0);
        }

        .floating-submenu .menu-text {
            display: inline-block !important;
            color: #fff !important;
            white-space: nowrap;
        }

        /* Styling untuk submenu items di floating menu */
        .floating-submenu a {
            padding: 0.75rem 1rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            transition: all 0.2s !important;
            border-radius: 0.5rem !important;
            margin-bottom: 0.25rem !important;
            color: #93C5FD !important;
        }

        .floating-submenu a:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: translateX(5px) !important;
            color: white !important;
        }

        /* Pastikan icon visible */
        .floating-submenu i {
            color: #93C5FD !important;
            width: 1.25rem !important;
            text-align: center !important;
            transition: color 0.2s;
        }

        .floating-submenu a:hover i {
            color: white !important;
        }

        /* Styling untuk submenu items di floating menu */
        .floating-submenu .text-xs {
            font-size: 0.8125rem !important;
            line-height: 1.5 !important;
        }

        /* Hover effect untuk parent button saat sidebar collapsed */
        .sidebar-collapsed .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            border-left: 4px solid #FBBF24 !important;
        }

        /* Gap antara tombol dan floating menu (toleransi untuk mencegah kehilangan hover) */
        .menu-gap {
            position: absolute;
            left: 100%;
            top: 0;
            width: 20px;
            height: 100%;
            z-index: 99998;
        }

    </style>
    <!-- SCRIPT CEK STATUS SIDEBAR SEBELUM HALAMAN DI-RENDER (Mencegah Kedip) -->
    <script>
        (function() {
            const state = localStorage.getItem('sidebar-state');
            if (state === 'collapsed') {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>
</head>
<body class="h-screen flex flex-col overflow-hidden text-sm">

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

    <div class="flex flex-1 overflow-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-72 sidebar-gradient hidden md:flex flex-col p-4 shadow-2xl relative">
            <div class="flex-1 mt-1 overflow-y-auto custom-scrollbar pr-2">
                
                <nav class="space-y-1">
                    <!-- 1. DASHBOARD (Universal) -->
                    <a href="{{ route('welcome') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('welcome') ? 'bg-white/20 border-l-4 border-yellow-400' : '' }}">
                        <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                            <i class="fa-solid fa-chart-pie text-blue-200"></i>
                        </div>
                        <span class="menu-text font-medium whitespace-nowrap">Dashboard Overview</span>
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
                                <!-- Ini adalah Titik Merah (Indikator) - Tetap Tampil -->
                                @if(isset($countCircle) && $countCircle > 0) 
                                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-[#091E6E]"></span> 
                                @endif
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Approve Circle</span>
                            <!-- Tambahkan class 'badge-number' di sini -->
                            @if(isset($countCircle) && $countCircle > 0) 
                                <span class="badge-number ml-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $countCircle }}</span> 
                            @endif
                        </a>

                        <a href="{{ route('qcc.approval.progress') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('*/approval/progress*') ? 'bg-white/10' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg relative">
                                <i class="fa-solid fa-file-signature text-blue-200"></i>
                                <!-- Titik Merah -->
                                @if(isset($countProgress) && $countProgress > 0) 
                                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-[#091E6E]"></span> 
                                @endif
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Approve Progres</span>
                            <!-- Tambahkan class 'badge-number' di sini -->
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
                            <!-- Invisible gap untuk menghubungkan tombol dengan floating menu -->
                            <div class="menu-gap"></div>

                            <div id="karyawanSubmenu" class="submenu pl-12 space-y-1 {{ request()->is('qcc/karyawan*') ? 'show' : '' }}">
                                <a href="{{ route('qcc.karyawan.my_circle') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/my-circle') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-circle-info w-4"></i> <span class="menu-text">Info Circle & Member</span>
                                </a>
                                <a href="{{ route('qcc.karyawan.themes') }}" class="text-blue-100/70 hover:text-white text-xs py-2 block {{ request()->is('*/themes') ? 'text-white font-bold' : '' }}">
                                    <i class="fa-solid fa-lightbulb w-4"></i> <span class="menu-text">Manajemen Tema</span>
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
                            <!-- Invisible gap untuk menghubungkan tombol dengan floating menu -->
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
                            </div>
                        </div>

                        <a href="#" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-regular fa-lightbulb text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Monitoring SS</span>
                        </a>

                        <a href="{{ route('admin.master_employee.index') }}" class="sidebar-link flex items-center gap-4 text-white p-4 rounded-xl {{ request()->is('admin/master-employee*') ? 'bg-white/10' : '' }}">
                            <div class="w-8 h-8 min-w-[2rem] flex items-center justify-center bg-white/10 rounded-lg">
                                <i class="fa-solid fa-users-gear text-blue-200"></i>
                            </div>
                            <span class="menu-text font-medium whitespace-nowrap text-sm">Master Karyawan</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="sidebar-footer bg-white/5 rounded-2xl p-4 mt-auto">
                <p class="text-blue-200 text-[10px] text-center whitespace-nowrap font-medium tracking-widest uppercase">© {{ date('Y') }} Satu AISIN</p>
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
        const qccSubmenu = document.getElementById('qccSubmenu');
        const qccArrow = document.getElementById('qccArrow');
        const karyawanSubmenu = document.getElementById('karyawanSubmenu');
        const karyawanArrow = document.getElementById('karyawanArrow');

        // Variabel global untuk kontrol floating menu
        let hoverTimeout = null;
        let activeFloatingMenu = null;
        let isSidebarCollapsed = false;
        let isMouseInFloatingMenu = false;

        // LOGIKA SAAT HALAMAN DIMUAT (Restore State)
        document.addEventListener('DOMContentLoaded', () => {
            const state = localStorage.getItem('sidebar-state');
            if (state === 'collapsed') {
                sidebar.classList.add('sidebar-collapsed');
                isSidebarCollapsed = true;
                closeAllSubmenus();
                setupFloatingMenuListeners();
            }
        });

        // Fungsi untuk membuat floating submenu
        function createFloatingSubmenu(parentButton, submenuId) {
            // Hapus floating menu yang sudah ada
            const existingFloating = document.querySelector('.floating-submenu');
            if (existingFloating) {
                existingFloating.remove();
                activeFloatingMenu = null;
            }
            
            // Clone submenu yang asli
            const originalSubmenu = document.getElementById(submenuId);
            if (!originalSubmenu) return null;
            
            const floatingMenu = originalSubmenu.cloneNode(true);
            floatingMenu.id = 'floating-' + submenuId;
            floatingMenu.classList.add('floating-submenu');
            floatingMenu.classList.remove('submenu');
            floatingMenu.classList.remove('show');
            
            // Tambahkan event listener untuk floating menu
            floatingMenu.addEventListener('mouseenter', () => {
                isMouseInFloatingMenu = true;
                if (hoverTimeout) {
                    clearTimeout(hoverTimeout);
                }
            });
            
            floatingMenu.addEventListener('mouseleave', (e) => {
                isMouseInFloatingMenu = false;
                // Cek apakah mouse benar-benar meninggalkan floating menu
                if (!floatingMenu.contains(e.relatedTarget)) {
                    hoverTimeout = setTimeout(() => {
                        if (!isMouseInFloatingMenu) {
                            hideFloatingSubmenu();
                        }
                    }, 300); // Delay lebih lama untuk memberi waktu ke floating menu
                }
            });
            
            // Tambahkan ke body
            document.body.appendChild(floatingMenu);
            
            // Hitung posisi
            const parentRect = parentButton.getBoundingClientRect();
            const sidebarWidth = 80; // 5rem = 80px
            
            // Pastikan posisi tidak melebihi tinggi layar
            let topPosition = parentRect.top - 10;
            const maxTop = window.innerHeight - 300;
            
            if (topPosition > maxTop) {
                topPosition = maxTop;
            }
            
            floatingMenu.style.left = (sidebarWidth + 5) + 'px';
            floatingMenu.style.top = topPosition + 'px';
            
            return floatingMenu;
        }

        // Fungsi untuk menampilkan floating submenu
        function showFloatingSubmenu(button, submenuId) {
            if (!isSidebarCollapsed) return;
            
            // Clear timeout sebelumnya
            if (hoverTimeout) {
                clearTimeout(hoverTimeout);
            }
            
            // Tutup menu aktif sebelumnya
            if (activeFloatingMenu) {
                hideFloatingSubmenu();
            }
            
            // Buat dan tampilkan menu baru
            const floatingMenu = createFloatingSubmenu(button, submenuId);
            if (floatingMenu) {
                // Small delay untuk memastikan DOM sudah siap
                setTimeout(() => {
                    floatingMenu.classList.add('active');
                    activeFloatingMenu = floatingMenu;
                }, 50);
            }
        }

        // Fungsi untuk menyembunyikan floating submenu
        function hideFloatingSubmenu() {
            if (activeFloatingMenu) {
                activeFloatingMenu.classList.remove('active');
                setTimeout(() => {
                    if (activeFloatingMenu && !activeFloatingMenu.classList.contains('active')) {
                        activeFloatingMenu.remove();
                        activeFloatingMenu = null;
                        isMouseInFloatingMenu = false;
                    }
                }, 300); // Match dengan CSS transition
            }
        }

        // Setup event listeners untuk floating menu
        function setupFloatingMenuListeners() {
            const groups = document.querySelectorAll('.group[data-submenu]');
            
            groups.forEach(group => {
                const button = group.querySelector('button');
                const menuGap = group.querySelector('.menu-gap');
                const submenuId = group.getAttribute('data-submenu');
                
                if (!button || !submenuId) return;
                
                // Event untuk tombol utama
                button.addEventListener('mouseenter', () => {
                    if (!isSidebarCollapsed) return;
                    
                    hoverTimeout = setTimeout(() => {
                        showFloatingSubmenu(button, submenuId);
                    }, 200); // Delay sedikit untuk mencegah flash
                });
                
                button.addEventListener('mouseleave', (e) => {
                    if (!isSidebarCollapsed) return;
                    
                    if (hoverTimeout) {
                        clearTimeout(hoverTimeout);
                    }
                    
                    // Cek apakah cursor pindah ke menu gap atau floating menu
                    const floatingMenu = document.querySelector('.floating-submenu.active');
                    const isMovingToGap = menuGap && menuGap.contains(e.relatedTarget);
                    const isMovingToFloatingMenu = floatingMenu && floatingMenu.contains(e.relatedTarget);
                    
                    if (!isMovingToGap && !isMovingToFloatingMenu) {
                        hoverTimeout = setTimeout(() => {
                            if (!isMouseInFloatingMenu) {
                                hideFloatingSubmenu();
                            }
                        }, 200);
                    }
                });
                
                // Event untuk menu gap (area transisi antara tombol dan floating menu)
                if (menuGap) {
                    menuGap.addEventListener('mouseenter', () => {
                        if (!isSidebarCollapsed) return;
                        
                        if (hoverTimeout) {
                            clearTimeout(hoverTimeout);
                        }
                        
                        // Tampilkan floating menu jika belum tampil
                        if (!activeFloatingMenu) {
                            showFloatingSubmenu(button, submenuId);
                        }
                    });
                    
                    menuGap.addEventListener('mouseleave', (e) => {
                        if (!isSidebarCollapsed) return;
                        
                        const floatingMenu = document.querySelector('.floating-submenu.active');
                        const isMovingToFloatingMenu = floatingMenu && floatingMenu.contains(e.relatedTarget);
                        
                        if (!isMovingToFloatingMenu) {
                            hoverTimeout = setTimeout(() => {
                                if (!isMouseInFloatingMenu) {
                                    hideFloatingSubmenu();
                                }
                            }, 200);
                        }
                    });
                }
                
                // Event untuk group parent
                group.addEventListener('mouseleave', (e) => {
                    if (!isSidebarCollapsed) return;
                    
                    const floatingMenu = document.querySelector('.floating-submenu.active');
                    const isMovingToGap = menuGap && menuGap.contains(e.relatedTarget);
                    const isMovingToFloatingMenu = floatingMenu && floatingMenu.contains(e.relatedTarget);
                    
                    if (!isMovingToGap && !isMovingToFloatingMenu) {
                        hoverTimeout = setTimeout(() => {
                            if (!isMouseInFloatingMenu) {
                                hideFloatingSubmenu();
                            }
                        }, 200);
                    }
                });
            });
            
            // Tutup floating menu saat klik di luar
            document.addEventListener('click', (e) => {
                const floatingMenu = document.querySelector('.floating-submenu.active');
                if (floatingMenu && !floatingMenu.contains(e.target) && 
                    !e.target.closest('.group')) {
                    hideFloatingSubmenu();
                }
            });
            
            // Update posisi floating menu saat scroll
            window.addEventListener('scroll', () => {
                if (activeFloatingMenu && isSidebarCollapsed) {
                    const button = document.querySelector('.group:hover button');
                    if (button) {
                        const parentRect = button.getBoundingClientRect();
                        const sidebarWidth = 80;
                        let topPosition = parentRect.top - 10;
                        const maxTop = window.innerHeight - 300;
                        
                        if (topPosition > maxTop) {
                            topPosition = maxTop;
                        }
                        
                        activeFloatingMenu.style.top = topPosition + 'px';
                    }
                }
            });
        }

        // Toggle Sidebar & Simpan Status ke LocalStorage
        sidebarToggle.addEventListener('click', () => {
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
        });

        function closeAllSubmenus() {
            document.querySelectorAll('.submenu').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown-arrow').forEach(el => el.classList.remove('rotate-180'));
        }

        function toggleQccDropdown() {
            // Hanya toggle jika sidebar tidak collapsed
            if (!isSidebarCollapsed) {
                if (qccSubmenu) qccSubmenu.classList.toggle('show');
                if (qccArrow) qccArrow.classList.toggle('rotate-180');
            }
        }

        function toggleKaryawanDropdown() {
            // Hanya toggle jika sidebar tidak collapsed
            if (!isSidebarCollapsed) {
                if (karyawanSubmenu) karyawanSubmenu.classList.toggle('show');
                if (karyawanArrow) karyawanArrow.classList.toggle('rotate-180');
            }
        }

        // GLOBAL FLASH MESSAGE HANDLER
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#091E6E' });
            @endif

            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#091E6E' });
            @endif

            @if(session('warning'))
                Swal.fire({ icon: 'warning', title: 'Perhatian!', text: "{{ session('warning') }}", confirmButtonColor: '#091E6E' });
            @endif

            @if(session('info'))
                Swal.fire({ 
                    icon: 'info', 
                    title: 'Informasi', 
                    text: "{{ session('info') }}", 
                    confirmButtonColor: '#091E6E' 
                });
            @endif
        });
    </script>
</body>
</html>