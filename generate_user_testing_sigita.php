<?php
/**
 * Generator: User Testing SIGITA (format mengikuti User Testing_63.docx)
 * Jalankan: php generate_user_testing_sigita.php
 */

$templatePath = file_exists(__DIR__ . '/User Testing_63.docx')
    ? __DIR__ . '/User Testing_63.docx'
    : __DIR__ . '/User Testing_SIGITA.docx';
$outputPath = __DIR__ . '/User Testing_SIGITA.docx';
$tempOutputPath = __DIR__ . '/User Testing_SIGITA.tmp.docx';

$rows = [];

$addModule = function (string $module, array $cases) use (&$rows) {
    foreach ($cases as $i => $case) {
        $rows[] = array_merge([
            'module' => $i === 0 ? $module : '',
            'no_fungsi' => $case['code'],
            'deskripsi' => $case['desc'],
            'kelompok' => $case['group'],
            'prosedur' => $case['steps'],
            'harapan' => $case['expected'],
        ], $case['extra'] ?? []);
    }
};

$addSeparator = function (string $title, string $description = '') use (&$rows) {
    $rows[] = [
        'separator' => true,
        'title' => $title,
        'description' => $description,
    ];
};

$addSeparator('ALUR TESTING LOGIN SEBAGAI KARYAWAN UMUM', 'Digunakan untuk memastikan login dasar, dashboard umum, dan akses karyawan non-approval sebelum masuk ke skenario role khusus.');

// ==================== LOGIN ====================
$addModule('LOGIN', [
    [
        'code' => 'LG001',
        'desc' => 'Melakukan Login Sistem (Karyawan)',
        'group' => 'Data Benar',
        'steps' => "1. Buka halaman login SIGITA (/login)\n2. Masukkan NPK yang terdaftar\n3. Masukkan password yang benar\n4. Klik tombol Masuk Ke Sistem\n5. Sistem otomatis masuk sebagai Karyawan (tanpa modal pilih akses)",
        'expected' => 'Berhasil masuk ke Dashboard Overview SIGITA dan menampilkan pesan selamat datang',
    ],
    [
        'code' => 'LG002',
        'desc' => 'Melakukan Login Sistem (Admin)',
        'group' => 'Data Benar',
        'steps' => "1. Buka halaman login SIGITA\n2. Masukkan NPK admin yang terdaftar\n3. Masukkan password yang benar\n4. Klik tombol \"Masuk Ke Sistem\"\n5. Pilih \"Masuk sebagai Admin\" pada modal Pilih Akses",
        'expected' => 'Berhasil masuk ke Dashboard Overview dengan menu Monitoring QCC, Monitoring SS, Master Karyawan, dan Master User',
    ],
    [
        'code' => 'LG003',
        'desc' => 'Melakukan Login Sistem',
        'group' => 'Data Salah',
        'steps' => "1. Buka halaman login SIGITA\n2. Masukkan NPK yang tidak terdaftar atau password salah\n3. Klik tombol \"Masuk Ke Sistem\"",
        'expected' => 'Menampilkan pesan error (NPK tidak terdaftar / Password salah)',
    ],
    [
        'code' => 'LG004',
        'desc' => 'Reset Password (Lupa Password)',
        'group' => 'Data Benar',
        'steps' => "1. Klik link \"Lupa Password?\" pada halaman login\n2. Isi NPK, Password Baru, dan Konfirmasi Password\n3. Klik tombol \"Update Password\"",
        'expected' => 'Password berhasil diperbarui dan dapat digunakan untuk login',
    ],
    [
        'code' => 'LG005',
        'desc' => 'Reset Password (Lupa Password)',
        'group' => 'Data Salah',
        'steps' => "1. Klik link \"Lupa Password?\"\n2. Isi password baru dan konfirmasi password yang tidak sama\n3. Klik tombol \"Update Password\"",
        'expected' => 'Menampilkan pesan error konfirmasi password tidak cocok',
    ],
]);

// ==================== DASHBOARD ====================
$addModule('DASHBOARD', [
    [
        'code' => 'DB001',
        'desc' => 'Melihat Dashboard Overview',
        'group' => 'Data Benar',
        'steps' => "1. Berhasil login ke sistem\n2. Pastikan berada di menu Dashboard Overview (/welcome)",
        'expected' => 'Menampilkan statistik performa (jumlah QCC, SS, departemen, role aktif, cakupan data, dan pembaruan terakhir)',
    ],
    [
        'code' => 'DB002',
        'desc' => 'Melihat Ringkasan QCC & SS',
        'group' => 'Data Benar',
        'steps' => "1. Login dan buka Dashboard Overview\n2. Scroll ke bagian ringkasan QCC dan SS",
        'expected' => 'Menampilkan kartu statistik QCC (total circle, anggota rata-rata, circle bulan ini) dan SS (pending, approved, rejected, bulan ini) sesuai hak akses user',
    ],
]);

$addSeparator('ALUR TESTING LOGIN SEBAGAI ADMIN', 'Login contoh: NPK 000188, sandi aiia, pilih akses Masuk sebagai Admin. Skenario berikut berfokus pada dashboard admin, master data, monitoring QCC/SS, konfigurasi target/scoring, export, dan reward.');

// ==================== MASTER SCHEDULE ====================
$addModule('MASTER SCHEDULE', [
    [
        'code' => 'MS001',
        'desc' => 'Melihat Master Schedule QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Pilih menu \"Master Schedule\" di sidebar",
        'expected' => 'Menampilkan jadwal kegiatan QCC (periode, step, dan timeline)',
    ],
]);

// ==================== MONITORING QCC (ADMIN) ====================
$addModule('MONITORING QCC', [
    [
        'code' => 'MQ001',
        'desc' => 'Melihat Dashboard QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka menu Monitoring QCC > Dashboard QCC",
        'expected' => 'Menampilkan dashboard statistik dan grafik progress QCC',
    ],
    [
        'code' => 'MQ002',
        'desc' => 'Melihat Master Steps QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring QCC > Master Steps",
        'expected' => 'Menampilkan daftar step PDCA (Step 1–8) QCC',
    ],
    [
        'code' => 'MQ003',
        'desc' => 'Menambah Master Steps QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Steps\n2. Klik tombol tambah step\n3. Isi nama step, urutan, dan deskripsi\n4. Klik Save",
        'expected' => 'Data step berhasil ditambahkan dan muncul di tabel Master Steps',
    ],
    [
        'code' => 'MQ004',
        'desc' => 'Mengubah Master Steps QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Steps\n2. Klik tombol Edit pada salah satu data\n3. Ubah data step\n4. Klik Save",
        'expected' => 'Menampilkan pesan data berhasil diubah dan perubahan tersimpan',
    ],
    [
        'code' => 'MQ005',
        'desc' => 'Menghapus Master Steps QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Steps\n2. Klik tombol Hapus pada salah satu data\n3. Konfirmasi hapus",
        'expected' => 'Data step terhapus dari tabel Master Steps',
    ],
    [
        'code' => 'MQ006',
        'desc' => 'Melihat Master Seven Tools',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring QCC > Master Seven Tools",
        'expected' => 'Menampilkan daftar seven tools QCC',
    ],
    [
        'code' => 'MQ007',
        'desc' => 'Menambah Master Seven Tools',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Seven Tools\n2. Klik tombol tambah\n3. Isi nama tool dan deskripsi\n4. Klik Save",
        'expected' => 'Data seven tool berhasil ditambahkan',
    ],
    [
        'code' => 'MQ008',
        'desc' => 'Melihat Master Periode QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring QCC > Master Periode",
        'expected' => 'Menampilkan daftar periode QCC (tahun, tanggal mulai/selesai)',
    ],
    [
        'code' => 'MQ009',
        'desc' => 'Menambah Master Periode QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Periode\n2. Klik tombol tambah periode\n3. Isi tahun, tanggal mulai, dan tanggal selesai\n4. Klik Save",
        'expected' => 'Periode QCC berhasil ditambahkan',
    ],
    [
        'code' => 'MQ010',
        'desc' => 'Melihat Master Target QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring QCC > Master Target",
        'expected' => 'Menampilkan daftar target QCC per departemen/periode',
    ],
    [
        'code' => 'MQ011',
        'desc' => 'Menambah Master Target QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Target\n2. Klik tombol tambah target\n3. Isi departemen, periode, dan nilai target\n4. Klik Save",
        'expected' => 'Target QCC berhasil ditambahkan',
    ],
    [
        'code' => 'MQ012',
        'desc' => 'Melihat Progress Circle (Semua Circle)',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring QCC > Progress Circle",
        'expected' => 'Menampilkan daftar seluruh circle QCC beserta progress step dan status approval',
    ],
    [
        'code' => 'MQ013',
        'desc' => 'Melihat Detail Anggota Circle',
        'group' => 'Data Benar',
        'steps' => "1. Buka halaman Progress Circle\n2. Klik tombol/ikon lihat anggota pada salah satu circle",
        'expected' => 'Menampilkan modal daftar anggota circle (nama, NPK, jabatan)',
    ],
    [
        'code' => 'MQ014',
        'desc' => 'Monitoring Progress per Circle',
        'group' => 'Data Benar',
        'steps' => "1. Buka Progress Circle\n2. Klik salah satu circle untuk melihat detail monitoring file transaksi",
        'expected' => 'Menampilkan detail progress upload file per step circle',
    ],
]);

