<?php
/**
 * Generator: User Testing SIGITA (format mengikuti User Testing_63.docx)
 * Jalankan: php generate_user_testing_sigita.php
 */

$templatePath = __DIR__ . '/User Testing_63.docx';
$outputPath = __DIR__ . '/User Testing_SIGITA.docx';

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
if (!copy($templatePath, $outputPath)) {
    fwrite(STDERR, "Gagal menyalin template.\n");
    exit(1);
}

$zip = new ZipArchive();
if ($zip->open($outputPath) !== true) {
    fwrite(STDERR, "Gagal membuka output docx.\n");
    exit(1);
}
$zip->addFromString('word/document.xml', $documentXml);
$zip->close();

echo "Berhasil membuat: $outputPath\n";
echo "Total kasus uji: " . count($rows) . "\n";
