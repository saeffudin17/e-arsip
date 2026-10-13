<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `kegiatan` (
      `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
      `jenis_kegiatan` VARCHAR(100) NOT NULL,
      `nama_kegiatan` VARCHAR(255) NOT NULL,
      `status_aktif` ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (Exception $e) {}

try {
    $pdo->exec("ALTER TABLE arsip_sp2d ADD COLUMN jumlah_halaman INT(11) DEFAULT 0 AFTER id_rak");
} catch (Exception $e) {}
try {
    $pdo->exec("ALTER TABLE arsip_sp2d ADD COLUMN jumlah_sp2d INT(11) DEFAULT 0 AFTER jumlah_halaman");
} catch (Exception $e) {}
try {
    $pdo->exec("ALTER TABLE arsip_sp2d ADD COLUMN nomor_spm VARCHAR(100) NULL AFTER nomor_sp2d");
} catch (Exception $e) {}
try {
    $pdo->exec("ALTER TABLE arsip_sp2d ADD COLUMN tanggal_spm DATE NULL AFTER nomor_spm");
} catch (Exception $e) {}

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN foto VARCHAR(255) DEFAULT 'default.png' AFTER status");
} catch (Exception $e) {}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `pengaturan` (
      `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
      `kunci` VARCHAR(100) UNIQUE NOT NULL,
      `nilai` TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    $pdo->exec("INSERT IGNORE INTO pengaturan (kunci, nilai) VALUES 
        ('kop_baris1', 'PEMERINTAH KABUPATEN / KOTA ...'),
        ('kop_baris2', 'BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH'),
        ('kop_alamat', 'Jl. Contoh Alamat No. 123, Telp. (021) 1234567, Fax (021) 7654321'),
        ('kop_email', 'Email: bpkad@contoh.go.id, Website: www.contoh.go.id'),
        ('ttd_lokasi', 'Jakarta'),
        ('ttd_jabatan', 'Kepala Sub Bagian Kearsipan'),
        ('ttd_nama', 'NAMA PEJABAT'),
        ('ttd_nip', '19800101 200501 1 001')");
} catch (Exception $e) {}

$page = $_GET['page'] ?? 'dashboard';

// ============================================
// MIDDLEWARE LOGIN
// ============================================
// Halaman yang bisa diakses tanpa login
$public_pages = ['login', 'login_process'];

if (!isset($_SESSION['user_id']) && !in_array($page, $public_pages)) {
    header("Location: " . base_url('index.php?page=login'));
    exit;
}

// Jika sudah login, tidak boleh akses halaman login lagi
if (isset($_SESSION['user_id']) && in_array($page, $public_pages)) {
    header("Location: " . base_url('index.php?page=dashboard'));
    exit;
}

// ============================================
// ACL (ACCESS CONTROL LIST) - SECURITY
// ============================================
$role = $_SESSION['user_role'] ?? '';

// Definisi hak akses (whitelist) untuk masing-masing role
$acl = [
    'Admin' => [
        'dashboard', 'arsip_list', 'arsip_input', 'arsip_detail', 'arsip_action',
        'instansi', 'instansi_action', 'rak', 'rak_action', 'bindex', 'bindex_action', 'kegiatan', 'kegiatan_action',
        'laporan', 'laporan_cetak', 'laporan_excel',
        'users', 'users_action', 'logs', 'recycle_bin', 'recycle_bin_action', 'backup_restore', 'backup_action',
        'profile', 'change_password', 'profile_action'
    ],
    'Operator' => [
        'dashboard', 'arsip_list', 'arsip_input', 'arsip_detail', 'arsip_action',
        'instansi', 'rak', 'bindex', 'kegiatan', // Hanya View
        'laporan', 'laporan_cetak', 'laporan_excel',
        'profile', 'change_password', 'profile_action'
    ],
    'Reviewer' => [
        'dashboard', 'arsip_list', 'arsip_detail', 'arsip_action', // Action untuk review status
        'laporan', 'laporan_cetak', 'laporan_excel',
        'profile', 'change_password', 'profile_action'
    ],
    'Management' => [
        'dashboard', 'arsip_list', 'arsip_detail',
        'laporan', 'laporan_cetak', 'laporan_excel', // Management untuk melihat laporan dan view arsip
        'profile', 'change_password', 'profile_action'
    ],
    'Direktur' => [
        'dashboard', 'arsip_list', 'arsip_detail',
        'instansi', 'rak', 'bindex', 'kegiatan', // Hanya View
        'laporan', 'laporan_cetak', 'laporan_excel',
        'logs',
        'profile', 'change_password', 'profile_action'
    ]
];

// Lakukan pengecekan jika halaman bukan milik publik
if (!in_array($page, $public_pages) && $page !== 'logout') {
    if (!isset($acl[$role]) || !in_array($page, $acl[$role])) {
        // Blokir akses dan kembalikan ke dashboard (atau halaman default)
        $_SESSION['flash_msg'] = "Akses Ditolak! Role Anda ($role) tidak memiliki izin ke halaman ini.";
        $_SESSION['flash_type'] = "error";
        
        // Cegah redirect loop jika dashboard juga diblokir
        if ($page === 'dashboard') {
            die("Fatal Error: Role tidak memiliki akses ke Dashboard.");
        }
        
        header("Location: " . base_url('index.php?page=dashboard'));
        exit;
    }
}

// ============================================
// ROUTING SEDERHANA
// ============================================
switch ($page) {
    case 'login':
        require 'views/auth/login.php';
        break;

    case 'login_process':
        require 'controllers/AuthController.php';
        break;

    case 'logout':
        // Log Activity Logout
        if (isset($_SESSION['user_id'])) {
            $logStmt = $pdo->prepare("INSERT INTO activity_logs (id_user, action, description) VALUES (:id_user, 'Logout', 'User berhasil logout')");
            $logStmt->execute([':id_user' => $_SESSION['user_id']]);
        }
        
        session_destroy();
        session_start();
        $_SESSION['success_logout'] = 'Anda berhasil keluar.';
        header("Location: " . base_url('index.php?page=login'));
        break;

    case 'dashboard':
        require 'controllers/DashboardController.php';
        break;

    // MASTER DATA INSTANSI
    case 'instansi':
    case 'instansi_action':
        require 'controllers/InstansiController.php';
        break;

    // MASTER DATA RAK
    case 'rak':
    case 'rak_action':
        require 'controllers/RakController.php';
        break;

    // MASTER DATA BINDEX
    case 'bindex':
    case 'bindex_action':
        require 'controllers/BindexController.php';
        break;
        
    case 'kegiatan':
    case 'kegiatan_action':
        require 'controllers/KegiatanController.php';
        break;

    // PENGELOLAAN ARSIP
    case 'arsip_list':
    case 'arsip_input':
    case 'arsip_detail':
    case 'arsip_action':
        require 'controllers/ArsipController.php';
        break;



    // LAPORAN
    case 'laporan':
    case 'laporan_cetak':
    case 'laporan_excel':
        require 'controllers/LaporanController.php';
        break;

    // SISTEM & PENGATURAN
    case 'logs':
    case 'logs_action':
        require 'controllers/LogController.php';
        break;

    case 'recycle_bin':
    case 'recycle_bin_action':
        require 'controllers/RecycleBinController.php';
        break;

    case 'users':
    case 'users_action':
        require 'controllers/UserController.php';
        break;

    case 'profile':
    case 'change_password':
    case 'profile_action':
        require 'controllers/ProfileController.php';
        break;

    case 'backup_restore':
    case 'backup_action':
        require 'controllers/BackupController.php';
        break;

    default:
        http_response_code(404);
        require 'views/errors/404.php';
        break;
}
