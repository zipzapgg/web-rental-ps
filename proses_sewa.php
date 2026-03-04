<?php
include 'config/koneksi.php';

if(isset($_POST['kirim'])){
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $wa      = $_POST['wa'];
    $alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $id_unit = $_POST['id_unit'];
    $durasi  = $_POST['durasi'];
    $tgl     = date('Y-m-d H:i:s');

    // Proses Upload KTP & STNK
    $folder  = "uploads/berkas/";
    if(!is_dir($folder)) mkdir($folder, 0777, true);

    $ktp_name  = "KTP_" . time() . "_" . $_FILES['ktp']['name'];
    $stnk_name = "STNK_" . time() . "_" . $_FILES['stnk']['name'];

    move_uploaded_file($_FILES['ktp']['tmp_name'], $folder . $ktp_name);
    move_uploaded_file($_FILES['stnk']['tmp_name'], $folder . $stnk_name);

    // Simpan ke tabel pengajuan
    $query = "INSERT INTO pengajuan (nama_penyewa, no_wa, alamat, id_unit, durasi, foto_ktp, foto_stnk, tgl_pengajuan, status_pengajuan) 
              VALUES ('$nama', '$wa', '$alamat', '$id_unit', '$durasi', '$ktp_name', '$stnk_name', '$tgl', 'Pending')";

    if(mysqli_query($koneksi, $query)){
        // Update status unit menjadi 'Disewa' agar tidak dipilih orang lain sementara
        mysqli_query($koneksi, "UPDATE units SET status = 'Disewa' WHERE id_unit = '$id_unit'");
        
        echo "<script>alert('Pengajuan Berhasil! Tunggu konfirmasi kami via WhatsApp.'); window.location='index.php';</script>";
    }
}
?>