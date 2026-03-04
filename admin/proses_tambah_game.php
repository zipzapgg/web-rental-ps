<?php
include '../config/koneksi.php'; // Mengambil koneksi ke db_violet_ps

if (isset($_POST['judul'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    
    // 1. Logika Pengolahan Foto
    $foto_nama = $_FILES['foto']['name'];
    $foto_tmp  = $_FILES['foto']['tmp_name'];
    $ekstensi  = pathinfo($foto_nama, PATHINFO_EXTENSION);
    
    // Keamanan: Ganti nama file agar unik berdasarkan waktu
    $nama_file_baru = "game_" . time() . "." . $ekstensi;
    $folder_tujuan  = "../uploads/games/" . $nama_file_baru;

    if (move_uploaded_file($foto_tmp, $folder_tujuan)) {
        
        // 2. Simpan ke Tabel Master games
        $sql_game = "INSERT INTO games (judul_game, foto_game) VALUES ('$judul', '$nama_file_baru')";
        mysqli_query($koneksi, $sql_game);
        
        // Ambil ID Game yang baru saja dibuat untuk relasi
        $id_game_baru = mysqli_insert_id($koneksi);

        // 3. Simpan ke Tabel unit_games (Logika Centang Banyak)
        if (!empty($_POST['unit_dipilih'])) {
            foreach ($_POST['unit_dipilih'] as $id_unit) {
                // Masukkan relasi antara unit dan game ke database
                mysqli_query($koneksi, "INSERT INTO unit_games (id_unit, id_game) VALUES ('$id_unit', '$id_game_baru')");
            }
        }

        echo "<script>alert('Berhasil! Game dan relasi unit telah disimpan.'); window.location='master_game.php';</script>";
    } else {
        echo "<script>alert('Gagal mengunggah foto. Periksa folder uploads/games!'); window.history.back();</script>";
    }
}
?>