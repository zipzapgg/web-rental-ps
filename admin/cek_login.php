<?php
session_start();
include '../config/koneksi.php';

$user = $_POST['user'];
$pass = $_POST['pass'];

$login = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$user' AND password='$pass'");
$cek = mysqli_num_rows($login);

if($cek > 0){
    $_SESSION['status'] = "login";
    $_SESSION['user'] = $user;
    header("location:index.php");
} else {
    echo "<script>alert('Login Gagal! Username atau Password salah.'); window.location='login.php';</script>";
}
?>