// ==================== APPROVAL QCC ====================
$addSeparator('ALUR TESTING LOGIN SEBAGAI SPV', 'Login sebagai employee dengan jabatan SPV. Skenario berikut berfokus pada approval tahap SPV untuk registrasi circle, progress QCC, dan review SS departemen.');
$addModule('APPROVAL QCC', [
    [
        'code' => 'AQ001',
        'desc' => 'Melihat Daftar Approval Circle Baru',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai SPV/KDP\n2. Buka Monitoring QCC > Approve Circle",
        'expected' => 'Menampilkan daftar registrasi circle baru yang menunggu persetujuan',
    ],
    [
        'code' => 'AQ002',
        'desc' => 'Menyetujui Registrasi Circle',
        'group' => 'Data Benar',
        'steps' => "1. Buka Approve Circle\n2. Pilih circle yang statusnya pending\n3. Klik Approve/Setujui\n4. Konfirmasi persetujuan",
        'expected' => 'Status circle berubah menjadi approved dan circle dapat melanjutkan proses QCC',
    ],
    [
        'code' => 'AQ003',
        'desc' => 'Menolak Registrasi Circle',
        'group' => 'Data Benar',
        'steps' => "1. Buka Approve Circle\n2. Pilih circle pending\n3. Klik Reject/Tolak dan isi alasan\n4. Konfirmasi penolakan",
        'expected' => 'Status circle berubah menjadi rejected dengan catatan alasan',
    ],
    [
        'code' => 'AQ004',
        'desc' => 'Melihat Daftar Approval Progres PDCA',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai SPV/KDP\n2. Buka Monitoring QCC > Approve Progres",
        'expected' => 'Menampilkan daftar upload progress step (1–8) yang menunggu approval',
    ],
    [
        'code' => 'AQ005',
        'desc' => 'Menyetujui Progres PDCA',
        'group' => 'Data Benar',
        'steps' => "1. Buka Approve Progres\n2. Pilih progress yang pending\n3. Review file yang diupload\n4. Klik Approve/Setujui",
        'expected' => 'Status progress berubah approved dan step circle maju ke tahap berikutnya',
    ],
    [
        'code' => 'AQ006',
        'desc' => 'Menolak Progres PDCA',
        'group' => 'Data Benar',
        'steps' => "1. Buka Approve Progres\n2. Pilih progress pending\n3. Klik Reject/Tolak dan isi catatan\n4. Konfirmasi",
        'expected' => 'Status progress berubah rejected dan karyawan dapat upload ulang',
    ],
]);

// ==================== CIRCLE QCC KARYAWAN ====================
$addSeparator('ALUR TESTING LOGIN SEBAGAI LEADER', 'Login sebagai employee dengan jabatan LDR/Leader. Skenario berikut berfokus pada pembuatan circle, pengelolaan tema QCC, upload progress, dan pengajuan SS pribadi/operator.');
$addModule('CIRCLE QCC KARYAWAN', [
    [
        'code' => 'CQ001',
        'desc' => 'Melihat Dashboard Progres QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Karyawan\n2. Buka menu Circle QCC Saya > Dashboard Progres",
        'expected' => 'Menampilkan ringkasan progress circle milik user (step aktif, status, timeline)',
    ],
    [
        'code' => 'CQ002',
        'desc' => 'Melihat Monitoring Roadmap',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Karyawan\n2. Buka Circle QCC Saya > Monitoring Roadmap",
        'expected' => 'Menampilkan roadmap visual step PDCA beserta status setiap tahap',
    ],
    [
        'code' => 'CQ003',
        'desc' => 'Melihat Master Circle & Member',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Karyawan\n2. Buka Circle QCC Saya > Master Circle & Member",
        'expected' => 'Menampilkan daftar circle yang didaftarkan user beserta anggota',
    ],
    [
        'code' => 'CQ004',
        'desc' => 'Menambah Circle & Member Baru',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Circle & Member\n2. Klik tombol tambah circle\n3. Isi nama circle, periode, dan tambahkan anggota (NPK)\n4. Klik Save/Submit",
        'expected' => 'Circle baru tersimpan dengan status pending menunggu approval SPV/KDP',
    ],
    [
        'code' => 'CQ005',
        'desc' => 'Menambah Circle & Member Baru',
        'group' => 'Data Salah',
        'steps' => "1. Buka Master Circle & Member\n2. Klik tambah circle\n3. Kosongkan field wajib atau isi data tidak valid\n4. Klik Save",
        'expected' => 'Menampilkan pesan validasi error pada field yang belum diisi',
    ],
    [
        'code' => 'CQ006',
        'desc' => 'Mengubah Data Circle',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Circle & Member\n2. Klik Edit pada circle yang masih pending\n3. Ubah data circle/anggota\n4. Klik Save",
        'expected' => 'Data circle berhasil diperbarui',
    ],
    [
        'code' => 'CQ007',
        'desc' => 'Menghapus Data Circle',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Circle & Member\n2. Klik Hapus pada circle pending\n3. Konfirmasi hapus",
        'expected' => 'Circle terhapus dari daftar',
    ],
    [
        'code' => 'CQ008',
        'desc' => 'Melihat Master Tema',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Karyawan\n2. Buka Circle QCC Saya > Master Tema",
        'expected' => 'Menampilkan daftar tema QCC milik circle user',
    ],
    [
        'code' => 'CQ009',
        'desc' => 'Menambah Tema QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Tema\n2. Klik tambah tema\n3. Pilih circle dan isi judul/deskripsi tema\n4. Klik Save",
        'expected' => 'Tema QCC berhasil ditambahkan ke circle',
    ],
    [
        'code' => 'CQ010',
        'desc' => 'Mengubah Tema QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Tema\n2. Klik Edit pada salah satu tema\n3. Ubah judul/deskripsi\n4. Klik Save",
        'expected' => 'Tema berhasil diperbarui',
    ],
    [
        'code' => 'CQ011',
        'desc' => 'Menghapus Tema QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Tema\n2. Klik Hapus pada salah satu tema\n3. Konfirmasi hapus",
        'expected' => 'Tema terhapus dari daftar',
    ],
    [
        'code' => 'CQ012',
        'desc' => 'Upload Progress PDCA (Step)',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Karyawan dengan circle yang sudah approved\n2. Buka Circle QCC Saya > Upload Progress\n3. Pilih circle, step, dan seven tool\n4. Upload file (PDF/dokumen sesuai ketentuan)\n5. Klik Submit",
        'expected' => 'File berhasil diupload dan status progress menjadi pending approval',
    ],
    [
        'code' => 'CQ013',
        'desc' => 'Upload Progress PDCA (Step)',
        'group' => 'Data Salah',
        'steps' => "1. Buka Upload Progress\n2. Submit tanpa memilih file atau format file tidak sesuai",
        'expected' => 'Menampilkan pesan error validasi file wajib diisi / format tidak didukung',
    ],
]);

// ==================== MONITORING SS (ADMIN) ====================
$addSeparator('ALUR TESTING ADMIN MONITORING SS', 'Lanjutan skenario Admin untuk monitoring SS, daftar ide, review komite, reward, master target, dan master scoring.');
$addModule('MONITORING SS', [
    [
        'code' => 'SSA001',
        'desc' => 'Melihat Dashboard SS',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka menu Monitoring SS > Dashboard SS",
        'expected' => 'Menampilkan statistik pengajuan SS (total, pending, approved, rejected)',
    ],
    [
        'code' => 'SSA002',
        'desc' => 'Melihat Daftar Ide SS',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring SS > Daftar Ide",
        'expected' => 'Menampilkan tabel seluruh pengajuan SS dengan status, pengaju, dan tanggal',
    ],
    [
        'code' => 'SSA003',
        'desc' => 'Mencari Data Daftar Ide SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Daftar Ide\n2. Input kata kunci pencarian (nama pengaju/status)\n3. Tekan Enter atau klik cari",
        'expected' => 'Menampilkan data SS sesuai kata kunci pencarian',
    ],
    [
        'code' => 'SSA004',
        'desc' => 'Melihat Detail Pengajuan SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Daftar Ide\n2. Klik salah satu data untuk melihat detail",
        'expected' => 'Menampilkan detail lengkap pengajuan SS termasuk file lampiran dan riwayat status',
    ],
    [
        'code' => 'SSA005',
        'desc' => 'Review Komite SS',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring SS > Review Komite\n3. Pilih SS yang menunggu review admin\n4. Isi penilaian/scoring dan keputusan\n5. Submit review",
        'expected' => 'Status SS diperbarui sesuai keputusan komite (approved/rejected)',
    ],
    [
        'code' => 'SSA006',
        'desc' => 'Melihat Hasil & Reward SS',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring SS > Hasil & Reward",
        'expected' => 'Menampilkan daftar SS yang sudah approved beserta informasi reward',
    ],
    [
        'code' => 'SSA007',
        'desc' => 'Input Reward SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Hasil & Reward\n2. Pilih SS yang sudah approved\n3. Isi data reward\n4. Klik Save",
        'expected' => 'Reward berhasil disimpan dan status SS menjadi rewarded',
    ],
    [
        'code' => 'SSA008',
        'desc' => 'Melihat Master Target SS',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring SS > Master Target",
        'expected' => 'Menampilkan daftar target SS per departemen',
    ],
    [
        'code' => 'SSA009',
        'desc' => 'Menambah Master Target SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Target SS\n2. Klik tambah target\n3. Isi departemen dan nilai target\n4. Klik Save",
        'expected' => 'Target SS berhasil ditambahkan',
    ],
    [
        'code' => 'SSA010',
        'desc' => 'Melihat Master Scoring SS',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka Monitoring SS > Master Scoring",
        'expected' => 'Menampilkan kriteria penilaian dan bobot scoring SS',
    ],
    [
        'code' => 'SSA011',
        'desc' => 'Menambah Master Scoring SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Scoring\n2. Klik tambah kriteria\n3. Isi nama kriteria dan bobot nilai\n4. Klik Save",
        'expected' => 'Kriteria scoring berhasil ditambahkan',
    ],
    [
        'code' => 'SSA012',
        'desc' => 'Export Daftar Ide SS ke PDF',
        'group' => 'Data Benar',
        'steps' => "1. Buka Daftar Ide SS\n2. Klik tombol Export PDF",
        'expected' => 'File PDF daftar pengajuan SS berhasil diunduh',
    ],
]);

