<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>
<?php include 'views/layout/navbar.php'; ?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('index.php?page=dashboard') ?>" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Cetak Laporan</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold mb-0">Cetak Laporan Arsip SP2D</h4>
    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKop">
        <i class="fa-solid fa-pen-to-square me-1"></i> Pengaturan Kop Laporan
    </button>
</div>

<?php if(isset($_SESSION['flash_msg'])): ?>
    <div id="flash-data" data-type="<?= $_SESSION['flash_type'] ?>" data-msg="<?= $_SESSION['flash_msg'] ?>"></div>
    <?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= base_url('index.php') ?>" method="GET" target="_blank">
                    <input type="hidden" name="page" id="page_input" value="laporan_cetak">
                    
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex align-items-center mb-4">
                        <i class="fa-solid fa-circle-info fa-2x text-info me-3"></i>
                        <div>
                            Silakan atur filter di bawah ini untuk menyesuaikan data arsip SP2D yang ingin Anda cetak. Halaman cetak akan terbuka di tab baru.
                        </div>
                    </div>
                    
                    <h6 class="text-primary mb-2"><i class="fa-solid fa-calendar-days me-2"></i>Periode SP2D (Wajib)</h6>
                    
                    <div class="mb-3">
                        <span class="text-muted small fw-semibold me-2">Pilih Cepat:</span>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill me-1" onclick="setPeriode('mingguan')">Minggu Ini</button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill me-1" onclick="setPeriode('bulanan')">Bulan Ini</button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" onclick="setPeriode('tahunan')">Tahun Ini</button>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Dari Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_awal" id="tgl_awal" required value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Sampai Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_akhir" id="tgl_akhir" required value="<?= date('Y-m-t') ?>">
                        </div>
                    </div>

                    <h6 class="text-primary mb-3"><i class="fa-solid fa-filter me-2"></i>Filter Tambahan (Opsional)</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Instansi / SKPD</label>
                            <select class="form-select" name="id_instansi">
                                <option value="">-- Semua Instansi --</option>
                                <?php foreach($instansiList as $i): ?>
                                    <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['nama_instansi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status Berkas</label>
                            <select class="form-select" name="status">
                                <option value="">-- Semua Status --</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Dikembalikan">Dikembalikan</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4 py-2" onclick="document.getElementById('page_input').value='laporan_excel'">
                            <i class="fa-solid fa-file-excel me-2"></i> Export Excel
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2" onclick="document.getElementById('page_input').value='laporan_cetak'">
                            <i class="fa-solid fa-print me-2"></i> Generate & Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengaturan KOP Laporan -->
<div class="modal fade" id="modalKop" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-secondary text-white border-bottom-0">
        <h5 class="modal-title"><i class="fa-solid fa-pen-to-square me-2"></i> Pengaturan Kop Laporan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('index.php?page=laporan&action=update_kop') ?>" method="POST">
      <div class="modal-body p-4">
        <div class="mb-3">
            <label class="form-label fw-semibold">Baris 1 (Pemerintah/Nama Instansi Induk)</label>
            <input type="text" class="form-control" name="kop_baris1" value="<?= htmlspecialchars($pengaturan['kop_baris1'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Baris 2 (Nama Bidang/SKPD Lengkap)</label>
            <input type="text" class="form-control" name="kop_baris2" value="<?= htmlspecialchars($pengaturan['kop_baris2'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Baris 3 (Alamat Lengkap & Telp)</label>
            <textarea class="form-control" name="kop_alamat" rows="2" required><?= htmlspecialchars($pengaturan['kop_alamat'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Baris 4 (Email / Website)</label>
            <input type="text" class="form-control" name="kop_email" value="<?= htmlspecialchars($pengaturan['kop_email'] ?? '') ?>" required>
        </div>
        
        <h6 class="text-primary fw-bold mt-4 mb-3 border-bottom pb-2">Bagian Tanda Tangan (Bawah)</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Lokasi TTD (Contoh: Jakarta)</label>
                <input type="text" class="form-control" name="ttd_lokasi" value="<?= htmlspecialchars($pengaturan['ttd_lokasi'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Jabatan Pejabat</label>
                <input type="text" class="form-control" name="ttd_jabatan" value="<?= htmlspecialchars($pengaturan['ttd_jabatan'] ?? '') ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Nama Pejabat</label>
                <input type="text" class="form-control" name="ttd_nama" value="<?= htmlspecialchars($pengaturan['ttd_nama'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">NIP Pejabat</label>
                <input type="text" class="form-control" name="ttd_nip" value="<?= htmlspecialchars($pengaturan['ttd_nip'] ?? '') ?>" required>
            </div>
        </div>
      </div>
      <div class="modal-footer border-top-0 bg-light">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-secondary">Simpan Pengaturan</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php 
$custom_scripts = "
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flashData = document.getElementById('flash-data');
    if(flashData) {
        Swal.fire({
            icon: flashData.dataset.type,
            title: flashData.dataset.type === 'success' ? 'Berhasil!' : 'Gagal!',
            text: flashData.dataset.msg,
            confirmButtonColor: '#4361ee'
        });
    }
});

function setPeriode(type) {
    const tglAwal = document.getElementById('tgl_awal');
    const tglAkhir = document.getElementById('tgl_akhir');
    const now = new Date();
    let start, end;

    if (type === 'mingguan') {
        const day = now.getDay() || 7; 
        if(day !== 1) now.setHours(-24 * (day - 1));
        start = new Date(now);
        end = new Date(start);
        end.setDate(start.getDate() + 6);
    } else if (type === 'bulanan') {
        start = new Date(now.getFullYear(), now.getMonth(), 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    } else if (type === 'tahunan') {
        start = new Date(now.getFullYear(), 0, 1);
        end = new Date(now.getFullYear(), 11, 31);
    }

    const formatDate = (date) => {
        let d = new Date(date), month = '' + (d.getMonth() + 1), day = '' + d.getDate(), year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    };

    tglAwal.value = formatDate(start);
    tglAkhir.value = formatDate(end);
}
</script>
";
?>

<?php include 'views/layout/footer.php'; ?>
