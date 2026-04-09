<?php
require_once '../config/koneksi.php';
require_admin();

$sql = "SELECT l.*, a.username FROM activity_logs l 
        JOIN admin a ON l.id_admin = a.id_admin 
        ORDER BY l.created_at DESC LIMIT 100";
$data = $koneksi->query($sql);
?>
<table class="v-table">
    <thead>
        <tr>
            <th>Waktu</th>
            <th>Admin</th>
            <th>Aksi</th>
            <th>Deskripsi</th>
            <th>IP Address</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $data->fetch_assoc()): ?>
        <tr>
            <td style="font-size:.8rem;color:var(--v-muted);"><?php echo $row['created_at']; ?></td>
            <td><strong><?php echo $row['username']; ?></strong></td>
            <td><span class="v-badge v-badge-ps4"><?php echo $row['aksi']; ?></span></td>
            <td style="color:var(--v-white);"><?php echo $row['deskripsi']; ?></td>
            <td style="font-family:monospace; font-size:.8rem;"><?php echo $row['ip_address']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>