// ==================== SS KARYAWAN ====================
$addModule('SS KARYAWAN', [
    [
        'code' => 'SSK001',
        'desc' => 'Melihat Daftar SS Saya',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Karyawan (OPR/LDR)\n2. Klik menu \"Daftar SS Saya\" atau \"Daftar SS Dept\" (untuk LDR)",
        'expected' => 'Menampilkan daftar pengajuan SS milik user/departemen beserta status',
    ],
    [
        'code' => 'SSK002',
        'desc' => 'Mencari Data SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Daftar SS Saya\n2. Input kata kunci pada kolom pencarian",
        'expected' => 'Menampilkan data SS sesuai pencarian',
    ],
    [
        'code' => 'SSK003',
        'desc' => 'Melihat Detail SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Daftar SS Saya\n2. Klik salah satu data SS",
        'expected' => 'Menampilkan detail pengajuan SS termasuk file dan riwayat approval',
    ],
    [
        'code' => 'SSK004',
        'desc' => 'Mengajukan SS Baru (Pengajuan SS)',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Karyawan yang berhak submit SS\n2. Klik menu Pengajuan SS\n3. Pilih tipe pengajuan (self/OPR untuk LDR)\n4. Upload file PDF formulir SS\n5. Isi catatan (opsional)\n6. Klik Submit",
        'expected' => 'Pengajuan SS berhasil tersimpan dengan status submitted dan masuk antrian review SPV',
    ],
    [
        'code' => 'SSK005',
        'desc' => 'Mengajukan SS Baru (Pengajuan SS)',
        'group' => 'Data Salah',
        'steps' => "1. Buka Pengajuan SS\n2. Submit tanpa upload file atau file bukan format PDF\n3. Klik Submit",
        'expected' => 'Menampilkan pesan error validasi (file wajib / format PDF / ukuran maks 5MB)',
    ],
]);

// ==================== APPROVAL SS ====================
$addSeparator('ALUR TESTING LOGIN SEBAGAI KDP', 'Login sebagai employee dengan jabatan KDP. Skenario berikut berfokus pada approval tahap KDP untuk QCC dan SS setelah tahap SPV selesai.');
$addModule('APPROVAL SS', [
    [
        'code' => 'ASS001',
        'desc' => 'Melihat Daftar Review SS (SPV)',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai SPV\n2. Buka Monitoring SS > Review SS (SPV)",
        'expected' => 'Menampilkan daftar SS departemen yang menunggu review SPV',
    ],
    [
        'code' => 'ASS002',
        'desc' => 'Menyetujui SS (SPV)',
        'group' => 'Data Benar',
        'steps' => "1. Buka Review SS (SPV)\n2. Pilih SS pending\n3. Review detail dan file\n4. Klik Approve/Setujui",
        'expected' => 'Status SS berubah ke kdp_review dan diteruskan ke KDP',
    ],
    [
        'code' => 'ASS003',
        'desc' => 'Menolak SS (SPV)',
        'group' => 'Data Benar',
        'steps' => "1. Buka Review SS (SPV)\n2. Pilih SS pending\n3. Klik Reject/Tolak dan isi catatan\n4. Konfirmasi",
        'expected' => 'Status SS berubah menjadi rejected',
    ],
    [
        'code' => 'ASS004',
        'desc' => 'Melihat Daftar Review SS (KDP)',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai KDP\n2. Buka Monitoring SS > Review SS (KDP)",
        'expected' => 'Menampilkan daftar SS yang sudah disetujui SPV dan menunggu review KDP',
    ],
    [
        'code' => 'ASS005',
        'desc' => 'Menyetujui SS (KDP)',
        'group' => 'Data Benar',
        'steps' => "1. Buka Review SS (KDP)\n2. Pilih SS pending\n3. Review detail\n4. Klik Approve/Setujui",
        'expected' => 'Status SS berubah ke admin_review dan diteruskan ke komite admin',
    ],
    [
        'code' => 'ASS006',
        'desc' => 'Menolak SS (KDP)',
        'group' => 'Data Benar',
        'steps' => "1. Buka Review SS (KDP)\n2. Pilih SS pending\n3. Klik Reject/Tolak\n4. Konfirmasi",
        'expected' => 'Status SS berubah menjadi rejected',
    ],
]);

// ==================== MASTER DATA ====================
$addModule('MASTER DATA', [
    [
        'code' => 'MK001',
        'desc' => 'Melihat Master Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka menu Master Karyawan",
        'expected' => 'Menampilkan daftar data karyawan (NPK, nama, departemen, jabatan)',
    ],
    [
        'code' => 'MK002',
        'desc' => 'Menambah Data Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Karyawan\n2. Klik tambah karyawan\n3. Isi NPK, nama, departemen, dan jabatan\n4. Klik Save",
        'expected' => 'Data karyawan berhasil ditambahkan',
    ],
    [
        'code' => 'MK003',
        'desc' => 'Mengubah Data Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Karyawan\n2. Klik Edit pada salah satu data\n3. Ubah data karyawan\n4. Klik Save",
        'expected' => 'Data karyawan berhasil diperbarui',
    ],
    [
        'code' => 'MK004',
        'desc' => 'Menghapus Data Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Karyawan\n2. Klik Hapus pada salah satu data\n3. Konfirmasi hapus",
        'expected' => 'Data karyawan terhapus dari sistem',
    ],
    [
        'code' => 'MK005',
        'desc' => 'Melihat Master User',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai Admin\n2. Buka menu Master User",
        'expected' => 'Menampilkan daftar user akses sistem (NPK, role)',
    ],
    [
        'code' => 'MK006',
        'desc' => 'Menambah User Akses',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master User\n2. Klik tambah user\n3. Isi NPK, password, dan role\n4. Klik Save",
        'expected' => 'User akses berhasil ditambahkan dan dapat login ke SIGITA',
    ],
    [
        'code' => 'MK007',
        'desc' => 'Mengubah User Akses',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master User\n2. Klik Edit pada salah satu user\n3. Ubah role/password\n4. Klik Save",
        'expected' => 'Data user berhasil diperbarui',
    ],
    [
        'code' => 'MK008',
        'desc' => 'Menghapus User Akses',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master User\n2. Klik Hapus pada salah satu user\n3. Konfirmasi hapus",
        'expected' => 'User terhapus dan tidak dapat login lagi',
    ],
]);

// ==================== RINCIAN LENGKAP SESUAI FITUR AKTUAL ====================
$akun = "Login Admin:\nNPK: 000188\nSandi: aiia\nPilih akses: Masuk sebagai Admin";
$akunKaryawan = "Login Karyawan:\nNPK: 000189\nSandi: aiia\nPilih akses: Masuk sebagai Karyawan";
$akunSpv = "Login SPV:\nNPK: contoh user jabatan SPV\nSandi: aiia\nPilih akses: Masuk sebagai Karyawan";
$akunKdp = "Login KDP:\nNPK: contoh user jabatan KDP\nSandi: aiia\nPilih akses: Masuk sebagai Karyawan";
$akunLeader = "Login Leader:\nNPK: contoh user jabatan LDR\nSandi: aiia\nPilih akses: Masuk sebagai Karyawan";
$nilaiSs = "Contoh skor SS:\nSafety=20, Lingkungan=15, Ergonomi/Higiene=10, Quality=15, Usaha=15, Manfaat=8, Kepekaan=8, Keaslian=8, Delivery=8, Cost/MP=20";

$addSeparator('RINCIAN TESTING ROLE ADMIN', 'Seluruh skenario di bawah dijalankan setelah login sebagai Admin. Gunakan NPK 000188 dan sandi aiia, lalu pilih akses Masuk sebagai Admin.');

$addModule('MASTER USER - SEARCH, PAGING, CRUD, VALIDASI', [
    [
        'code' => 'MU001',
        'desc' => 'Login Admin untuk akses Master User',
        'group' => 'Data Benar',
        'steps' => "1. Buka /login\n2. $akun\n3. Buka menu Master User (/admin/master-user)",
        'expected' => 'Halaman Master User tampil berisi tabel user, kolom search, pilihan per_page, tombol tambah, edit, dan hapus.',
    ],
    [
        'code' => 'MU002',
        'desc' => 'Search Master User berdasarkan NPK/nama/email',
        'group' => 'Data Benar',
        'steps' => "1. Pada Master User isi kolom search dengan contoh: 000188\n2. Tekan Enter atau klik cari\n3. Ulangi dengan kata kunci: admin@aiia.co.id",
        'expected' => 'Tabel hanya menampilkan user yang NPK, nama, atau email-nya sesuai kata kunci. Parameter search tetap terbawa saat pindah halaman.',
    ],
    [
        'code' => 'MU003',
        'desc' => 'Paging Master User',
        'group' => 'Data Benar',
        'steps' => "1. Pilih per_page: 10\n2. Klik halaman berikutnya pada pagination\n3. Ganti per_page menjadi 20",
        'expected' => 'Jumlah baris tabel mengikuti pilihan per_page dan pagination tetap mempertahankan search/filter yang aktif.',
    ],
    [
        'code' => 'MU004',
        'desc' => 'Tambah User Akses',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah User\n2. Isi NPK: 009901\n3. Nama: User Testing Admin\n4. Email: user.testing.admin@aiia.co.id\n5. Role: Admin\n6. Status User: ACTIVE\n7. OT PAR: A\n8. Limit MP: 5\n9. Klik Simpan",
        'expected' => 'User baru tersimpan, tampil pesan sukses, dan password default yang dapat digunakan adalah aiia.',
    ],
    [
        'code' => 'MU005',
        'desc' => 'Validasi Tambah User Akses',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah User\n2. Kosongkan NPK dan Email\n3. Isi Email: email-salah\n4. Isi Limit MP: -1\n5. Klik Simpan",
        'expected' => 'Sistem menolak penyimpanan dan menampilkan validasi: NPK wajib, format email tidak valid, limit MP tidak boleh kurang dari 0.',
    ],
    [
        'code' => 'MU006',
        'desc' => 'Edit User Akses',
        'group' => 'Data Benar',
        'steps' => "1. Cari user NPK 009901\n2. Klik Edit\n3. Ubah Role: Supervisor\n4. Ubah Status User: INACTIVE\n5. Isi Password baru: aiia123\n6. Klik Simpan",
        'expected' => 'Data user diperbarui. Role/status/password baru tersimpan dan muncul pada tabel.',
    ],
    [
        'code' => 'MU007',
        'desc' => 'Hapus User Akses',
        'group' => 'Data Benar',
        'steps' => "1. Cari user NPK 009901\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'User terhapus dari tabel dan tidak dapat login lagi.',
    ],
    [
        'code' => 'MU008',
        'desc' => 'Validasi Hapus Akun Sendiri',
        'group' => 'Data Salah',
        'steps' => "1. Login sebagai admin NPK 000188\n2. Buka Master User\n3. Coba hapus akun 000188 yang sedang digunakan",
        'expected' => 'Sistem menolak hapus akun sendiri dan menampilkan pesan error.',
    ],
]);

