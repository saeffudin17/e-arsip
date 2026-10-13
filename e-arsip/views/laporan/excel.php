<?php
// Header untuk force download sebagai file Excel (.xls)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Arsip_SP2D_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export Excel</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="11" style="font-size: 16px; font-weight: bold; text-align: left;">Daftar Arsip Tahun <?= date('Y', strtotime($tgl_awal)) ?></th>
            </tr>
            <tr>
                <th colspan="11"></th>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: center;">NO</th>
                <th style="font-weight: bold; text-align: center;">SKPD</th>
                <th style="font-weight: bold; text-align: center;">NO. BINDEX</th>
                <th style="font-weight: bold; text-align: center;">NO. SPM</th>
                <th style="font-weight: bold; text-align: center;">TGL SPM</th>
                <th style="font-weight: bold; text-align: center;">NO. SP2D</th>
                <th style="font-weight: bold; text-align: center;">TGL SP2D</th>
                <th style="font-weight: bold; text-align: center;">KEPERLUAN UNTUK</th>
                <th style="font-weight: bold; text-align: center;">ADDRESS</th>
                <th style="font-weight: bold; text-align: center;">JUMLAH HALAMAN</th>
                <th style="font-weight: bold; text-align: center;">JML SP2D</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Grouping logic by SKPD (Instansi)
            $groupedData = [];
            $grand_total_halaman = 0;
            $grand_total_sp2d = 0;
            
            foreach($dataLaporan as $row) {
                $instansi = $row['nama_instansi'] ?? 'Tanpa Instansi';
                if(!isset($groupedData[$instansi])) {
                    $groupedData[$instansi] = [
                        'total_halaman' => 0,
                        'total_sp2d' => 0,
                        'items' => []
                    ];
                }
                $groupedData[$instansi]['items'][] = $row;
                $groupedData[$instansi]['total_halaman'] += (int)($row['jumlah_halaman'] ?? 0);
                $groupedData[$instansi]['total_sp2d'] += (int)($row['jumlah_sp2d'] ?? 0);
                
                $grand_total_halaman += (int)($row['jumlah_halaman'] ?? 0);
                $grand_total_sp2d += (int)($row['jumlah_sp2d'] ?? 0);
            }
            
            $skpd_no = 1;
            foreach($groupedData as $instansi => $group):
            ?>
            <tr>
                <td style="text-align: center; font-weight: bold;"><?= $skpd_no++ ?></td>
                <td style="font-weight: bold;"><?= htmlspecialchars($instansi) ?></td>
                <td style="font-weight: bold;">No. BINDEX</td>
                <td style="font-weight: bold;">NO. SPM</td>
                <td style="font-weight: bold;">Tgl. SPM</td>
                <td style="font-weight: bold;">No. SP2D</td>
                <td style="font-weight: bold;">Tgl. SP2D</td>
                <td style="font-weight: bold;">KEPERLUAN UNTUK</td>
                <td style="font-weight: bold;">BROWSE</td>
                <td style="font-weight: bold; text-align: center;"><?= $group['total_halaman'] ?></td>
                <td style="font-weight: bold; text-align: center;"><?= $group['total_sp2d'] ?></td>
            </tr>
            <tr>
                <td colspan="11"></td>
            </tr>
            <?php foreach($group['items'] as $item): ?>
            <tr>
                <td></td>
                <td></td>
                <td><?= htmlspecialchars($item['nomor_bindex'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['nomor_spm'] ?? '') ?></td>
                <td><?= !empty($item['tanggal_spm']) ? date('d-m-Y', strtotime($item['tanggal_spm'])) : '' ?></td>
                <td><?= htmlspecialchars($item['nomor_sp2d'] ?? '') ?></td>
                <td><?= !empty($item['tanggal_sp2d']) ? date('d-m-Y', strtotime($item['tanggal_sp2d'])) : '' ?></td>
                <td><?= htmlspecialchars($item['nama_kegiatan'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['file_pdf'] ?? '') ?></td>
                <td style="text-align: center;"><?= htmlspecialchars($item['jumlah_halaman'] ?? '') ?></td>
                <td style="text-align: center;"><?= htmlspecialchars($item['jumlah_sp2d'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="9" style="font-weight: bold; text-align: right;">TOTAL HALAMAN</td>
                <td style="font-weight: bold; text-align: center;"><?= $grand_total_halaman ?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="9" style="font-weight: bold; text-align: right;">TOTAL SP2D</td>
                <td></td>
                <td style="font-weight: bold; text-align: center;"><?= $grand_total_sp2d ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
