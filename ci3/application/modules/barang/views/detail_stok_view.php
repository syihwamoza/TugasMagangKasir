<style>
    .tbl-res { width: 100%; border-collapse: collapse; font-size: 14px; background-color: #ffffff; color: #2c3e50; }
    .tbl-res th { background: #f4f7f6; color: #2c3e50; padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; }
    .tbl-res td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; color: #2c3e50; }
    .badge-plus { background: #eafaf1; color: #27ae60; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    .badge-min { background: #fef4f4; color: #e74c3c; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
</style>

<table class="tbl-res">
    <thead>
        <tr>
            <th>ID</th>
            <th>Waktu (Tgl & Jam)</th>
            <th style="text-align:left;">Aktivitas</th>
            <th>Masuk (+)</th>
            <th>Keluar (-)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($detail)): ?>
            <?php foreach ($detail as $row): ?>
            <tr>
                <td style="font-weight: bold; font-size: 11px;">#<?= $row['id'] ?></td>
                <td style="color: #666; font-size: 12px;"><?= date('d/m/Y H:i', strtotime($row['tgl'])) ?></td>
                <td style="text-align:left; font-weight: 500;"><?= $row['ket'] ?></td>
                <td><?= ($row['masuk'] > 0) ? '<span class="badge-plus">+' . $row['masuk'] . '</span>' : '-' ?></td>
                <td><?= ($row['keluar'] > 0) ? '<span class="badge-min">-' . $row['keluar'] . '</span>' : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan='4' style='padding:40px; color:#999;'>Belum ada aktivitas stok untuk barang ini.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