$addModule('MASTER KARYAWAN - SEARCH, PAGING, CRUD, VALIDASI', [
    [
        'code' => 'MKY001',
        'desc' => 'Melihat Master Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Master Karyawan (/admin/master-employee)",
        'expected' => 'Tabel karyawan tampil dengan data NPK, nama, jabatan, sub-section/departemen, nomor WA, status, tombol tambah, edit, hapus, search, dan paging.',
    ],
    [
        'code' => 'MKY002',
        'desc' => 'Search Master Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Isi search: 000188\n2. Klik cari\n3. Ulangi dengan search: Dean",
        'expected' => 'Data terfilter berdasarkan NPK atau nama karyawan.',
    ],
    [
        'code' => 'MKY003',
        'desc' => 'Paging Master Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Pilih per_page: 10\n2. Klik pagination halaman 2\n3. Ubah per_page: 20",
        'expected' => 'Jumlah data per halaman berubah sesuai per_page dan query search tetap dipertahankan.',
    ],
    [
        'code' => 'MKY004',
        'desc' => 'Tambah Data Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Karyawan\n2. Isi NPK: 009902\n3. Nama: Karyawan User Testing\n4. Nomor WA: 081234567890\n5. Dept/Line Code: PROD-A\n6. Sub-Section: pilih salah satu data tersedia, contoh PRD-SUB-01\n7. Jabatan: pilih OPR\n8. Transport: BUS\n9. Status Karyawan: ACTIVE\n10. Employment Status: PERMANENT\n11. Klik Simpan",
        'expected' => 'Karyawan baru tersimpan, quota_used_1 sampai quota_used_12 dan quota_remain_1 sampai quota_remain_12 otomatis bernilai 0.',
    ],
    [
        'code' => 'MKY005',
        'desc' => 'Validasi Tambah Karyawan',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah Karyawan\n2. Isi NPK: 0099021 (lebih dari 6 karakter)\n3. Kosongkan Nama\n4. Isi Nomor WA: abcde\n5. Kosongkan Sub-Section dan Jabatan\n6. Klik Simpan",
        'expected' => 'Sistem menolak penyimpanan dan menampilkan validasi NPK maksimal 6 karakter, nama wajib, nomor WA hanya angka/simbol telepon, sub-section dan jabatan wajib dipilih.',
    ],
    [
        'code' => 'MKY006',
        'desc' => 'Edit Data Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Search NPK: 009902\n2. Klik Edit\n3. Ubah Nama: Karyawan User Testing Update\n4. Ubah Nomor WA: 089876543210\n5. Ubah Status Karyawan: INACTIVE\n6. Klik Simpan",
        'expected' => 'Perubahan data karyawan tersimpan dan tabel menampilkan data terbaru.',
    ],
    [
        'code' => 'MKY007',
        'desc' => 'Hapus Data Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Search NPK: 009902\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Data karyawan terhapus dari Master Karyawan.',
    ],
]);

$addModule('MASTER QCC STEP - SEARCH, PAGING, CRUD, TEMPLATE', [
    [
        'code' => 'MQS001',
        'desc' => 'Melihat dan Search Master Step QCC',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring QCC > Master Steps\n3. Isi search: Plan\n4. Pilih per_page: 10 lalu pindah halaman",
        'expected' => 'Tabel step QCC tampil dan dapat difilter berdasarkan nama step/deskripsi. Paging mengikuti per_page.',
    ],
    [
        'code' => 'MQS002',
        'desc' => 'Tambah Master Step QCC',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Step\n2. Isi Step Number: 9\n3. Step Name: Standardisasi Improvement\n4. Description: Finalisasi standard kerja hasil QCC\n5. Upload template_file: template_step9.pdf\n6. Klik Simpan",
        'expected' => 'Step baru tersimpan. File template berformat ppt/pptx/xls/xlsx/pdf maksimal 10 MB tersimpan di storage.',
    ],
    [
        'code' => 'MQS003',
        'desc' => 'Validasi Tambah Master Step QCC',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah Step\n2. Kosongkan Step Number dan Step Name\n3. Upload file template: gambar.png\n4. Klik Simpan",
        'expected' => 'Sistem menolak data karena step number dan step name wajib, serta format template tidak sesuai.',
    ],
    [
        'code' => 'MQS004',
        'desc' => 'Edit Master Step QCC',
        'group' => 'Data Benar',
        'steps' => "1. Search: Standardisasi Improvement\n2. Klik Edit\n3. Ubah Step Name: Standardisasi & Yokoten\n4. Upload template baru: template_step9_update.xlsx\n5. Klik Simpan",
        'expected' => 'Step dan template diperbarui; template lama diganti oleh template baru.',
    ],
    [
        'code' => 'MQS005',
        'desc' => 'Hapus Master Step QCC',
        'group' => 'Data Benar',
        'steps' => "1. Search: Standardisasi & Yokoten\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Step terhapus dan file template terkait ikut dihapus dari storage apabila ada.',
    ],
]);

$addModule('MASTER QCC PERIODE - SEARCH, PAGING, CRUD, DEADLINE', [
    [
        'code' => 'MQP001',
        'desc' => 'Melihat, Search, dan Paging Master Periode QCC',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring QCC > Master Periods\n3. Search: 2026\n4. Pilih per_page: 10 dan klik halaman berikutnya",
        'expected' => 'Periode QCC tampil dan dapat dicari berdasarkan period_name, period_code, atau tahun. Paging berjalan sesuai per_page.',
    ],
    [
        'code' => 'MQP002',
        'desc' => 'Tambah Periode QCC',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Periode\n2. Period Code: QCC-2026-UAT\n3. Period Name: Periode QCC User Testing 2026\n4. Year: 2026\n5. Start Date: 2026-01-01\n6. End Date: 2026-12-31\n7. Klik Simpan",
        'expected' => 'Periode berstatus ACTIVE dibuat dan seluruh deadline step otomatis terbentuk mengikuti tanggal akhir periode.',
    ],
    [
        'code' => 'MQP003',
        'desc' => 'Validasi Periode QCC',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah Periode\n2. Period Code: QCC-2026-UAT yang sudah ada\n3. Year: 26\n4. Start Date: 2026-12-31\n5. End Date: 2026-01-01\n6. Klik Simpan",
        'expected' => 'Sistem menolak karena period code harus unik, year harus 4 digit, dan end date tidak boleh sebelum start date.',
    ],
    [
        'code' => 'MQP004',
        'desc' => 'Edit Data Periode QCC',
        'group' => 'Data Benar',
        'steps' => "1. Search: QCC-2026-UAT\n2. Klik Edit\n3. Ubah Period Name: Periode QCC UAT Revisi\n4. Status: ACTIVE\n5. Klik Simpan",
        'expected' => 'Data periode berhasil diperbarui.',
    ],
    [
        'code' => 'MQP005',
        'desc' => 'Edit Deadline Step Periode QCC',
        'group' => 'Data Benar',
        'steps' => "1. Klik tombol pengaturan deadline pada periode QCC-2026-UAT\n2. Isi deadline Step 1: 2026-02-28\n3. Isi deadline Step 2: 2026-03-31\n4. Lanjutkan hingga Step 8 sesuai timeline\n5. Klik Simpan Deadline",
        'expected' => 'Deadline setiap step pada periode tersebut tersimpan dan digunakan pada dashboard/schedule.',
    ],
    [
        'code' => 'MQP006',
        'desc' => 'Hapus Periode QCC',
        'group' => 'Data Benar',
        'steps' => "1. Search: QCC-2026-UAT\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Periode terhapus beserta relasi period step, circle, transaksi progress, tema, dan target terkait.',
    ],
]);

$addModule('MASTER QCC TARGET - SEARCH, PAGING, CRUD, VALIDASI', [
    [
        'code' => 'MQT001',
        'desc' => 'Melihat, Search, dan Paging Target QCC',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring QCC > Master Target\n3. Search: Production\n4. Pilih per_page: 10",
        'expected' => 'Target QCC tampil dan dapat dicari berdasarkan nama departemen, periode, atau kode departemen.',
    ],
    [
        'code' => 'MQT002',
        'desc' => 'Tambah Target QCC',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Target\n2. Pilih Periode: Periode QCC 2026 ACTIVE\n3. Department Code: PRD\n4. Target Amount: 12\n5. Klik Simpan",
        'expected' => 'Target QCC departemen tersimpan untuk periode yang dipilih.',
    ],
    [
        'code' => 'MQT003',
        'desc' => 'Validasi Target QCC Duplikat',
        'group' => 'Data Salah',
        'steps' => "1. Tambahkan target dengan Periode dan Department Code yang sama seperti data sebelumnya\n2. Target Amount: 10\n3. Klik Simpan",
        'expected' => 'Sistem menolak dengan pesan target untuk departemen pada periode tersebut sudah ada.',
    ],
    [
        'code' => 'MQT004',
        'desc' => 'Edit Target QCC',
        'group' => 'Data Benar',
        'steps' => "1. Search: PRD\n2. Klik Edit\n3. Ubah Target Amount: 15\n4. Klik Simpan",
        'expected' => 'Target amount berubah menjadi 15.',
    ],
    [
        'code' => 'MQT005',
        'desc' => 'Hapus Target QCC',
        'group' => 'Data Benar',
        'steps' => "1. Search target PRD pada periode uji\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Target QCC terhapus dari tabel.',
    ],
]);

$addModule('MASTER QCC SEVEN TOOLS - SEARCH, PAGING, CRUD, TEMPLATE', [
    [
        'code' => 'MST001',
        'desc' => 'Melihat, Search, dan Paging Seven Tools',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring QCC > Master Seven Tools\n3. Search: Pareto\n4. Pilih per_page: 10",
        'expected' => 'Data seven tools tampil dan dapat dicari berdasarkan nama tool atau deskripsi.',
    ],
    [
        'code' => 'MST002',
        'desc' => 'Tambah Seven Tool',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Tool\n2. Tool Name: Pareto Chart UAT\n3. Description: Template analisis pareto untuk user testing\n4. Upload template_file: pareto_uat.xlsx\n5. Klik Simpan",
        'expected' => 'Seven tool baru tersimpan beserta template valid.',
    ],
    [
        'code' => 'MST003',
        'desc' => 'Validasi Seven Tool',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah Tool\n2. Kosongkan Tool Name\n3. Upload file: pareto.png\n4. Klik Simpan",
        'expected' => 'Sistem menolak karena tool name wajib dan file harus ppt/pptx/xls/xlsx/pdf maksimal 10 MB.',
    ],
    [
        'code' => 'MST004',
        'desc' => 'Edit Seven Tool',
        'group' => 'Data Benar',
        'steps' => "1. Search: Pareto Chart UAT\n2. Klik Edit\n3. Ubah Description: Template pareto update\n4. Upload template_file: pareto_uat_update.pdf\n5. Klik Simpan",
        'expected' => 'Data tool dan template berhasil diperbarui.',
    ],
    [
        'code' => 'MST005',
        'desc' => 'Hapus Seven Tool',
        'group' => 'Data Benar',
        'steps' => "1. Search: Pareto Chart UAT\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Seven tool dan file template terkait terhapus.',
    ],
]);

$addSeparator('RINCIAN TESTING ROLE LEADER', 'Seluruh skenario di bawah dijalankan setelah login sebagai Leader/LDR. Alurnya dimulai dari dashboard karyawan, pembuatan circle/tema QCC, upload progress, sampai pengajuan SS pribadi atau SS operator.');

$addModule('QCC KARYAWAN - CIRCLE, TEMA, PROGRESS, FILTER', [
    [
        'code' => 'QCK001',
        'desc' => 'Dashboard QCC Karyawan Filter Periode',
        'group' => 'Data Benar',
        'steps' => "1. $akunKaryawan\n2. Buka Circle QCC Saya > Dashboard Progres\n3. Pilih period_id: Periode QCC 2026",
        'expected' => 'Dashboard menampilkan total circle, butuh perhatian, sedang review, selesai, chart per circle, dan garis timeline sesuai periode.',
    ],
    [
        'code' => 'QCK002',
        'desc' => 'Search dan Paging Master Circle & Member',
        'group' => 'Data Benar',
        'steps' => "1. Buka Circle QCC Saya > Master Circle & Member\n2. Search: SIGITA\n3. Pilih per_page: 10 dan pindah halaman",
        'expected' => 'Circle milik user difilter berdasarkan nama/kode circle dan paging berjalan.',
    ],
    [
        'code' => 'QCK003',
        'desc' => 'Tambah Circle QCC',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Circle\n2. Circle Name: SIGITA Improvement Circle\n3. Members: pilih NPK 000190 dan 000191\n4. Upload Step 0 File: registrasi_circle_sigita.pdf\n5. Klik Simpan",
        'expected' => 'Circle tersimpan dengan kode otomatis C-xxxxxx, status WAITING SPV, leader otomatis user login, anggota tersimpan, dan dokumen Step 0 terupload.',
    ],
    [
        'code' => 'QCK004',
        'desc' => 'Validasi Tambah Circle QCC',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah Circle\n2. Kosongkan Circle Name\n3. Tidak memilih members\n4. Upload file: registrasi.docx\n5. Klik Simpan",
        'expected' => 'Sistem menolak karena nama circle wajib, members minimal 1, dan Step 0 harus PDF maksimal 10 MB.',
    ],
    [
        'code' => 'QCK005',
        'desc' => 'Edit Circle QCC',
        'group' => 'Data Benar',
        'steps' => "1. Cari circle SIGITA Improvement Circle\n2. Pastikan status WAITING SPV atau REJECTED\n3. Klik Edit\n4. Ubah Circle Name: SIGITA Improvement Circle Update\n5. Ubah anggota menjadi NPK 000190 dan 000192\n6. Klik Simpan",
        'expected' => 'Nama circle dan daftar anggota diperbarui. Edit ditolak jika circle sudah ACTIVE.',
    ],
    [
        'code' => 'QCK006',
        'desc' => 'Hapus Circle QCC',
        'group' => 'Data Benar',
        'steps' => "1. Cari circle yang statusnya WAITING SPV atau REJECTED\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Circle, anggota, dan file Step 0 terhapus. Hapus ditolak jika circle sudah ACTIVE atau user bukan leader.',
    ],
    [
        'code' => 'QCK007',
        'desc' => 'Filter Roadmap QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Monitoring Roadmap\n2. Pilih periode: 2026\n3. Search: SIGITA",
        'expected' => 'Roadmap hanya menampilkan circle user pada periode dan kata kunci yang dipilih.',
    ],
    [
        'code' => 'QCK008',
        'desc' => 'Tambah Tema QCC',
        'group' => 'Data Benar',
        'steps' => "1. Pastikan circle status ACTIVE\n2. Buka Master Tema dengan circle_id circle aktif\n3. Klik Tambah Tema\n4. QCC Period: Periode QCC 2026\n5. Theme Name: Menurunkan defect input data SIGITA\n6. Description: Perbaikan validasi dan alur input data\n7. Klik Simpan",
        'expected' => 'Tema aktif dibuat oleh leader. Jika sudah ada tema ACTIVE pada periode yang sama, sistem menolak pembuatan tema baru.',
    ],
    [
        'code' => 'QCK009',
        'desc' => 'Edit Tema QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Tema\n2. Klik Edit pada tema yang belum memiliki progress approved\n3. Ubah Theme Name: Menurunkan defect input master SIGITA\n4. Status: ACTIVE\n5. Klik Simpan",
        'expected' => 'Tema diperbarui. Edit ditolak jika user bukan leader atau sudah ada progress step approved.',
    ],
    [
        'code' => 'QCK010',
        'desc' => 'Hapus Tema QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Tema\n2. Pilih tema yang belum memiliki upload progress\n3. Klik Hapus\n4. Konfirmasi",
        'expected' => 'Tema terhapus. Hapus ditolak jika sudah ada progress diupload.',
    ],
    [
        'code' => 'QCK011',
        'desc' => 'Upload Progress QCC Step 1-8',
        'group' => 'Data Benar',
        'steps' => "1. Buka Upload Progress\n2. Pilih theme_id: tema aktif SIGITA\n3. Pada Step 1 klik Upload\n4. Pilih Seven Tool: Pareto Chart\n5. Upload file: step1_pareto_sigita.pdf\n6. Klik Submit",
        'expected' => 'File progress tersimpan sebagai PDF, status menjadi WAITING SPV. Step berikutnya terkunci sampai step sebelumnya APPROVED.',
    ],
    [
        'code' => 'QCK012',
        'desc' => 'Validasi Upload Progress QCC',
        'group' => 'Data Salah',
        'steps' => "1. Buka Upload Progress\n2. Kosongkan qcc_step_id atau upload file step1.docx\n3. Klik Submit",
        'expected' => 'Sistem menolak karena qcc_step_id, qcc_theme_id, qcc_circle_id wajib dan file harus PDF maksimal 10 MB.',
    ],
]);

$addSeparator('RINCIAN TESTING ROLE SPV', 'Seluruh skenario di bawah dijalankan setelah login sebagai SPV. Alurnya dimulai setelah Leader mengajukan circle/progress/SS, lalu SPV melakukan approve atau reject.');

$addModule('APPROVAL QCC - SEARCH, PAGING, APPROVE, REJECT', [
    [
        'code' => 'AQD001',
        'desc' => 'Search dan Paging Approval Circle',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai SPV/KDP karyawan departemen terkait\n2. Buka Approval QCC > Approve Circle\n3. Search: SIGITA\n4. Pilih per_page: 10 dan pindah halaman",
        'expected' => 'Daftar circle departemen terkait tampil, dapat dicari berdasarkan nama/kode circle, dan paging aktif.',
    ],
    [
        'code' => 'AQD002',
        'desc' => 'Approve Circle oleh SPV',
        'group' => 'Data Benar',
        'steps' => "1. Login SPV\n2. Pilih circle status WAITING SPV\n3. Action: approve\n4. Note: Dokumen registrasi lengkap\n5. Klik Proses",
        'expected' => 'Status circle berubah menjadi WAITING KDP dan tanggal approval SPV tersimpan.',
    ],
    [
        'code' => 'AQD003',
        'desc' => 'Approve Circle oleh KDP',
        'group' => 'Data Benar',
        'steps' => "1. Login KDP\n2. Pilih circle status WAITING KDP\n3. Action: approve\n4. Note: Disetujui untuk berjalan\n5. Klik Proses",
        'expected' => 'Status circle berubah menjadi ACTIVE dan dapat membuat tema QCC.',
    ],
    [
        'code' => 'AQD004',
        'desc' => 'Reject Circle',
        'group' => 'Data Benar',
        'steps' => "1. Login SPV atau KDP\n2. Pilih circle terkait\n3. Action: reject\n4. Note: Anggota belum sesuai ketentuan\n5. Klik Proses",
        'expected' => 'Status circle berubah REJECTED BY SPV atau REJECTED BY KDP sesuai jabatan approver.',
    ],
    [
        'code' => 'AQD005',
        'desc' => 'Validasi Approval Circle',
        'group' => 'Data Salah',
        'steps' => "1. Login sebagai karyawan selain SPV/KDP\n2. Buka halaman approval circle atau proses approval\n3. Isi action kosong",
        'expected' => 'Sistem menolak akses/proses karena tidak memiliki wewenang dan action wajib approve/reject.',
    ],
    [
        'code' => 'AQD006',
        'desc' => 'Search dan Paging Approval Progress QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login SPV/KDP\n2. Buka Approval QCC > Approve Progress\n3. Search: defect input\n4. Pilih per_page: 10",
        'expected' => 'Tema/progress departemen terkait tampil dan dapat dicari berdasarkan nama tema atau nama circle.',
    ],
    [
        'code' => 'AQD007',
        'desc' => 'Approve Progress QCC oleh SPV dan KDP',
        'group' => 'Data Benar',
        'steps' => "1. Login SPV dan pilih progress status WAITING SPV\n2. Action: approve, Note: Analisis sesuai template\n3. Klik Proses\n4. Login KDP dan pilih progress status WAITING KDP\n5. Action: approve, Note: Approved\n6. Klik Proses",
        'expected' => 'Setelah SPV approve status menjadi WAITING KDP; setelah KDP approve status menjadi APPROVED dan step berikutnya terbuka.',
    ],
    [
        'code' => 'AQD008',
        'desc' => 'Reject Progress QCC',
        'group' => 'Data Benar',
        'steps' => "1. Login SPV/KDP\n2. Pilih progress yang perlu revisi\n3. Action: reject\n4. Note: File belum memuat analisis akar masalah\n5. Klik Proses",
        'expected' => 'Status menjadi REJECTED BY SPV atau REJECTED BY KDP dan catatan tampil untuk perbaikan.',
    ],
]);

$addSeparator('RINCIAN TESTING ROLE ADMIN - DASHBOARD DAN MASTER SS', 'Kembali login sebagai Admin untuk pengujian dashboard QCC/SS, daftar ide, export PDF, master target, master scoring, dan reward.');

$addModule('DASHBOARD DAN MONITORING QCC - FILTER', [
    [
        'code' => 'DMQ001',
        'desc' => 'Filter Dashboard QCC Admin Level Company',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring QCC > Dashboard\n3. Pilih view_level: company\n4. Pilih period_id: Periode QCC 2026",
        'expected' => 'Dashboard menampilkan total circle, target circle, need review, completed, chart all company, timeline step, dan tanggal hari ini.',
    ],
    [
        'code' => 'DMQ002',
        'desc' => 'Filter Dashboard QCC Admin Level Division/Department/Circle',
        'group' => 'Data Benar',
        'steps' => "1. Pada Dashboard QCC pilih view_level: division dan division_code: DIV-PRD\n2. Ubah view_level: department dan department_code: PRD\n3. Ubah view_level: circle",
        'expected' => 'Grafik berubah mengikuti scope filter. Admin dapat melihat company/division/department/circle; SPV/KDP dibatasi departemennya.',
    ],
    [
        'code' => 'DMQ003',
        'desc' => 'Filter Master Schedule QCC',
        'group' => 'Data Benar',
        'steps' => "1. Buka Master Schedule\n2. Pilih period_id: Periode QCC 2026",
        'expected' => 'Gantt chart menampilkan step, tanggal mulai, tanggal selesai/deadline, dan warna step sesuai periode.',
    ],
    [
        'code' => 'DMQ004',
        'desc' => 'Filter All Circle Progress',
        'group' => 'Data Benar',
        'steps' => "1. Buka Monitoring QCC > All Circle Progress\n2. Pilih period_id: Periode QCC 2026\n3. Pilih division_code: DIV-PRD\n4. Pilih department_code: PRD\n5. Search: SIGITA\n6. Pilih per_page: 10",
        'expected' => 'Daftar circle terfilter berdasarkan periode/divisi/departemen/search dan paging mempertahankan filter.',
    ],
]);

$addModule('MASTER SS TARGET - SEARCH, PAGING, CRUD, VALIDASI', [
    [
        'code' => 'MSTG001',
        'desc' => 'Melihat, Search, dan Paging Master Target SS',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring SS > Master Target\n3. Search: PRD atau 2026\n4. Pilih per_page: 10 dan pindah halaman",
        'expected' => 'Target SS tampil dan dapat dicari berdasarkan nama departemen, kode departemen, atau tahun.',
    ],
    [
        'code' => 'MSTG002',
        'desc' => 'Tambah Target SS',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Target\n2. Year: 2026\n3. Month: 6\n4. Department Code: PRD\n5. Target Amount: 25\n6. Description: Target SS Juni departemen produksi\n7. Klik Simpan",
        'expected' => 'Target SS tersimpan untuk kombinasi tahun, bulan, dan departemen.',
    ],
    [
        'code' => 'MSTG003',
        'desc' => 'Validasi Target SS',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah Target\n2. Year: 1999\n3. Month: 13\n4. Department Code: kode tidak tersedia\n5. Target Amount: 0\n6. Klik Simpan",
        'expected' => 'Sistem menolak karena year harus 2000-2100, month 1-12, department harus valid, dan target minimal 1.',
    ],
    [
        'code' => 'MSTG004',
        'desc' => 'Validasi Duplikat Target SS',
        'group' => 'Data Salah',
        'steps' => "1. Tambahkan target dengan Year: 2026, Month: 6, Department Code: PRD yang sudah ada\n2. Klik Simpan",
        'expected' => 'Sistem menolak dengan pesan target untuk departemen pada bulan tersebut sudah ada.',
    ],
    [
        'code' => 'MSTG005',
        'desc' => 'Edit Target SS',
        'group' => 'Data Benar',
        'steps' => "1. Search: PRD\n2. Klik Edit target Juni 2026\n3. Ubah Target Amount: 30\n4. Description: Target SS Juni produksi revisi\n5. Klik Simpan",
        'expected' => 'Target amount dan deskripsi berhasil diperbarui.',
    ],
    [
        'code' => 'MSTG006',
        'desc' => 'Hapus Target SS',
        'group' => 'Data Benar',
        'steps' => "1. Search target PRD Juni 2026\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Target SS terhapus dari tabel.',
    ],
]);

$addModule('MASTER SS SCORING - SEARCH, PAGING, CRUD, VALIDASI', [
    [
        'code' => 'MSSC001',
        'desc' => 'Melihat, Search, dan Paging Master Scoring SS',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring SS > Master Scoring\n3. Search: Admin atau Ranking 1\n4. Pilih per_page: 20",
        'expected' => 'Range scoring tampil dan dapat dicari berdasarkan approver_level, deskripsi, atau ranking.',
    ],
    [
        'code' => 'MSSC002',
        'desc' => 'Tambah Range Scoring SS',
        'group' => 'Data Benar',
        'steps' => "1. Klik Tambah Scoring\n2. Min Score: 151\n3. Max Score: 200\n4. Ranking: 1\n5. Reward Amount: 500000\n6. Approver Level: Admin Komite\n7. Description: Reward tertinggi untuk improvement berdampak besar\n8. Extra Score Increment: 10\n9. Extra Reward Increment: 50000\n10. Is Active: centang\n11. Klik Simpan",
        'expected' => 'Range scoring aktif tersimpan jika tidak tumpang tindih dengan range aktif lain.',
    ],
    [
        'code' => 'MSSC003',
        'desc' => 'Validasi Range Scoring SS',
        'group' => 'Data Salah',
        'steps' => "1. Klik Tambah Scoring\n2. Min Score: 100\n3. Max Score: 90\n4. Ranking: 0\n5. Reward Amount: -1000\n6. Is Active: centang\n7. Klik Simpan",
        'expected' => 'Sistem menolak karena max_score harus lebih besar/sama min_score, ranking minimal 1, dan reward tidak boleh negatif.',
    ],
    [
        'code' => 'MSSC004',
        'desc' => 'Validasi Range Scoring Tumpang Tindih',
        'group' => 'Data Salah',
        'steps' => "1. Buat range aktif Min Score: 100, Max Score: 160\n2. Pastikan sudah ada range aktif 151-200\n3. Klik Simpan",
        'expected' => 'Sistem menolak dengan pesan range total nilai aktif tidak boleh tumpang tindih.',
    ],
    [
        'code' => 'MSSC005',
        'desc' => 'Edit Range Scoring SS',
        'group' => 'Data Benar',
        'steps' => "1. Search: Admin Komite\n2. Klik Edit\n3. Ubah Reward Amount: 600000\n4. Ubah Description: Reward tertinggi revisi\n5. Klik Simpan",
        'expected' => 'Range scoring berhasil diperbarui.',
    ],
    [
        'code' => 'MSSC006',
        'desc' => 'Hapus Range Scoring SS',
        'group' => 'Data Benar',
        'steps' => "1. Search: Reward tertinggi revisi\n2. Klik Hapus\n3. Konfirmasi hapus",
        'expected' => 'Range scoring terhapus dari master scoring.',
    ],
]);

$addModule('SS KARYAWAN - SEARCH, PAGING, PENGAJUAN, VALIDASI', [
    [
        'code' => 'SSKX001',
        'desc' => 'Melihat Daftar SS Saya/Dept',
        'group' => 'Data Benar',
        'steps' => "1. $akunKaryawan\n2. Buka menu SS Karyawan (/ss/karyawan)\n3. Pilih per_page: 10",
        'expected' => 'Karyawan biasa melihat SS miliknya; LDR melihat SS departemennya. Tabel menampilkan status, pengaju, file, dan aksi detail.',
    ],
    [
        'code' => 'SSKX002',
        'desc' => 'Search Daftar SS Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Pada Daftar SS Saya isi search: approved\n2. Ulangi search: nama pengaju atau catatan pengajuan\n3. Klik cari",
        'expected' => 'Daftar SS terfilter berdasarkan notes, status, atau nama karyawan.',
    ],
    [
        'code' => 'SSKX003',
        'desc' => 'Pengajuan SS Pribadi oleh LDR/SPV/KDP',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai LDR/SPV/KDP\n2. Buka Pengajuan SS\n3. Submission Type: self\n4. Idea Title: Mengurangi waktu input laporan SIGITA\n5. Idea Types: Quality dan Delivery\n6. Implemented Date: 2026-06-15\n7. Idea Location: Area produksi line A\n8. Before Condition: Input laporan masih manual dan berulang\n9. Cause: Belum ada validasi dan template input seragam\n10. Action: Menambahkan template upload dan validasi wajib\n11. Result: Waktu input turun 30 persen\n12. Standardization: Dibuat instruksi kerja baru\n13. Benefit: Proses lebih cepat dan data lebih akurat\n14. Benefit Amount: 1500000\n15. Implementation Status: sudah_dilaksanakan\n16. Upload file: form_ss_sigita.pdf\n17. Notes: Pengajuan user testing\n18. $nilaiSs\n19. Klik Submit",
        'expected' => 'SS tersimpan dengan idea_no otomatis. Untuk LDR status menjadi spv_review; untuk SPV/KDP alur awal mengikuti aturan approval di sistem.',
    ],
    [
        'code' => 'SSKX004',
        'desc' => 'Pengajuan SS untuk Operator oleh LDR',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai LDR\n2. Buka Pengajuan SS\n3. Submission Type: opr\n4. Pilih Employee NPK: 000190 (OPR di departemen yang sama)\n5. Isi data ide dan upload form PDF seperti kasus SSKX003\n6. Isi skor Leader lengkap\n7. Klik Submit",
        'expected' => 'SS operator tersimpan atas nama OPR, dinilai Leader, status menjadi spv_review, dan menunggu review SPV.',
    ],
    [
        'code' => 'SSKX005',
        'desc' => 'Validasi Pengajuan SS',
        'group' => 'Data Salah',
        'steps' => "1. Buka Pengajuan SS\n2. Submission Type kosong\n3. Untuk LDR pilih OPR dari departemen lain\n4. Upload file: form_ss.docx atau file lebih dari 5 MB\n5. Kosongkan skor Leader\n6. Klik Submit",
        'expected' => 'Sistem menolak karena submission_type wajib self/opr, OPR harus valid di departemen Leader, file harus PDF maksimal 5 MB, dan skor Leader wajib untuk LDR.',
    ],
    [
        'code' => 'SSKX006',
        'desc' => 'Melihat Detail SS Karyawan',
        'group' => 'Data Benar',
        'steps' => "1. Buka Daftar SS Saya/Dept\n2. Klik detail pada salah satu SS",
        'expected' => 'Detail SS tampil, termasuk data pengaju, file, status approval LDR/SPV/KDP/Admin, catatan, skor, reward, dan tanggal approval jika ada.',
    ],
]);

$addSeparator('RINCIAN TESTING ROLE KDP DAN KOMITE ADMIN', 'Skenario KDP dijalankan setelah data lolos SPV. Jika skor membutuhkan review komite, lanjutkan login Admin untuk review komite dan finalisasi reward.');

$addModule('APPROVAL SS - SPV, KDP, ADMIN, VALIDASI SKOR', [
    [
        'code' => 'ASSX001',
        'desc' => 'Search dan Paging Review SS SPV',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai SPV\n2. Buka /ss/approval/spv\n3. Search: 000190 atau nama pengaju\n4. Pilih per_page: 10",
        'expected' => 'Daftar SS berstatus spv_review pada departemen SPV tampil dan dapat dicari/paging.',
    ],
    [
        'code' => 'ASSX002',
        'desc' => 'Approve Review SS oleh SPV',
        'group' => 'Data Benar',
        'steps' => "1. Pilih SS status spv_review\n2. Buka form review\n3. Action: approved\n4. Supervisor Decision: perlu_dilakukan\n5. Standard Review: meningkatkan_std\n6. SPV Notes: Layak dilanjutkan\n7. Supervisor Reason: Dampak improvement jelas\n8. $nilaiSs\n9. Klik Simpan",
        'expected' => 'Status SS berubah menjadi kdp_review, skor SPV tersimpan, dan catatan SPV tampil pada detail.',
    ],
    [
        'code' => 'ASSX003',
        'desc' => 'Reject Review SS oleh SPV',
        'group' => 'Data Benar',
        'steps' => "1. Pilih SS status spv_review\n2. Action: rejected\n3. Supervisor Decision: ditolak\n4. SPV Notes: Data manfaat belum lengkap\n5. Klik Simpan",
        'expected' => 'Status SS berubah rejected, final score/reward dikosongkan, dan pengajuan tidak lanjut ke KDP.',
    ],
    [
        'code' => 'ASSX004',
        'desc' => 'Validasi Review SS SPV',
        'group' => 'Data Salah',
        'steps' => "1. Buka form review SPV\n2. Kosongkan Action dan Supervisor Decision\n3. Isi skor Safety: 99\n4. Klik Simpan",
        'expected' => 'Sistem menolak karena action wajib approved/rejected, keputusan SPV wajib, dan skor tidak boleh melebihi maksimum kriteria.',
    ],
    [
        'code' => 'ASSX005',
        'desc' => 'Search dan Paging Review SS KDP',
        'group' => 'Data Benar',
        'steps' => "1. Login sebagai KDP\n2. Buka /ss/approval/kdp\n3. Search: 000190\n4. Pilih per_page: 10",
        'expected' => 'Daftar SS berstatus kdp_review pada departemen KDP tampil dan dapat dicari/paging.',
    ],
    [
        'code' => 'ASSX006',
        'desc' => 'Approve Review SS oleh KDP',
        'group' => 'Data Benar',
        'steps' => "1. Pilih SS status kdp_review\n2. Action: approved\n3. KDP Notes: Disetujui oleh KDP\n4. Jika sistem meminta scoring, isi skor lengkap sesuai kriteria\n5. Klik Simpan",
        'expected' => 'Jika skor perlu review admin maka status menjadi admin_review; jika tidak, status menjadi approved dan calculated_reward_amount terisi otomatis dari master scoring.',
    ],
    [
        'code' => 'ASSX007',
        'desc' => 'Reject Review SS oleh KDP',
        'group' => 'Data Benar',
        'steps' => "1. Pilih SS status kdp_review\n2. Action: rejected\n3. KDP Notes: Perlu perbaikan bukti manfaat\n4. Klik Simpan",
        'expected' => 'Status SS berubah rejected dan reward dikosongkan.',
    ],
    [
        'code' => 'ASSX008',
        'desc' => 'Search dan Paging Review Komite/Admin',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring SS > Review Komite\n3. Search: 000190 atau PRD\n4. Pilih per_page: 10",
        'expected' => 'Daftar SS berstatus admin_review tampil dan dapat dicari berdasarkan nama, NPK, atau departemen.',
    ],
    [
        'code' => 'ASSX009',
        'desc' => 'Approve Review Komite/Admin',
        'group' => 'Data Benar',
        'steps' => "1. Pilih SS status admin_review\n2. Action: approved\n3. Admin Notes: Disetujui komite\n4. $nilaiSs\n5. Klik Simpan",
        'expected' => 'Status SS menjadi approved, final_score tersimpan, final_approved_at terisi, calculated_reward_amount dihitung dari master scoring.',
    ],
    [
        'code' => 'ASSX010',
        'desc' => 'Reject Review Komite/Admin',
        'group' => 'Data Benar',
        'steps' => "1. Pilih SS status admin_review\n2. Action: rejected\n3. Admin Notes: Tidak memenuhi kriteria komite\n4. Klik Simpan",
        'expected' => 'Status SS menjadi rejected dan final score/reward dikosongkan.',
    ],
]);

$addModule('MONITORING SS - DASHBOARD, DAFTAR IDE, FILTER, EXPORT, REWARD', [
    [
        'code' => 'MSSX001',
        'desc' => 'Filter Dashboard SS Admin Level Company',
        'group' => 'Data Benar',
        'steps' => "1. $akun\n2. Buka Monitoring SS > Dashboard SS\n3. View Level: company\n4. Year: 2026\n5. Month: 6",
        'expected' => 'Dashboard menampilkan target_ss, total_ss, actual_today, need_review, completed, dan chart target/submitted/approved sesuai tahun dan bulan.',
    ],
    [
        'code' => 'MSSX002',
        'desc' => 'Filter Dashboard SS Division/Department',
        'group' => 'Data Benar',
        'steps' => "1. Pada Dashboard SS pilih View Level: division\n2. Division Code: DIV-PRD\n3. Ubah View Level: department\n4. Department Code: PRD",
        'expected' => 'Grafik dan statistik berubah sesuai divisi/departemen. GMR dibatasi division, SPV/KDP dibatasi department.',
    ],
    [
        'code' => 'MSSX003',
        'desc' => 'Filter Daftar Ide SS',
        'group' => 'Data Benar',
        'steps' => "1. Buka Monitoring SS > Daftar Ide\n2. Status: approved\n3. Department Code: PRD\n4. Year: 2026\n5. Month: 6\n6. Date From: 2026-06-01\n7. Date To: 2026-06-30\n8. Search: Mengurangi waktu input\n9. Pilih per_page: 20",
        'expected' => 'Daftar pengajuan SS terfilter berdasarkan status, departemen, tanggal/bulan/tahun, dan search pada nama/NPK/dept/judul ide. Paging mempertahankan filter.',
    ],
    [
        'code' => 'MSSX004',
        'desc' => 'Melihat Detail Daftar Ide SS',
        'group' => 'Data Benar',
        'steps' => "1. Dari Daftar Ide klik detail pada salah satu SS\n2. Periksa file dan riwayat approval",
        'expected' => 'Detail SS tampil lengkap untuk admin termasuk file, pengaju, LDR/SPV/KDP/Admin, skor, status, catatan, dan reward.',
    ],
    [
        'code' => 'MSSX005',
        'desc' => 'Export PDF Daftar Ide SS sesuai Filter',
        'group' => 'Data Benar',
        'steps' => "1. Aktifkan filter status: approved, department: PRD, tanggal 2026-06-01 s.d. 2026-06-30\n2. Klik Export PDF",
        'expected' => 'Sistem mengunduh file daftar-ss-YYYYMMDD-HHMMSS.pdf berisi data sesuai filter aktif.',
    ],
    [
        'code' => 'MSSX006',
        'desc' => 'Input Reward SS',
        'group' => 'Data Benar',
        'steps' => "1. Dari Daftar Ide pilih SS status approved\n2. Klik Reward\n3. Reward Amount: 500000\n4. Klik Simpan",
        'expected' => 'Reward tersimpan, paid_at terisi, dan status SS berubah menjadi rewarded.',
    ],
    [
        'code' => 'MSSX007',
        'desc' => 'Validasi Reward SS',
        'group' => 'Data Salah',
        'steps' => "1. Pilih SS yang statusnya belum approved atau sudah rewarded\n2. Coba buka/input reward\n3. Isi Reward Amount: -1\n4. Klik Simpan",
        'expected' => 'Sistem menolak reward untuk status selain approved, menolak reward yang sudah dibayar, dan reward amount tidak boleh negatif.',
    ],
]);

// ==================== LOGOUT ====================
$addModule('LOGOUT', [
    [
        'code' => 'LO001',
        'desc' => 'Melakukan Logout',
        'group' => 'Data Benar',
        'steps' => "1. Login ke sistem SIGITA\n2. Klik ikon logout di header kanan atas",
        'expected' => 'Session berakhir dan user diarahkan kembali ke halaman login',
    ],
]);

// ---- Build Word XML ----
function xmlEscape(string $text): string
{
    return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function cellText(string $text, bool $bold = false): string
{
    $lines = array_filter(explode("\n", $text), fn ($l) => $l !== '');
    $boldTag = $bold ? '<w:b/>' : '';
    $paras = '';
    foreach ($lines as $line) {
        $paras .= '<w:p><w:pPr><w:spacing w:after="40" w:line="240" w:lineRule="auto"/></w:pPr>'
            . '<w:r><w:rPr>' . $boldTag . '<w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr>'
            . '<w:t xml:space="preserve">' . xmlEscape($line) . '</w:t></w:r></w:p>';
    }
    if ($paras === '') {
        $paras = '<w:p/>';
    }
    return '<w:tc><w:tcPr><w:tcW w:w="1200" w:type="dxa"/></w:tcPr>' . $paras . '</w:tc>';
}

function headerCell(string $text): string
{
    return '<w:tc><w:tcPr><w:tcW w:w="1200" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="D9E2F3"/></w:tcPr><w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . xmlEscape($text) . '</w:t></w:r></w:p></w:tc>';
}

function separatorRow(string $title, string $description = ''): string
{
    $text = $description ? $title . "\n" . $description : $title;
    $lines = explode("\n", $text);
    $paras = '';
    foreach ($lines as $index => $line) {
        $boldTag = $index === 0 ? '<w:b/>' : '';
        $size = $index === 0 ? 22 : 18;
        $paras .= '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="40"/></w:pPr>'
            . '<w:r><w:rPr>' . $boldTag . '<w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/></w:rPr>'
            . '<w:t xml:space="preserve">' . xmlEscape($line) . '</w:t></w:r></w:p>';
    }

    return '<w:tr><w:tc><w:tcPr><w:gridSpan w:val="10"/><w:shd w:val="clear" w:color="auto" w:fill="B4C6E7"/>'
        . '<w:tcBorders><w:top w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
        . '<w:left w:val="single" w:sz="4" w:space="0" w:color="000000"/>'
        . '<w:bottom w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
        . '<w:right w:val="single" w:sz="4" w:space="0" w:color="000000"/></w:tcBorders></w:tcPr>'
        . $paras . '</w:tc></w:tr>';
}

function para(string $text, bool $bold = false, string $align = 'left', int $size = 22): string
{
    $boldTag = $bold ? '<w:b/>' : '';
    $alignTag = $align !== 'left' ? '<w:jc w:val="' . $align . '"/>' : '';
    return '<w:p><w:pPr>' . $alignTag . '<w:spacing w:after="120"/></w:pPr><w:r><w:rPr>' . $boldTag . '<w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/></w:rPr><w:t xml:space="preserve">' . xmlEscape($text) . '</w:t></w:r></w:p>';
}

$body = '';
$body .= para('DOKUMEN RINCI TESTING', true, 'center', 28);
$body .= para('SISTEM INTEGRASI GAGASAN, INOVASI, DAN TINDAK LANJUT AIIA (SIGITA)', true, 'center', 24);
$body .= para('PT AISIN INDONESIA AUTOMOTIVE', true, 'center', 24);
$body .= para('KELOMPOK 63', true, 'center', 24);
$body .= para('Dean Ichsanul Fiqri', false, 'center', 22);
$body .= para('', false);
$body .= para('TIM PENGUJI', true, 'center', 22);
$body .= para('Luthfi Atikah, S.Kom., M.Kom.', false, 'center');
$body .= para('Imam Mahfud, S.ST', false, 'center');
$body .= para('', false);
$body .= para('HASIL RINCI PENGUJIAN', true, 'center', 24);
$body .= para('', false);

// Table header
$table = '<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/><w:tblBorders>
<w:top w:val="single" w:sz="4" w:space="0" w:color="000000"/>
<w:left w:val="single" w:sz="4" w:space="0" w:color="000000"/>
<w:bottom w:val="single" w:sz="4" w:space="0" w:color="000000"/>
<w:right w:val="single" w:sz="4" w:space="0" w:color="000000"/>
<w:insideH w:val="single" w:sz="4" w:space="0" w:color="000000"/>
<w:insideV w:val="single" w:sz="4" w:space="0" w:color="000000"/>
</w:tblBorders></w:tblPr><w:tr>';
$headers = ['NO', 'NO FUNGSI', 'DESKRIPSI FUNGSIONAL', 'KELOMPOK UJI', 'PROSEDUR & KASUS UJI', 'HASIL YANG DIHARAPKAN', 'HASIL TEST', 'TESTER', 'TGL TEST', 'KETERANGAN'];
foreach ($headers as $h) {
    $table .= headerCell($h);
}
$table .= '</w:tr>';

$no = 1;
foreach ($rows as $row) {
    if (!empty($row['separator'])) {
        $table .= separatorRow($row['title'], $row['description'] ?? '');
        continue;
    }

    $table .= '<w:tr>';
    $table .= cellText((string) $no++);
    $noFungsi = $row['module']
        ? $row['module'] . "\n" . $row['no_fungsi']
        : $row['no_fungsi'];
    $table .= cellText($noFungsi, (bool) $row['module']);
    $table .= cellText($row['deskripsi']);
    $table .= cellText($row['kelompok']);
    $table .= cellText($row['prosedur']);
    $table .= cellText($row['harapan']);
    $table .= cellText(''); // HASIL TEST
    $table .= cellText(''); // TESTER
    $table .= cellText(''); // TGL TEST
    $table .= cellText(''); // KETERANGAN
    $table .= '</w:tr>';
}
$table .= '</w:tbl>';

$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
 xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<w:body>' . $body . $table . '<w:sectPr><w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/><w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720"/></w:sectPr></w:body></w:document>';

// Copy template and replace document.xml
if (!copy($templatePath, $tempOutputPath)) {
    fwrite(STDERR, "Gagal menyalin template.\n");
    exit(1);
}

$zip = new ZipArchive();
if ($zip->open($tempOutputPath) !== true) {
    fwrite(STDERR, "Gagal membuka output docx.\n");
    exit(1);
}
$zip->addFromString('word/document.xml', $documentXml);
$zip->close();

if (file_exists($outputPath) && !unlink($outputPath)) {
    fwrite(STDERR, "Gagal mengganti output lama.\n");
    exit(1);
}

if (!rename($tempOutputPath, $outputPath)) {
    fwrite(STDERR, "Gagal menyimpan output final.\n");
    exit(1);
}

echo "Berhasil membuat: $outputPath\n";
echo "Total kasus uji: " . count($rows) . "\n";
